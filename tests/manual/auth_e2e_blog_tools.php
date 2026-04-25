<?php

declare(strict_types=1);

final class AuthE2EBlogToolsRunner
{
    private string $baseUrl = 'http://127.0.0.1:8000';
    private string $cookieFile;

    public function run(): int
    {
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'travelxp_auth_');
        if ($this->cookieFile === false) {
            $this->out(['error' => 'Unable to create cookie file.']);

            return 1;
        }

        try {
            $health = $this->request('GET', '/blogs');
            if (!in_array($health['status'], [200, 302], true)) {
                $this->out([
                    'error' => 'Application is not reachable at /blogs.',
                    'status' => $health['status'],
                ]);

                return 1;
            }

            $stamp = (new DateTimeImmutable())->format('YmdHis');
            $email = sprintf('e2e+%s@example.com', $stamp);
            $username = sprintf('e2e_%s', $stamp);
            $password = 'Travelxp123';

            $this->createTemporaryUser($username, $email, $password);

            $loginPage = $this->request('GET', '/login');
            $loginToken = $this->extractInputValueByName($loginPage['body'], '_csrf_token');
            if ($loginToken === null) {
                $this->out(['error' => 'Failed to parse login CSRF token.']);

                return 1;
            }

            $this->request('POST', '/login', [
                '_username' => $email,
                '_password' => $password,
                '_csrf_token' => $loginToken,
            ]);

            $newBlogPage = $this->request('GET', '/blogs/new');
            if (str_contains($newBlogPage['body'], 'Sign in to TravelXP')) {
                $this->out(['error' => 'Login failed, still on sign-in view.']);

                return 1;
            }

            $blogGrammarToken = $this->extractDataAttributeByUrl($newBlogPage['body'], '/blogs/tools/grammar', 'data-grammar-token');
            if ($blogGrammarToken === null) {
                $this->out(['error' => 'Missing blog grammar token in blog form.']);

                return 1;
            }

            $blogId = $this->extractFirstId($this->request('GET', '/blogs')['body'], '~href="/blogs/(\d+)"~');
            if ($blogId === null) {
                $blogToken = $this->extractInputValueByName($newBlogPage['body'], 'blog[_token]');
                if ($blogToken === null) {
                    $this->out(['error' => 'Failed to parse blog form token.']);

                    return 1;
                }

                $created = $this->request('POST', '/blogs/new', [
                    'blog[title]' => 'E2E Travel Blog',
                    'blog[content]' => 'This is a test blog content for grammar and summarize verification. It includes multiple sentences to summarize clearly.',
                    'blog[imageUrl]' => '',
                    'blog[_token]' => $blogToken,
                ]);
                $blogId = $this->extractFirstId($created['body'], '~href="/blogs/(\d+)"~');

                if ($blogId === null) {
                    $this->out(['error' => 'Failed to create/find blog id.']);

                    return 1;
                }
            }

            $blogShow = $this->request('GET', '/blogs/' . $blogId);
            $blogTranslateToken = $this->extractDataAttributeByUrl($blogShow['body'], sprintf('/blogs/%d/translate', $blogId), 'data-translate-token');
            $blogAiToken = $this->extractDataAttributeByUrl($blogShow['body'], sprintf('/blogs/%d/ai/summarize', $blogId), 'data-ai-summarize-token');

            if ($blogGrammarToken === null || $blogTranslateToken === null || $blogAiToken === null) {
                $this->out([
                    'error' => 'Missing one or more blog tool tokens in rendered HTML.',
                    'blogGrammarToken' => $blogGrammarToken !== null,
                    'blogTranslateToken' => $blogTranslateToken !== null,
                    'blogAiToken' => $blogAiToken !== null,
                ]);

                return 1;
            }

            $commentId = $this->extractFirstId($blogShow['body'], '~data-translate-url="/comments/(\d+)/translate"~');
            if ($commentId === null) {
                $commentToken = $this->extractInputValueByName($blogShow['body'], 'comment[_token]');
                if ($commentToken === null) {
                    $this->out(['error' => 'Missing comment form token.']);

                    return 1;
                }

                $this->request('POST', '/blogs/' . $blogId . '/comments/new', [
                    'comment[content]' => 'Great article and very informative for travelers.',
                    'comment[_token]' => $commentToken,
                ]);

                $blogShow = $this->request('GET', '/blogs/' . $blogId);
                $commentId = $this->extractFirstId($blogShow['body'], '~data-translate-url="/comments/(\d+)/translate"~');
            }

            if ($commentId === null) {
                $this->out(['error' => 'Could not find/create comment for translation test.']);

                return 1;
            }

            $commentTranslateToken = $this->extractTokenForCommentTranslate($blogShow['body'], $commentId);
            $commentGrammarToken = $this->extractDataAttributeByUrl($blogShow['body'], '/comments/tools/grammar', 'data-grammar-token');

            if ($commentTranslateToken === null || $commentGrammarToken === null) {
                $this->out([
                    'error' => 'Missing one or more comment tool tokens in rendered HTML.',
                    'commentTranslateToken' => $commentTranslateToken !== null,
                    'commentGrammarToken' => $commentGrammarToken !== null,
                ]);

                return 1;
            }

            $blogGrammar = $this->request('POST', '/blogs/tools/grammar', [
                'text' => 'This are bad grammar',
                'language' => 'en-US',
                '_token' => $blogGrammarToken,
            ]);

            $commentGrammar = $this->request('POST', '/comments/tools/grammar', [
                'text' => 'He go to airport yesterday',
                'language' => 'en-US',
                '_token' => $commentGrammarToken,
            ]);

            $blogTranslate = $this->request('POST', '/blogs/' . $blogId . '/translate', [
                'target' => 'fr',
                '_token' => $blogTranslateToken,
            ]);

            $commentTranslate = $this->request('POST', '/comments/' . $commentId . '/translate', [
                'target' => 'fr',
                '_token' => $commentTranslateToken,
            ]);

            $ai = $this->request('POST', '/blogs/' . $blogId . '/ai/summarize', [
                '_token' => $blogAiToken,
            ]);

            $result = [
                'ok' => true,
                'user_email' => $email,
                'blog_id' => $blogId,
                'comment_id' => $commentId,
                'statuses' => [
                    'blog_grammar' => $blogGrammar['status'],
                    'comment_grammar' => $commentGrammar['status'],
                    'blog_translate' => $blogTranslate['status'],
                    'comment_translate' => $commentTranslate['status'],
                    'ai_summarize' => $ai['status'],
                ],
                'samples' => [
                    'blog_grammar' => $this->truncate($blogGrammar['body']),
                    'blog_translate' => $this->truncate($blogTranslate['body']),
                    'comment_translate' => $this->truncate($commentTranslate['body']),
                    'ai_summarize' => $this->truncate($ai['body']),
                ],
            ];

            $this->out($result);

            return 0;
        } catch (Throwable $e) {
            $this->out([
                'ok' => false,
                'error' => $e->getMessage(),
            ]);

            return 1;
        } finally {
            @unlink($this->cookieFile);
        }
    }

    /**
     * @param array<string, string>|null $form
     * @return array{status:int,body:string}
     */
    private function request(string $method, string $path, ?array $form = null): array
    {
        $ch = curl_init();
        if ($ch === false) {
            throw new RuntimeException('Unable to initialize cURL.');
        }

        $url = rtrim($this->baseUrl, '/') . $path;
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR => $this->cookieFile,
            CURLOPT_COOKIEFILE => $this->cookieFile,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
        ];

        if ($form !== null) {
            $this->applySameOriginCsrfIfNeeded($form);

            $options[CURLOPT_HTTPHEADER] = ['Content-Type: application/x-www-form-urlencoded'];
            $options[CURLOPT_POSTFIELDS] = http_build_query($form);
        }

        curl_setopt_array($ch, $options);
        $body = curl_exec($ch);
        if ($body === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('HTTP request failed: ' . $error);
        }

        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $status,
            'body' => $body,
        ];
    }

    private function extractInputValueByName(string $html, string $name): ?string
    {
        $pattern = '~name="' . preg_quote($name, '~') . '"[^>]*value="([^"]*)"~';
        if (preg_match($pattern, $html, $m) === 1) {
            return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);
        }

        $patternAlt = '~value="([^"]*)"[^>]*name="' . preg_quote($name, '~') . '"~';
        if (preg_match($patternAlt, $html, $m) === 1) {
            return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);
        }

        return null;
    }

    /**
     * @param array<string, string> $form
     */
    private function applySameOriginCsrfIfNeeded(array &$form): void
    {
        foreach ($form as $key => $value) {
            if (!$this->isCsrfFieldName($key)) {
                continue;
            }

            // Same-origin CSRF uses a short token name (e.g. "csrf-token") that browser JS
            // transforms into a random token value and a paired cookie.
            if (!preg_match('/^[-_a-zA-Z0-9]{4,22}$/', $value)) {
                continue;
            }

            $randomToken = rtrim(strtr(base64_encode(random_bytes(18)), '+/', '+/'), '=');
            if (!preg_match('/^[-_\/+a-zA-Z0-9]{24,}$/', $randomToken)) {
                $randomToken = 'A1b2C3d4E5f6G7h8I9j0K1l2';
            }

            $cookieName = $value . '_' . $randomToken;
            $this->appendCookie($cookieName, $value);
            $form[$key] = $randomToken;
        }
    }

    private function isCsrfFieldName(string $name): bool
    {
        return $name === '_csrf_token' || $name === '_token' || str_ends_with($name, '[_token]');
    }

    private function appendCookie(string $name, string $value): void
    {
        $domain = '127.0.0.1';
        $tail = implode("\t", [
            $domain,
            'FALSE',
            '/',
            'FALSE',
            '0',
            $name,
            $value,
        ]);

        file_put_contents($this->cookieFile, $tail . PHP_EOL, FILE_APPEND);
    }

    private function extractInputValueBySuffix(string $html, string $suffix): ?string
    {
        $pattern = '~name="([^"]*' . preg_quote($suffix, '~') . ')"\s+value="([^"]*)"~';
        if (preg_match($pattern, $html, $m) === 1) {
            return html_entity_decode($m[2], ENT_QUOTES | ENT_HTML5);
        }

        $patternAlt = '~value="([^"]*)"\s+name="([^"]*' . preg_quote($suffix, '~') . ')"~';
        if (preg_match($patternAlt, $html, $m) === 1) {
            return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);
        }

        return null;
    }

    private function createTemporaryUser(string $username, string $email, string $plainPassword): void
    {
        $pdo = new PDO(
            'mysql:host=127.0.0.1;port=3306;dbname=travelxp_symfony;charset=utf8mb4',
            'root',
            '',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $hashed = password_hash($plainPassword, PASSWORD_BCRYPT);
        if ($hashed === false) {
            throw new RuntimeException('Failed to hash temporary password.');
        }

        $stmt = $pdo->prepare('INSERT INTO users (username, email, roles, password, birthday, bio, profile_image, balance, face_registered, totp_enabled, totp_secret, created_at, updated_at, xp, level, streak) VALUES (:username, :email, :roles, :password, :birthday, :bio, :profileImage, :balance, :faceRegistered, :totpEnabled, :totpSecret, :createdAt, :updatedAt, :xp, :level, :streak)');

        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':roles' => json_encode(['ROLE_USER'], JSON_THROW_ON_ERROR),
            ':password' => $hashed,
            ':birthday' => '1999-01-01',
            ':bio' => 'E2E test account',
            ':profileImage' => null,
            ':balance' => '0.00',
            ':faceRegistered' => 0,
            ':totpEnabled' => 0,
            ':totpSecret' => null,
            ':createdAt' => $now,
            ':updatedAt' => $now,
            ':xp' => 0,
            ':level' => 1,
            ':streak' => 0,
        ]);
    }

    private function extractDataAttributeByUrl(string $html, string $url, string $attribute): ?string
    {
        $pattern = '~' . preg_quote('data-translate-url="' . $url . '"', '~') . '[^>]*' . preg_quote($attribute, '~') . '="([^"]+)"~';
        if (preg_match($pattern, $html, $m) === 1) {
            return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);
        }

        $pattern = '~' . preg_quote('data-grammar-url="' . $url . '"', '~') . '[^>]*' . preg_quote($attribute, '~') . '="([^"]+)"~';
        if (preg_match($pattern, $html, $m) === 1) {
            return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);
        }

        $pattern = '~' . preg_quote('data-ai-summarize-url="' . $url . '"', '~') . '[^>]*' . preg_quote($attribute, '~') . '="([^"]+)"~';
        if (preg_match($pattern, $html, $m) === 1) {
            return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);
        }

        return null;
    }

    private function extractDataAttribute(string $html, string $anchor, string $attribute): ?string
    {
        $pattern = '~' . preg_quote($anchor, '~') . '[^>]*' . preg_quote($attribute, '~') . '="([^"]+)"~';
        if (preg_match($pattern, $html, $m) === 1) {
            return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);
        }

        return null;
    }

    private function extractTokenForCommentTranslate(string $html, int $commentId): ?string
    {
        $pattern = '~data-translate-url="/comments/' . $commentId . '/translate"[^>]*data-translate-token="([^"]+)"~';
        if (preg_match($pattern, $html, $m) === 1) {
            return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);
        }

        return null;
    }

    private function extractFirstId(string $html, string $pattern): ?int
    {
        if (preg_match($pattern, $html, $m) === 1) {
            return (int) $m[1];
        }

        return null;
    }

    private function truncate(string $value, int $max = 300): string
    {
        $flat = trim(preg_replace('/\s+/', ' ', $value) ?? '');
        if (mb_strlen($flat) <= $max) {
            return $flat;
        }

        return mb_substr($flat, 0, $max) . '...';
    }

    /**
     * @param array<string,mixed> $payload
     */
    private function out(array $payload): void
    {
        echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    }
}

$runner = new AuthE2EBlogToolsRunner();
exit($runner->run());
