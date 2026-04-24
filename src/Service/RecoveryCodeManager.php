<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class RecoveryCodeManager
{
    public function __construct(
        #[Autowire('%env(string:APP_SECRET)%')]
        private readonly string $appSecret,
    ) {
    }

    /**
     * @return list<string>
     */
    public function generateCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < max(1, $count); ++$i) {
            $raw = strtoupper(bin2hex(random_bytes(4)));
            $codes[] = sprintf('%s-%s', substr($raw, 0, 4), substr($raw, 4, 4));
        }

        return $codes;
    }

    /**
     * @param list<string> $codes
     * @return list<string>
     */
    public function hashCodes(array $codes): array
    {
        return array_map(fn (string $code): string => $this->hashCode($code), $codes);
    }

    /**
     * @param list<string> $storedHashes
     * @return list<string>|null
     */
    public function consumeCode(string $code, array $storedHashes): ?array
    {
        $normalized = $this->normalizeCode($code);
        if ('' === $normalized) {
            return null;
        }

        $hash = $this->hashCode($normalized);
        foreach ($storedHashes as $index => $storedHash) {
            if (hash_equals($storedHash, $hash)) {
                unset($storedHashes[$index]);

                return array_values($storedHashes);
            }
        }

        return null;
    }

    private function hashCode(string $code): string
    {
        return hash('sha256', $this->appSecret.'|'.$this->normalizeCode($code));
    }

    private function normalizeCode(string $code): string
    {
        return strtoupper(str_replace([' ', '_'], '', trim($code)));
    }
}
