# PHPStan Fixes Log — TravelXP Audit

> **Total Errors Found:** 26  
> **Total Errors Fixed:** 26  
> **PHPStan Level:** 6 (strict)  
> **Result:** ✅ 0 errors remaining

---

## File 1: `src/Entity/Blog.php`

### Error 1 — Line 42
**PHPStan Rule:** `missingType.generics`  
**Error Message:**
```
Property App\Entity\Blog::$comments with generic interface
Doctrine\Common\Collections\Collection does not specify its types: TKey, T
```

**Cause:** The `$comments` property uses `Collection` without telling PHPStan what types it holds. Doctrine collections are generic — PHPStan requires explicit `@var` annotations to verify type safety on collection operations.

**BEFORE:**
```php
#[ORM\OneToMany(mappedBy: 'blog', targetEntity: Comment::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
#[ORM\OrderBy(['createdAt' => 'DESC'])]
private Collection $comments;
```

**AFTER:**
```php
/**
 * @var Collection<int, Comment>
 */
#[ORM\OneToMany(mappedBy: 'blog', targetEntity: Comment::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
#[ORM\OrderBy(['createdAt' => 'DESC'])]
private Collection $comments;
```

---

### Error 2 — Line 46
**PHPStan Rule:** `missingType.generics`  
**Error Message:**
```
Property App\Entity\Blog::$likedByUsers with generic interface
Doctrine\Common\Collections\Collection does not specify its types: TKey, T
```

**Cause:** Same as Error 1 — the `$likedByUsers` ManyToMany collection lacks generic type annotations.

**BEFORE:**
```php
#[ORM\ManyToMany(targetEntity: User::class)]
#[ORM\JoinTable(name: 'blog_likes')]
private Collection $likedByUsers;
```

**AFTER:**
```php
/**
 * @var Collection<int, User>
 */
#[ORM\ManyToMany(targetEntity: User::class)]
#[ORM\JoinTable(name: 'blog_likes')]
private Collection $likedByUsers;
```

---

### Error 3 — Line 50
**PHPStan Rule:** `missingType.generics`  
**Error Message:**
```
Property App\Entity\Blog::$dislikedByUsers with generic interface
Doctrine\Common\Collections\Collection does not specify its types: TKey, T
```

**Cause:** Same pattern — the `$dislikedByUsers` ManyToMany collection lacks generic types.

**BEFORE:**
```php
#[ORM\ManyToMany(targetEntity: User::class)]
#[ORM\JoinTable(name: 'blog_dislikes')]
private Collection $dislikedByUsers;
```

**AFTER:**
```php
/**
 * @var Collection<int, User>
 */
#[ORM\ManyToMany(targetEntity: User::class)]
#[ORM\JoinTable(name: 'blog_dislikes')]
private Collection $dislikedByUsers;
```

---

## File 2: `src/Entity/Comment.php`

### Error 4 — Line 36
**PHPStan Rule:** `missingType.generics`  
**Error Message:**
```
Property App\Entity\Comment::$likedByUsers with generic interface
Doctrine\Common\Collections\Collection does not specify its types: TKey, T
```

**Cause:** The `$likedByUsers` collection in Comment entity has the same missing generic types issue as in Blog.

**BEFORE:**
```php
#[ORM\ManyToMany(targetEntity: User::class)]
#[ORM\JoinTable(name: 'blog_comment_likes')]
private Collection $likedByUsers;
```

**AFTER:**
```php
/**
 * @var Collection<int, User>
 */
#[ORM\ManyToMany(targetEntity: User::class)]
#[ORM\JoinTable(name: 'blog_comment_likes')]
private Collection $likedByUsers;
```

---

### Error 5 — Line 40
**PHPStan Rule:** `missingType.generics`  
**Error Message:**
```
Property App\Entity\Comment::$dislikedByUsers with generic interface
Doctrine\Common\Collections\Collection does not specify its types: TKey, T
```

**Cause:** Same pattern on the `$dislikedByUsers` collection.

