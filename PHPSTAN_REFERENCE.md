# PHPStan Configuration & Type Improvements Reference

## PHPStan Configuration File

### File: `phpstan.neon`

```yaml
parameters:
    # Analysis level (0-8, higher is stricter)
    level: 5
    
    # Directories to analyze
    paths:
        - src/
    
    # Exclude directories
    excludePaths:
        - vendor/
        - var/
        - public/
    
    # PHP version (8.4)
    phpVersion: 80400
    
    # Report settings
    reportUnmatchedIgnoredErrors: true
    
    # Doctrine configuration
    doctrine:
        repositoryClassRegex: .*Repository$
    
    # Type checking
    strictRules:
        disallowedLooseComparison: false
        disallowedConstructWithoutNew: false
    
    # Ignore common Symfony patterns
    ignoreErrors:
        - 
            message: '#Access to an undefined property#'
            path: src/Service/
        -
            message: '#Call to an undefined method.*toArray|getScalarResult#'
            reportUnmatched: false
```

### Key Configuration Explanations

| Setting | Value | Purpose |
|---------|-------|---------|
| level | 5 | Strict analysis level |
| phpVersion | 80400 | PHP 8.4.0.0 support |
| paths | src/ | Only analyze source code |
| excludePaths | vendor/, var/, public/ | Ignore framework, cache, uploads |
| doctrine.repositoryClassRegex | .*Repository$ | Recognize Doctrine repositories |
| reportUnmatchedIgnoredErrors | true | Warn about unused ignore rules |

---

## Type Annotations Applied

### Property Entity

```php
// Nullable types for optional fields
private ?int $id = null;
private ?string $title = null;
private ?string $description = null;
private ?string $propertyType = null;
private ?string $city = null;
private ?string $country = null;
private ?string $address = null;

// Coordinates - nullable floats
private ?float $latitude = null;
private ?float $longitude = null;

// Price - decimal as string to prevent precision loss
private string $pricePerNight = '0.00';

// Integer fields with defaults
private int $bedrooms = 0;
private int $maxGuests = 1;

// Boolean status
private bool $isActive = true;

// Timestamps
private ?\DateTimeImmutable $createdAt = null;
private ?\DateTimeImmutable $updatedAt = null;

// Collections with generic types
/** @var Collection<int, Offer> */
private Collection $offers;

/** @var Collection<int, Booking> */
private Collection $bookings;
```

### Method Return Types

```php
// Getters
public function getId(): ?int
public function getTitle(): ?string
public function getDescription(): ?string
public function getPropertyType(): ?string
public function getCity(): ?string
public function getCountry(): ?string
public function getAddress(): ?string
public function getLatitude(): ?float
public function getLongitude(): ?float
public function getPricePerNight(): string
public function getBedrooms(): int
public function getMaxGuests(): int
public function getImages(): ?string
public function isActive(): bool
public function getCreatedAt(): ?\DateTimeImmutable
public function getUpdatedAt(): ?\DateTimeImmutable
public function getOffers(): Collection
public function getBookings(): Collection

// Setters - return static for fluent interface
public function setTitle(?string $title): static
public function setDescription(?string $description): static
public function setPropertyType(?string $propertyType): static
public function setCity(?string $city): static
public function setCountry(?string $country): static
public function setAddress(?string $address): static
public function setLatitude(?float $latitude): static
public function setLongitude(?float $longitude): static
public function setPricePerNight(string|float|int $pricePerNight): static
public function setBedrooms(int $bedrooms): static
public function setMaxGuests(int $maxGuests): static
public function setImages(?string $images): static
public function setIsActive(bool $isActive): static
public function setCreatedAt(?\DateTimeInterface $createdAt): static
public function setUpdatedAt(?\DateTimeInterface $updatedAt): static

// Collection operations
public function addOffer(Offer $offer): static
public function removeOffer(Offer $offer): static
public function addBooking(Booking $booking): static
public function removeBooking(Booking $booking): static

// Lifecycle hooks
public function onPrePersist(): void
public function onPreUpdate(): void
```

---

## Repository Type Signatures

```php
// Filtering with typed array parameters
public function createFilteredQueryBuilder(array $filters): QueryBuilder {
    // @param array<string, string> $filters
}

// Result methods with typed returns
public function getDistinctPropertyTypes(): array {
    // @return list<string>
}

public function getDistinctCities(): array {
    // @return list<string>
}

public function getDistinctCountries(): array {
    // @return list<string>
}
```

