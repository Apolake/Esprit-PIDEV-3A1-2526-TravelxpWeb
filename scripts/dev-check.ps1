param(
    [string]$HostAddress = "127.0.0.1",
    [int]$AppPort = 8001,
    [int]$OllamaPort = 11434
)

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

function Assert-HttpOk {
    param(
        [Parameter(Mandatory = $true)][string]$Url,
        [int]$TimeoutSec = 10
    )

    $res = Invoke-WebRequest -UseBasicParsing -Uri $Url -TimeoutSec $TimeoutSec
    if ($res.StatusCode -lt 200 -or $res.StatusCode -ge 400) {
        throw "URL failed: $Url (status $($res.StatusCode))"
    }
}

function Assert-Contains {
    param(
        [Parameter(Mandatory = $true)][string]$Path,
        [Parameter(Mandatory = $true)][string]$Pattern,
        [Parameter(Mandatory = $true)][string]$Reason
    )

    $hit = Select-String -Path $Path -Pattern $Pattern -Quiet
    if (-not $hit) {
        throw "Check failed: $Reason ($Path does not match '$Pattern')"
    }
}

$projectRoot = Split-Path -Parent $PSScriptRoot
Set-Location $projectRoot

Write-Host "Running service checks..."
Assert-HttpOk -Url "http://$HostAddress`:$OllamaPort/api/tags"
Assert-HttpOk -Url "http://$HostAddress`:$AppPort/login"
Assert-HttpOk -Url "http://$HostAddress`:$AppPort/trips/browse"

Write-Host "Running Symfony checks..."
php bin/console lint:twig templates/trip/index.html.twig templates/trip/show.html.twig | Out-Null
php bin/console lint:container | Out-Null
php bin/console debug:scheduler | Out-Null

Write-Host "Running frontend wiring checks..."
Assert-Contains -Path "assets/stimulus_bootstrap.js" -Pattern "trip-map" -Reason "Trip map controller must be registered"
Assert-Contains -Path "assets/stimulus_bootstrap.js" -Pattern "trip-card-ai-drawer" -Reason "Trip AI drawer controller must be registered"
Assert-Contains -Path "templates/trip/index.html.twig" -Pattern "trip-card-ai-drawer#openFromCard" -Reason "AI button click action must be wired"
Assert-Contains -Path "templates/trip/show.html.twig" -Pattern 'data-controller="trip-map"' -Reason "Trip map controller must be attached in trip show template"

Write-Host "Running AI provider smoke test..."
$smokeScript = @'
<?php
require __DIR__ . '/vendor/autoload.php';
use App\Service\TripAiAssistantService;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\HttpClient;

(new Dotenv())->bootEnv(__DIR__.'/.env');
$service = new TripAiAssistantService(HttpClient::create());
$ctx = [
  'tripId' => 99,
  'tripName' => 'Smoke Test Trip',
  'origin' => 'Lagos',
  'destination' => 'Cairo',
  'dateRange' => '2026-07-01 -> 2026-07-03',
  'durationDays' => 3,
  'budgetAmount' => 400,
  'currency' => 'USD',
  'activities' => [],
  'weatherWarnings' => [],
  'userJoinedTrip' => true,
];
$a = $service->answerUserFreeMessageLive($ctx, 'hello', []);
$b = $service->answerUserFreeMessageLive($ctx, 'is 400 USD enough?', []);
if ($a === null || $b === null) {
    fwrite(STDERR, 'AI smoke test failed: ' . ($service->getLastProviderError() ?? 'unknown') . PHP_EOL);
    exit(2);
}
echo "AI_SMOKE_OK\n";
'@
$smokeScript | php | Out-Null

Write-Host "All checks passed."