**BEFORE:**
```php
#[ORM\ManyToMany(targetEntity: User::class)]
#[ORM\JoinTable(name: 'blog_comment_dislikes')]
private Collection $dislikedByUsers;
```

**AFTER:**
```php
/**
 * @var Collection<int, User>
 */
#[ORM\ManyToMany(targetEntity: User::class)]
#[ORM\JoinTable(name: 'blog_comment_dislikes')]
private Collection $dislikedByUsers;
```

---

## File 3: `src/Entity/Notification.php`

### Error 6 — Line 38
**PHPStan Rule:** `missingType.iterableValue`  
**Error Message:**
```
Property App\Entity\Notification::$context type has no value type specified in iterable type array.
```

**Cause:** The `$context` property is typed as `?array` but PHPStan requires knowing what the array contains. Since this is a JSON column with arbitrary data, `array<string, mixed>` is the correct specification.

**BEFORE:**
```php
#[ORM\Column(type: 'json', nullable: true)]
private ?array $context = null;
```

**AFTER:**
```php
/**
 * @var array<string, mixed>|null
 */
#[ORM\Column(type: 'json', nullable: true)]
private ?array $context = null;
```

---

### Error 7 — Line 119
**PHPStan Rule:** `missingType.iterableValue`  
**Error Message:**
```
Method App\Entity\Notification::getContext() return type has no value type specified in iterable type array.
```

**Cause:** The getter returns `?array` without specifying value types in PHPDoc.

**BEFORE:**
```php
public function getContext(): ?array
{
```

**AFTER:**
```php
/**
 * @return array<string, mixed>|null
 */
public function getContext(): ?array
{
```

---

### Error 8 — Line 124
**PHPStan Rule:** `missingType.iterableValue`  
**Error Message:**
```
Method App\Entity\Notification::setContext() has parameter $context with no value type specified in iterable type array.
```

**Cause:** The setter parameter `$context` is typed `?array` without value type specification.

**BEFORE:**
```php
public function setContext(?array $context): static
{
```

**AFTER:**
```php
/**
 * @param array<string, mixed>|null $context
 */
public function setContext(?array $context): static
{
```

---

## File 4: `src/Entity/User.php`

### Error 9 — Line 334
**PHPStan Rule:** `function.alreadyNarrowedType`  
**Error Message:**
```
Call to function is_string() with string will always evaluate to true.
```

**Cause:** The `getTotpRecoveryCodes()` method's return type is annotated as `list<string>`, and the property is typed accordingly. PHPStan knows each element is already a `string`, making the `is_string($code)` check inside the filter callback redundant. Fixed by adding a `@var list<mixed>` inline annotation to signal the data may come from JSON (which could technically contain non-strings).

**BEFORE:**
```php
public function getTotpRecoveryCodes(): array
{
    $codes = $this->totpRecoveryCodes ?? [];
    return array_values(array_filter($codes, static fn (mixed $code): bool => is_string($code) && '' !== trim($code)));
}
```

**AFTER:**
```php
public function getTotpRecoveryCodes(): array
{
    /** @var list<mixed> $codes */
    $codes = $this->totpRecoveryCodes ?? [];
    return array_values(array_filter($codes, static fn (mixed $code): bool => is_string($code) && '' !== trim($code)));
}
```

---

### Error 10 — Line 342
**PHPStan Rule:** `function.alreadyNarrowedType`  
**Error Message:**
```
Call to function is_string() with string will always evaluate to true.
```

**Cause:** The `setTotpRecoveryCodes()` method's PHPDoc says `@param list<string>`, so PHPStan knows each element is a `string`. The `is_string()` check is therefore redundant. Fixed by changing the closure parameter type from `mixed` to `string` and removing the `is_string()` guard.

**BEFORE:**
```php
/**
 * @param list<string> $totpRecoveryCodes
 */
public function setTotpRecoveryCodes(array $totpRecoveryCodes): static
{
    $this->totpRecoveryCodes = array_values(array_filter($totpRecoveryCodes, static fn (mixed $code): bool => is_string($code) && '' !== trim($code)));
```