---

## Controller Type Signatures

### Public Route Handlers

```php
public function index(
    Request $request,
    PropertyRepository $propertyRepository,
    CurrencyConverterService $currencyConverter
): Response

public function show(
    Request $request,
    Property $property,
    CurrencyConverterService $currencyConverter
): Response

public function new(
    Request $request,
    EntityManagerInterface $entityManager,
    GeoapifyService $geoapifyService
): Response

public function chat(
    Request $request,
    Property $property,
    GeminiService $geminiService
): JsonResponse

public function edit(
    Request $request,
    Property $property,
    EntityManagerInterface $entityManager,
    GeoapifyService $geoapifyService
): Response

public function delete(
    Request $request,
    Property $property,
    EntityManagerInterface $entityManager
): Response

public function generatePdf(
    Request $request,
    Property $property
): Response
```

### Private Helper Methods

```php
// File upload handling
private function handlePropertyImageUpload(
    Property $property,
    ?UploadedFile $imageFile
): void

// Image resolution for PDF
private function resolvePropertyImageForPdf(Property $property): ?string

// Rate limiting enforcement
private function enforceChatRateLimit(
    Request $request,
    Property $property
): ?JsonResponse

// Chat history management
/**
 * @return array<int, array{role:string,content:string,timestamp:string}>
 */
private function getChatHistory(
    Request $request,
    Property $property
): array

private function storeChatHistory(
    Request $request,
    Property $property,
    array $history
): void

private function getChatHistoryKey(Property $property): string

/**
 * @param mixed $historyPayload
 * @return array<int, array{role:string,content:string,timestamp:string}>
 */
private function normalizeClientHistory(mixed $historyPayload): array
```

---

## Service Type Signatures

### GeoapifyService

```php
// Location autocomplete with detailed return shape
/**
 * @return array<int, array{
 *     formatted: string,
 *     address: string,
 *     city: string,
 *     country: string,
 *     postalCode: string,
 *     latitude: float,
 *     longitude: float
 * }>
 */
public function autocomplete(string $query, int $limit = 6): array

// Nearby places with detailed return shape
/**
 * @return array<int, array{
 *     name: string,
 *     category: string,
 *     latitude: float,
 *     longitude: float,
 *     address: string
 * }>
 */
public function nearbyPlaces(
    float $latitude,
    float $longitude,
    int $radiusMeters = 4000
): array

// Geocode property from address
public function geocodeProperty(Property $property): void

// Reverse geocoding with detailed return shape
/**
 * @return array{
 *     address: string,
 *     city: string,
 *     country: string,
 *     postalCode: string,
 *     latitude: float,
 *     longitude: float,
 *     formatted: string
 * }|null
 */
public function reverse(float $latitude, float $longitude): ?array

// Route calculation with detailed return shape
/**
 * @return array{
 *     distanceMeters: float,
 *     durationSeconds: float,
 *     distanceKm: float|int,
 *     durationMinutes: int,
 *     geometry: mixed
 * }|null
 */
public function route(
    float $fromLatitude,
    float $fromLongitude,
    float $toLatitude,
    float $toLongitude
): ?array

// Check if API key is configured
public function hasAutocompleteApiKey(): bool

// Private cache wrapper
/**
 * @param array<string, mixed> $query
 * @return array<string, mixed>
 */
private function requestJson(
    string $url,
    array $query,
    int $ttlSeconds,
    string $prefix,
    string $apiKey
): array
```

---

## Form Type Signatures

```php
class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    public function configureOptions(OptionsResolver $resolver): void
}
```

---

## Type Annotation Patterns

### Nullable Types

```php
// Optional string field
private ?string $description = null;

// Optional coordinate
private ?float $latitude = null;

// Optional return value
public function getDescription(): ?string

// Optional parameter
public function setDescription(?string $description): static
```

### Collection Typing

```php
// Doctrine OneToMany relationship
/** @var Collection<int, Offer> */
private Collection $offers;

// Return type with generic
/** @return Collection<int, Offer> */
public function getOffers(): Collection
```

### Union Types

```php
// Accept multiple types
public function setPricePerNight(string|float|int $pricePerNight): static

// Return either array or null
public function reverse(float $latitude, float $longitude): ?array

// Parameter could be multiple types
private function normalizeClientHistory(mixed $historyPayload): array
```

