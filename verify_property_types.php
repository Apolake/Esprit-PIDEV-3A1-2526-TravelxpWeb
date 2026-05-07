<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Entity\Property;
use App\Entity\Offer;
use App\Entity\Booking;
use Doctrine\Common\Collections\Collection;

echo "=== Property Module Type Safety Verification ===\n\n";

// Test 1: Property Entity Instantiation
echo "Test 1: Property Entity Instantiation\n";
$property = new Property();
assert($property->getId() === null, 'ID should be null initially');
echo "✓ Property instantiated successfully\n";

// Test 2: Property Getters/Setters with Type Conversion
echo "\nTest 2: Price Per Night Decimal Handling\n";
$property->setPricePerNight('150.50');
assert($property->getPricePerNight() === '150.50', 'String price should be preserved');
echo "✓ String price set and retrieved: " . $property->getPricePerNight() . "\n";

$property->setPricePerNight(199.99);
assert(is_string($property->getPricePerNight()), 'Getter should return string');
echo "✓ Float price converted to string: " . $property->getPricePerNight() . "\n";

// Test 3: Coordinate Nullable Handling
echo "\nTest 3: Nullable Coordinate Handling\n";
assert($property->getLatitude() === null, 'Latitude should be null initially');
assert($property->getLongitude() === null, 'Longitude should be null initially');
$property->setLatitude(36.7372);
$property->setLongitude(3.0869);
assert($property->getLatitude() === 36.7372, 'Latitude should be set');
assert($property->getLongitude() === 3.0869, 'Longitude should be set');
echo "✓ Coordinates set and retrieved: Lat={$property->getLatitude()}, Lon={$property->getLongitude()}\n";

// Test 4: Collection Typing
echo "\nTest 4: Doctrine Collection Typing\n";
$offers = $property->getOffers();
assert($offers instanceof Collection, 'getOffers() should return Collection');
assert($offers->isEmpty(), 'Offers collection should be empty initially');
echo "✓ Offers collection is properly typed: " . get_class($offers) . "\n";

$bookings = $property->getBookings();
assert($bookings instanceof Collection, 'getBookings() should return Collection');
assert($bookings->isEmpty(), 'Bookings collection should be empty initially');
echo "✓ Bookings collection is properly typed: " . get_class($bookings) . "\n";

// Test 5: Boolean and Integer Fields
echo "\nTest 5: Integer and Boolean Fields\n";
assert($property->isActive() === true, 'isActive should be true by default');
assert($property->getBedrooms() === 0, 'Bedrooms should be 0 by default');
assert($property->getMaxGuests() === 1, 'MaxGuests should be 1 by default');
echo "✓ isActive: {$property->isActive()}\n";
echo "✓ bedrooms: {$property->getBedrooms()}\n";
echo "✓ maxGuests: {$property->getMaxGuests()}\n";

// Test 6: String Fields with Trimming
echo "\nTest 6: String Fields with Trimming\n";
$property->setTitle('  Beach House  ');
assert($property->getTitle() === 'Beach House', 'Title should be trimmed');
echo "✓ Title trimmed: '{$property->getTitle()}'\n";

$property->setCity('  Algiers  ');
assert($property->getCity() === 'Algiers', 'City should be trimmed');
echo "✓ City trimmed: '{$property->getCity()}'\n";

// Test 7: Timestamps
echo "\nTest 7: Timestamps (DateTimeImmutable)\n";
$now = new \DateTimeImmutable();
$property->setCreatedAt($now);
$created = $property->getCreatedAt();
assert($created instanceof \DateTimeImmutable, 'CreatedAt should be DateTimeImmutable');
echo "✓ CreatedAt is DateTimeImmutable: " . $created->format('Y-m-d H:i:s') . "\n";

// Test 8: Fluent Interface
echo "\nTest 8: Fluent Interface (returns static)\n";
$result = $property
    ->setTitle('Modern Apartment')
    ->setCity('Tunis')
    ->setCountry('Tunisia')
    ->setPricePerNight(120.00)
    ->setBedrooms(2)
    ->setMaxGuests(4);
assert($result instanceof Property, 'Fluent interface should return Property instance');
echo "✓ Fluent interface works correctly\n";

// Test 9: Nullable String Fields
echo "\nTest 9: Nullable String Fields\n";
$property->setDescription(null);
assert($property->getDescription() === null, 'Description should accept null');
$property->setDescription('Beautiful beachfront property');
assert($property->getDescription() === 'Beautiful beachfront property', 'Description should be set');
echo "✓ Nullable string field works correctly\n";

// Test 10: Type Preservation
echo "\nTest 10: Type Preservation in Getters\n";
$property->setTitle('Test Property');
$title = $property->getTitle();
assert(is_string($title) || $title === null, 'Title should be string or null');
echo "✓ Getter returns correct type\n";

echo "\n=== All Type Safety Tests Passed ✓ ===\n";

// Summary
echo "\n=== Type Safety Summary ===\n";
echo "✓ Entity instantiation works\n";
echo "✓ Decimal (pricePerNight) handling is correct\n";
echo "✓ Nullable coordinates are properly handled\n";
echo "✓ Doctrine Collections are properly typed\n";
echo "✓ Integer and Boolean fields work correctly\n";
echo "✓ String trimming works for string fields\n";
echo "✓ DateTimeImmutable timestamps work\n";
echo "✓ Fluent interface (returns static) works\n";
echo "✓ Nullable optional fields work correctly\n";
echo "✓ Type preservation in getters is maintained\n";

echo "\n✓✓✓ Property Module Type Safety Verified ✓✓✓\n";