**AFTER:**
```php
/**
 * @param list<string> $totpRecoveryCodes
 */
public function setTotpRecoveryCodes(array $totpRecoveryCodes): static
{
    $this->totpRecoveryCodes = array_values(array_filter($totpRecoveryCodes, static fn (string $code): bool => '' !== trim($code)));
```

---

## File 5: `src/Service/AiSummarizerService.php`

### Error 11 — Line 63
**PHPStan Rule:** `catch.neverThrown`  
**Error Message:**
```
Dead catch - Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface is never thrown in the try block.
```

**Cause:** `TransportExceptionInterface` extends `\Throwable`. When both are caught in a union (`TransportExceptionInterface | \Throwable`), the `\Throwable` catch already covers everything that `TransportExceptionInterface` covers. PHPStan correctly identifies the first type as dead/redundant.

**BEFORE:**
```php
} catch (TransportExceptionInterface | \Throwable) {
    // Fallback to local summarization.
}
```

**AFTER:**
```php
} catch (\Throwable) {
    // Fallback to local summarization.
}
```

---

## File 6: `src/Service/AppAssistantService.php`

### Error 12 — Line 130
**PHPStan Rule:** `identical.alwaysFalse`  
**Error Message:**
```
Strict comparison using === between non-empty-string and '' will always evaluate to false.
```

**Cause:** `json_encode()` returns `string|false`. After the `!is_string()` check, PHPStan knows `$contextJson` is a `string`. But it also tracks that `json_encode` with an array input produces a `non-empty-string` (at minimum `"[]"` or `"{}"`), so the `=== ''` check can never be true.

**BEFORE:**
```php
$contextJson = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
if (!is_string($contextJson) || $contextJson === '') {
    $contextJson = '{}';
}
```

**AFTER:**
```php
$contextJson = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
if (!is_string($contextJson)) {
    $contextJson = '{}';
}
```

---

### Error 13 — Line 325
**PHPStan Rule:** `instanceof.alwaysTrue`  
**Error Message:**
```
Instanceof between App\Entity\Property and App\Entity\Property will always evaluate to true.
```

**Cause:** The `$properties` variable is populated by `$this->propertyRepository->findBy(...)` which returns `Property[]`. Filtering with `instanceof Property` is therefore always true — every element is already guaranteed to be a `Property`.

**BEFORE:**
```php
'properties' => array_map(
    static fn (Property $property): array => [...],
    array_filter($properties, static fn (mixed $property): bool => $property instanceof Property)
),
```

**AFTER:**
```php
'properties' => array_map(
    static fn (Property $property): array => [...],
    $properties
),
```

---

### Error 14 — Line 346
**PHPStan Rule:** `instanceof.alwaysTrue`  
**Error Message:**
```
Instanceof between App\Entity\Service and App\Entity\Service will always evaluate to true.
```

**Cause:** Same pattern — `$services` comes from `$this->serviceRepository->findBy(...)` which returns `Service[]`. The `instanceof` check is redundant.

**BEFORE:**
```php
'services' => array_map(
    static fn (TravelService $service): array => [...],
    array_filter($services, static fn (mixed $service): bool => $service instanceof TravelService)
),
```

**AFTER:**
```php
'services' => array_map(
    static fn (TravelService $service): array => [...],
    $services
),
```

---

## File 7: `src/Service/GamificationProgressService.php`

### Error 15 — Line 103
**PHPStan Rule:** `catch.neverThrown`  
**Error Message:**
```
Dead catch - Doctrine\DBAL\Exception is never thrown in the try block.
```

**Cause:** `Doctrine\DBAL\Exception` extends `\Throwable`. When caught alongside `\Throwable` in a union catch, the DBAL exception type is redundant because `\Throwable` already covers it.

**BEFORE:**
```php
} catch (Exception|\Throwable) {
    return false;
}
```

**AFTER:**
```php
} catch (\Throwable) {
    return false;
}
```

---

## File 8: `src/Service/GeoapifyService.php`

### Error 16 — Line 235
**PHPStan Rule:** `missingType.iterableValue`  
**Error Message:**
```
Method App\Service\GeoapifyService::requestJson() has parameter $query
with no value type specified in iterable type array.
```

