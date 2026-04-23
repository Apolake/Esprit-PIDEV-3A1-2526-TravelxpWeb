<?php

namespace App\Twig;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MediaThumbExtension extends AbstractExtension
{
    private ?bool $processorAvailable = null;

    public function __construct(private readonly CacheManager $cacheManager)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('media_thumb', [$this, 'mediaThumb']),
        ];
    }

    public function mediaThumb(?string $path, string $filter): string
    {
        $rawPath = trim((string) $path);
        if ($rawPath === '') {
            return '';
        }

        if (str_starts_with($rawPath, 'data:') || preg_match('#^https?://#i', $rawPath)) {
            return $rawPath;
        }

        $normalizedPath = str_starts_with($rawPath, '/') ? $rawPath : '/' . ltrim($rawPath, '/');
        if (!$this->isProcessorAvailable()) {
            return $normalizedPath;
        }

        try {
            return $this->cacheManager->getBrowserPath($normalizedPath, $filter);
        } catch (\Throwable) {
            return $normalizedPath;
        }
    }

    private function isProcessorAvailable(): bool
    {
        if ($this->processorAvailable !== null) {
            return $this->processorAvailable;
        }

        $this->processorAvailable = extension_loaded('gd')
            || extension_loaded('imagick')
            || extension_loaded('gmagick');

        return $this->processorAvailable;
    }
}

