$ErrorActionPreference = 'Stop'
$base = 'http://127.0.0.1:8000'
$testEmail = 'autotest_blog_tools_20260419@example.com'
$testPassword = 'Test1234x'

function Invoke-NoRedirect {
    param(
        [string]$Uri,
        [string]$Method = 'GET',
        [hashtable]$Body = $null,
        [Microsoft.PowerShell.Commands.WebRequestSession]$Session
    )

    try {
        if ($null -ne $Body) {
            return Invoke-WebRequest -Uri $Uri -Method $Method -Body $Body -WebSession $Session -MaximumRedirection 0
        }

        return Invoke-WebRequest -Uri $Uri -Method $Method -WebSession $Session -MaximumRedirection 0
    } catch {
        if ($_.Exception.Response) {
            return $_.Exception.Response
        }

        throw
    }
}

$ping = Invoke-WebRequest -Uri "$base/blogs" -Method Get
Write-Output ("Ping /blogs => " + [int]$ping.StatusCode)

$hash = php -r "echo password_hash('$testPassword', PASSWORD_BCRYPT);"
$hash = $hash.Trim()
$sql = "INSERT INTO users (username,email,roles,password,birthday,bio,profile_image,balance,xp,level,streak,face_registered,totp_enabled,totp_secret,created_at,updated_at) VALUES ('autotest_blog_tools_20260419','$testEmail','[\"ROLE_USER\"]','$hash','1998-01-01','Automated test account',NULL,'0.00',0,1,0,0,0,NULL,NOW(),NOW()) ON DUPLICATE KEY UPDATE password=VALUES(password), updated_at=NOW();"
php bin/console doctrine:query:sql $sql | Out-Null
Write-Output 'User prepared in DB.'

$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$loginPage = Invoke-WebRequest -Uri "$base/login" -WebSession $session
$loginCsrf = [regex]::Match($loginPage.Content, 'name="_csrf_token"\s+value="([^"]+)"').Groups[1].Value
if ([string]::IsNullOrWhiteSpace($loginCsrf)) {
    throw 'Failed to parse login CSRF token.'
}

$loginResponse = Invoke-NoRedirect -Uri "$base/login" -Method 'POST' -Body @{ _username = $testEmail; _password = $testPassword; _csrf_token = $loginCsrf } -Session $session
$loginStatus = [int]$loginResponse.StatusCode
Write-Output ("Login POST status => " + $loginStatus)

$profileCheck = Invoke-NoRedirect -Uri "$base/profile" -Method 'GET' -Session $session
$profileLocation = ''
if ($profileCheck.Headers['Location']) {
    $profileLocation = [string]$profileCheck.Headers['Location']
}
if (($profileCheck.StatusCode -eq 302) -and $profileLocation.Contains('/login')) {
    throw 'Authentication failed: profile redirects to login.'
}
Write-Output ("Profile access status => " + [int]$profileCheck.StatusCode)

$newBlogPage = Invoke-WebRequest -Uri "$base/blogs/new" -WebSession $session
$blogToken = [regex]::Match($newBlogPage.Content, 'name="blog\[_token\]"\s+value="([^"]+)"').Groups[1].Value
$blogGrammarToken = [regex]::Match($newBlogPage.Content, 'data-grammar-token="([^"]+)"').Groups[1].Value
if ([string]::IsNullOrWhiteSpace($blogToken)) {
    throw 'Failed to parse blog form CSRF token.'
}
if ([string]::IsNullOrWhiteSpace($blogGrammarToken)) {
    throw 'Failed to parse blog grammar token.'
}

$blogTitle = 'Automated Blog Flow 2026-04-19'
$blogContent = 'This are a demo travel blog content with grammar mistake. I go to Tunis and enjoy the old city very much. The weather were amazing and food was excellent.'
$createBlog = Invoke-NoRedirect -Uri "$base/blogs/new" -Method 'POST' -Body @{ 'blog[title]' = $blogTitle; 'blog[content]' = $blogContent; 'blog[imageUrl]' = ''; 'blog[_token]' = $blogToken } -Session $session
$blogLocation = [string]$createBlog.Headers['Location']
if ([string]::IsNullOrWhiteSpace($blogLocation)) {
    throw 'Blog creation did not return redirect location.'
}
if (-not $blogLocation.StartsWith('http')) {
    $blogLocation = $base + $blogLocation
}
Write-Output ("Blog created redirect => " + $blogLocation)

$blogPage = Invoke-WebRequest -Uri $blogLocation -WebSession $session
$blogPageHtml = $blogPage.Content
$hasSummaryPanel = $blogPageHtml.Contains('id="blog-summary-output"')
$hasTranslationPanel = $blogPageHtml.Contains('id="translation-panel"') -and $blogPageHtml.Contains('data-translate-run="true"')
if (-not $hasSummaryPanel) {
    throw 'AI summary panel not present in blog show HTML.'
}
if (-not $hasTranslationPanel) {
    throw 'Translation panel UI hooks not present in blog show HTML.'
}
Write-Output 'UI hooks => summary panel and translation panel found.'