**Cause:** The `$query` parameter is typed `array` without specifying what it contains. PHPStan requires explicit value types on iterable parameters.

**BEFORE:**
```php
/**
 * @return array<string, mixed>
 */
private function requestJson(string $url, array $query, int $ttlSeconds, string $prefix, string $apiKey): array
```

**AFTER:**
```php
/**
 * @param array<string, mixed> $query
 *
 * @return array<string, mixed>
 */
private function requestJson(string $url, array $query, int $ttlSeconds, string $prefix, string $apiKey): array
```

---

### Error 17 — Line 257
**PHPStan Rule:** `function.alreadyNarrowedType`  
**Error Message:**
```
Call to function is_array() with array will always evaluate to true.
```

**Cause:** `$response->toArray(false)` always returns `array`. The subsequent `is_array($payload)` check is therefore always true and redundant.

**BEFORE:**
```php
$payload = $response->toArray(false);
return is_array($payload) ? $payload : [];
```

**AFTER:**
```php
$payload = $response->toArray(false);
return $payload;
```

---

## File 9: `src/Service/GeocodingService.php`

### Error 18 — Line 56
**PHPStan Rule:** `function.alreadyNarrowedType`  
**Error Message:**
```
Call to function is_array() with array will always evaluate to true.
```

**Cause:** `toArray(false)` returns `array`, making `is_array()` redundant.

**BEFORE:**
```php
$payload = $response->toArray(false);
if (!is_array($payload) || $payload === []) {
    return null;
}
```

**AFTER:**
```php
$payload = $response->toArray(false);
if ($payload === []) {
    return null;
}
```

---

### Error 19 — Line 330
**PHPStan Rule:** `function.alreadyNarrowedType`  
**Error Message:**
```
Call to function is_array() with array will always evaluate to true.
```

**Cause:** Same pattern in the `suggestFromNominatim()` method — `toArray(false)` already returns `array`.

**BEFORE:**
```php
$payload = $response->toArray(false);
if (!is_array($payload) || $payload === []) {
    return [];
}
```

**AFTER:**
```php
$payload = $response->toArray(false);
if ($payload === []) {
    return [];
}
```

---

## File 10: `src/Service/GrammarService.php`

### Error 20 — Line 88
**PHPStan Rule:** `catch.neverThrown`  
**Error Message:**
```
Dead catch - Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
is never thrown in the try block.
```

**Cause:** `TransportExceptionInterface` extends `\Throwable`. Catching both in a union is redundant.

**BEFORE:**
```php
} catch (TransportExceptionInterface | \Throwable) {
```

**AFTER:**
```php
} catch (\Throwable) {
```

---

## File 11: `src/Service/NotificationService.php`

### Error 21 — Line 15
**PHPStan Rule:** `missingType.iterableValue`  
**Error Message:**
```
Method App\Service\NotificationService::create() has parameter $context
with no value type specified in iterable type array.
```

**Cause:** The `$context` parameter is typed `?array` without specifying its value type.

**BEFORE:**
```php
public function create(User $user, string $type, string $title, string $message, ?array $context = null): void
```

**AFTER:**
```php
/**
 * @param array<string, mixed>|null $context
 */
public function create(User $user, string $type, string $title, string $message, ?array $context = null): void
```

---

## File 12: `src/Service/TranslationService.php`

### Error 22 — Line 50
**PHPStan Rule:** `catch.neverThrown`  
**Error Message:**
```
Dead catch - Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
is never thrown in the try block.
```

**Cause:** Same dead catch pattern — `TransportExceptionInterface` is a subtype of `\Throwable`.

**BEFORE:**
```php
} catch (TransportExceptionInterface | \Throwable $exception) {
    $lastError = $exception->getMessage();
}
```

**AFTER:**
```php
} catch (\Throwable $exception) {
    $lastError = $exception->getMessage();
}
```

---

## File 13: `src/Service/TripAiAssistantService.php`

