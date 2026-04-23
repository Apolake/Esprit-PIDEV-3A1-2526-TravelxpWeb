param(
    [string]$HostAddress = "127.0.0.1",
    [int]$AppPort = 8001,
    [int]$OllamaPort = 11434,
    [switch]$PullModelIfMissing,
    [switch]$RunChecks
)

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

function Test-HttpOk {
    param(
        [Parameter(Mandatory = $true)][string]$Url,
        [int]$TimeoutSec = 8
    )

    try {
        $res = Invoke-WebRequest -UseBasicParsing -Uri $Url -TimeoutSec $TimeoutSec
        return ($res.StatusCode -ge 200 -and $res.StatusCode -lt 400)
    } catch {
        return $false
    }
}

function Wait-HttpOk {
    param(
        [Parameter(Mandatory = $true)][string]$Url,
        [int]$TimeoutSec = 60
    )

    $deadline = (Get-Date).AddSeconds($TimeoutSec)
    while ((Get-Date) -lt $deadline) {
        if (Test-HttpOk -Url $Url -TimeoutSec 5) {
            return $true
        }
        Start-Sleep -Seconds 1
    }

    return $false
}

function Start-OllamaIfNeeded {
    param(
        [string]$HostAddress,
        [int]$OllamaPort,
        [switch]$PullModelIfMissing
    )

    $ollamaExe = Join-Path $env:LOCALAPPDATA "Programs\Ollama\ollama.exe"
    if (-not (Test-Path -LiteralPath $ollamaExe)) {
        throw "Ollama executable not found at $ollamaExe"
    }

    $ollamaUrl = "http://$HostAddress`:$OllamaPort/api/tags"
    if (-not (Test-HttpOk -Url $ollamaUrl -TimeoutSec 5)) {
        Write-Host "Starting Ollama service..."
        $proc = Start-Process -FilePath $ollamaExe -ArgumentList "serve" -WindowStyle Hidden -PassThru
        Write-Host "Ollama PID: $($proc.Id)"
    } else {
        Write-Host "Ollama is already running."
    }

    if (-not (Wait-HttpOk -Url $ollamaUrl -TimeoutSec 45)) {
        throw "Ollama API is not reachable at $ollamaUrl"
    }

    $model = if ($env:OLLAMA_MODEL -and $env:OLLAMA_MODEL.Trim() -ne "") { $env:OLLAMA_MODEL.Trim() } else { "llama3.2" }
    $listOutput = & $ollamaExe list
    $modelPattern = [regex]::Escape($model) + "(:|`$)"
    if ($PullModelIfMissing -and ($listOutput -notmatch $modelPattern)) {
        Write-Host "Model '$model' not found. Pulling..."
        & $ollamaExe pull $model
    }

    Write-Host "Ollama ready."
}

function Start-AppIfNeeded {
    param(
        [string]$HostAddress,
        [int]$AppPort,
        [string]$ProjectRoot
    )

    $loginUrl = "http://$HostAddress`:$AppPort/login"
    if (Test-HttpOk -Url $loginUrl -TimeoutSec 5) {
        Write-Host "PHP app server is already running."
        return
    }

    Write-Host "Starting PHP app server..."
    $args = @("-d", "max_execution_time=0", "-S", "$HostAddress`:$AppPort", "-t", "public", "public/index.php")
    $proc = Start-Process -FilePath "php" -ArgumentList $args -WorkingDirectory $ProjectRoot -WindowStyle Hidden -PassThru
    Write-Host "PHP PID: $($proc.Id)"

    if (-not (Wait-HttpOk -Url $loginUrl -TimeoutSec 60)) {
        throw "PHP app server did not start correctly on http://$HostAddress`:$AppPort"
    }

    Write-Host "PHP app server ready."
}

function Start-SchedulerIfNeeded {
    param(
        [string]$ProjectRoot
    )

    $existing = Get-CimInstance Win32_Process | Where-Object {
        $_.Name -match '^php(\.exe)?$' -and
        $_.CommandLine -match 'messenger:consume' -and
        $_.CommandLine -match 'scheduler_default'
    } | Select-Object -First 1

    if ($null -ne $existing) {
        Write-Host "Scheduler worker is already running."
        return
    }

    Write-Host "Starting scheduler worker..."
    $args = @(
        "bin/console",
        "messenger:consume",
        "scheduler_default",
        "--time-limit=0",
        "--memory-limit=256M",
        "--no-interaction"
    )
    $proc = Start-Process -FilePath "php" -ArgumentList $args -WorkingDirectory $ProjectRoot -WindowStyle Hidden -PassThru
    Start-Sleep -Seconds 2

    if ($proc.HasExited) {
        throw "Scheduler worker exited early. Start it manually with: php bin/console messenger:consume scheduler_default -vv"
    }

    Write-Host "Scheduler worker PID: $($proc.Id)"
}

$projectRoot = Split-Path -Parent $PSScriptRoot
Set-Location $projectRoot

Write-Host "Project root: $projectRoot"
Start-OllamaIfNeeded -HostAddress $HostAddress -OllamaPort $OllamaPort -PullModelIfMissing:$PullModelIfMissing
Start-AppIfNeeded -HostAddress $HostAddress -AppPort $AppPort -ProjectRoot $projectRoot
Start-SchedulerIfNeeded -ProjectRoot $projectRoot

if ($RunChecks) {
    & "$PSScriptRoot\dev-check.ps1" -HostAddress $HostAddress -AppPort $AppPort -OllamaPort $OllamaPort
}

Write-Host "All services are up."