$blogTranslateUrl = [regex]::Match($blogPageHtml, 'data-translate-url="([^"]*/blogs/\d+/translate)"').Groups[1].Value
$blogTranslateToken = [regex]::Match($blogPageHtml, 'data-translate-token="([^"]+)"').Groups[1].Value
$aiUrl = [regex]::Match($blogPageHtml, 'data-ai-summarize-url="([^"]*/blogs/\d+/ai/summarize)"').Groups[1].Value
$aiToken = [regex]::Match($blogPageHtml, 'data-ai-summarize-token="([^"]+)"').Groups[1].Value
$commentAction = [regex]::Match($blogPageHtml, 'action="([^"]*/blogs/\d+/comments/new)"').Groups[1].Value
$commentFormToken = [regex]::Match($blogPageHtml, 'name="comment\[_token\]"\s+value="([^"]+)"').Groups[1].Value
$commentGrammarToken = [regex]::Match($blogPageHtml, 'data-grammar-url="/comments/tools/grammar"[^>]*data-grammar-token="([^"]+)"').Groups[1].Value

if ([string]::IsNullOrWhiteSpace($blogTranslateUrl) -or [string]::IsNullOrWhiteSpace($blogTranslateToken)) {
    throw 'Failed to parse blog translate URL/token.'
}
if ([string]::IsNullOrWhiteSpace($aiUrl) -or [string]::IsNullOrWhiteSpace($aiToken)) {
    throw 'Failed to parse AI summarize URL/token.'
}
if ([string]::IsNullOrWhiteSpace($commentAction) -or [string]::IsNullOrWhiteSpace($commentFormToken)) {
    throw 'Failed to parse comment create action/token.'
}
if ([string]::IsNullOrWhiteSpace($commentGrammarToken)) {
    throw 'Failed to parse comment grammar token.'
}

$grammarBlogResp = Invoke-WebRequest -Uri ($base + '/blogs/tools/grammar') -Method 'POST' -Body @{ text = 'This are amazing place for traveler.'; language = 'en-US'; _token = $blogGrammarToken } -WebSession $session
$grammarBlogJson = $grammarBlogResp.Content | ConvertFrom-Json
Write-Output ("Blog grammar => status " + [int]$grammarBlogResp.StatusCode + ', changed=' + [string]$grammarBlogJson.changed + ', message=' + [string]$grammarBlogJson.message)

$translateBlogResp = Invoke-WebRequest -Uri ($base + $blogTranslateUrl) -Method 'POST' -Body @{ target = 'fr'; _token = $blogTranslateToken } -WebSession $session
$translateBlogJson = $translateBlogResp.Content | ConvertFrom-Json
Write-Output ("Blog translate => status " + [int]$translateBlogResp.StatusCode + ', provider=' + [string]$translateBlogJson.provider + ', hasText=' + [string](-not [string]::IsNullOrWhiteSpace([string]$translateBlogJson.translatedText)))

$aiResp = Invoke-WebRequest -Uri ($base + $aiUrl) -Method 'POST' -Body @{ _token = $aiToken } -WebSession $session
$aiJson = $aiResp.Content | ConvertFrom-Json
Write-Output ("AI summarize => status " + [int]$aiResp.StatusCode + ', source=' + [string]$aiJson.source + ', summaryLen=' + [string]([string]$aiJson.summary).Length)

$commentContent = 'This are comment grammar test and it should be corrected if possible.'
$createCommentResp = Invoke-NoRedirect -Uri ($base + $commentAction) -Method 'POST' -Body @{ 'comment[content]' = $commentContent; 'comment[_token]' = $commentFormToken } -Session $session
Write-Output ("Comment create status => " + [int]$createCommentResp.StatusCode)

$blogPage2 = Invoke-WebRequest -Uri $blogLocation -WebSession $session
$blogPage2Html = $blogPage2.Content
$commentTranslateMatch = [regex]::Matches($blogPage2Html, 'data-translate-url="([^"]*/comments/\d+/translate)"[^>]*data-translate-original="[^"]*"[^>]*data-translate-target="[^"]*"[^>]*data-translate-token="([^"]+)"')
if ($commentTranslateMatch.Count -lt 1) {
    throw 'No comment translate button found after creating comment.'
}
$commentTranslateUrl = $commentTranslateMatch[0].Groups[1].Value
$commentTranslateToken = $commentTranslateMatch[0].Groups[2].Value

$grammarCommentResp = Invoke-WebRequest -Uri ($base + '/comments/tools/grammar') -Method 'POST' -Body @{ text = 'I are happy with this trip.'; language = 'en-US'; _token = $commentGrammarToken } -WebSession $session
$grammarCommentJson = $grammarCommentResp.Content | ConvertFrom-Json
Write-Output ("Comment grammar => status " + [int]$grammarCommentResp.StatusCode + ', changed=' + [string]$grammarCommentJson.changed + ', message=' + [string]$grammarCommentJson.message)

$translateCommentResp = Invoke-WebRequest -Uri ($base + $commentTranslateUrl) -Method 'POST' -Body @{ target = 'fr'; _token = $commentTranslateToken } -WebSession $session
$translateCommentJson = $translateCommentResp.Content | ConvertFrom-Json
Write-Output ("Comment translate => status " + [int]$translateCommentResp.StatusCode + ', provider=' + [string]$translateCommentJson.provider + ', hasText=' + [string](-not [string]::IsNullOrWhiteSpace([string]$translateCommentJson.translatedText)))

Write-Output 'Authenticated browser-level flow checks completed.'