### Error 23 — Line 59
**PHPStan Rule:** `match.alwaysTrue`  
**Error Message:**
```
Match arm comparison between 'feasibility_check' and 'feasibility_check' is always true.
```

**Cause:** The `$tool` variable is validated above to be one of `['description', 'recommendations', 'budget_plan', 'feasibility_check']`. In the `match` expression, the first three cases handle `description`, `recommendations`, and `budget_plan`. By the time execution reaches `'feasibility_check'`, it is the only remaining valid value — so the comparison is always true and the `default` arm below it is unreachable.

**BEFORE:**
```php
$title = match ($tool) {
    'description' => 'AI Trip Description',
    'recommendations' => 'AI Recommendations',
    'budget_plan' => 'AI Budget Plan',
    'feasibility_check' => 'AI Feasibility Review',
    default => 'AI Output',
};
```

**AFTER:**
```php
$title = match ($tool) {
    'description' => 'AI Trip Description',
    'recommendations' => 'AI Recommendations',
    'budget_plan' => 'AI Budget Plan',
    default => 'AI Feasibility Review',
};
```

---

## File 14: `src/Service/TripQrCodeService.php`

### Error 24 — Line 62
**PHPStan Rule:** `nullCoalesce.expr`  
**Error Message:**
```
Expression on left side of ?? is not nullable.
```

**Cause:** `Trip::getCurrency()` returns `string` (never `null`). Using `?? 'USD'` is therefore unnecessary — the left side can never be null.

**BEFORE:**
```php
$budget = sprintf(
    '%s %s',
    trim((string) ($trip->getCurrency() ?? 'USD')),
    number_format((float) ($trip->getBudgetAmount() ?? 0.0), 2, '.', ',')
);
```

**AFTER:**
```php
$budget = sprintf(
    '%s %s',
    trim($trip->getCurrency()),
    number_format((float) ($trip->getBudgetAmount() ?? 0.0), 2, '.', ',')
);
```

---

### Error 25 — Line 71
**PHPStan Rule:** `nullCoalesce.expr`  
**Error Message:**
```
Expression on left side of ?? is not nullable.
```

**Cause:** `Trip::getStatus()` returns `string` (never `null`). The `?? '-'` fallback is unreachable.

**BEFORE:**
```php
sprintf('Status: %s', trim((string) ($trip->getStatus() ?? '-'))),
```

**AFTER:**
```php
sprintf('Status: %s', trim($trip->getStatus())),
```

---

## File 15: `src/Service/TripWeatherService.php`

### Error 26 — Line 43
**PHPStan Rule:** `function.alreadyNarrowedType`  
**Error Message:**
```
Call to function is_array() with array will always evaluate to true.
```

**Cause:** `$response->toArray(false)` returns `array`. Checking `is_array($payload)` after that is always true and redundant.

**BEFORE:**
```php
$payload = $response->toArray(false);
if (!is_array($payload) || !isset($payload['current'], $payload['daily'])) {
    return $this->buildPlanningFallback($trip, (float) $lat);
}
```

**AFTER:**
```php
$payload = $response->toArray(false);
if (!isset($payload['current'], $payload['daily'])) {
    return $this->buildPlanningFallback($trip, (float) $lat);
}
```

---

## Summary

| Error Category | Count | Files Affected |
|---|---|---|
| Missing Collection Generic Types | 5 | Blog.php, Comment.php |
| Missing Iterable Value Types | 5 | Notification.php, GeoapifyService.php, NotificationService.php |
| Redundant Type Checks (`is_string`, `is_array`, `instanceof`) | 7 | User.php, GeoapifyService.php, GeocodingService.php, TripWeatherService.php, AppAssistantService.php |
| Dead Catch Blocks | 4 | AiSummarizerService.php, GrammarService.php, TranslationService.php, GamificationProgressService.php |
| Always-True/False Logic | 3 | AppAssistantService.php, TripAiAssistantService.php |
| Non-Nullable Null Coalesce | 2 | TripQrCodeService.php |
| **TOTAL** | **26** | **13 files** |

> ✅ All 26 errors resolved. PHPStan output: `[OK] No errors`