### Array Shapes

```php
// Simple array of strings
/** @return array<int, string> */
public function getDistinctCities(): array

// Complex array with structure
/**
 * @return array<int, array{
 *     formatted: string,
 *     address: string,
 *     latitude: float,
 *     longitude: float
 * }>
 */
public function autocomplete(string $query): array

// Parameterized arrays in parameters
/**
 * @param array<string, string> $filters
 */
public function createFilteredQueryBuilder(array $filters): QueryBuilder
```

---

## Common PHPStan Patterns

### Safe Array Access

```php
// Using null coalescing
$value = $payload['key'] ?? '';
$count = count($array ?? []);

// Using isset check
if (isset($feature['lat'])) {
    $lat = (float) $feature['lat'];
}

// Combining checks
$value = isset($array['key']) && is_array($array['key']) ? $array['key'] : [];
```

### Type Casting

```php
// Explicit casting
$lat = (float) $feature['lat'];
$count = (int) $response->getStatusCode();
$text = (string) $value;

// Safe boolean casting
$isActive = (bool) $row['active'];

// Safe array casting
$data = is_array($payload) ? $payload : [];
```

### Null Safety

```php
// Ternary with null check
$value = null !== $value ? trim($value) : null;

// Safe method chain with null coalescing
$city = $property->getCity() ?? 'Unknown';

// Safe iteration
foreach ($items ?? [] as $item) {
    // Process $item
}
```

---

## PHPStan Level 5 Checklist

- ✅ Return types declared on all methods
- ✅ Parameter types declared on all methods
- ✅ Properties have type declarations
- ✅ No undefined variables
- ✅ No undefined array keys without safety checks
- ✅ Proper nullable type marking
- ✅ Collections have generic type parameters
- ✅ External API calls wrapped in try-catch
- ✅ Type casts are explicit
- ✅ Array accesses are safe

---

## Testing PHPStan Locally

```bash
# Run PHPStan
vendor/bin/phpstan analyze

# Run with verbose output
vendor/bin/phpstan analyze -vvv

# Run on specific file
vendor/bin/phpstan analyze src/Entity/Property.php

# Generate human-readable report
vendor/bin/phpstan analyze --report=table

# Generate JSON for CI/CD
vendor/bin/phpstan analyze --report=json > phpstan-report.json

# Cache behavior
vendor/bin/phpstan analyze --no-cache  # Skip cache
vendor/bin/phpstan clear-result-cache   # Clear cache

# Show memory usage
vendor/bin/phpstan analyze --memory-limit=1G
```

---

## Integration with IDE

### PHPStorm/IntelliJ
1. Install PHPStan plugin
2. Configure path to vendor/bin/phpstan
3. Set analysis level to 5
4. Enable real-time analysis

### VS Code
1. Install PHP Intelephense extension
2. Enable PHPStan support
3. Configure PHPStan binary path
4. Real-time type checking enabled

---

## Troubleshooting Guide

### Error: Call to an undefined method
**Cause**: Repository methods not recognized
**Solution**: Ensure `doctrine` configuration in phpstan.neon

### Error: Undefined index
**Cause**: Array access without null coalescing
**Solution**: Use `??` operator or `isset()` check

### Error: Method has no return type
**Cause**: Missing return type declaration
**Solution**: Add explicit `->` return type to method signature

### Error: Cannot assign mixed to string
**Cause**: Type mismatch in assignment
**Solution**: Use explicit casting or type narrowing

### PHPStan very slow
**Cause**: Analyzing too many files
**Solution**: Limit paths in phpstan.neon

---

## Best Practices

1. **Always declare return types** - No implicit void or mixed
2. **Mark nullables explicitly** - Use `?` for optional values
3. **Document complex arrays** - Use shape syntax in PHPDoc
4. **Type collection generics** - Always use `Collection<int, Type>`
5. **Safe API handling** - Validate before accessing array keys
6. **Exception handling** - Wrap external calls in try-catch
7. **Explicit casting** - Never rely on implicit type conversion
8. **Validate parameters** - Check types before use

---

**Last Updated**: May 7, 2026
**PHPStan Version**: 1.x
**PHP Version**: 8.4.0.0
**Configuration Level**: 5
