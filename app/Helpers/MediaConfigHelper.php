<?php

namespace App\Helpers;

class MediaConfigHelper
{
    /**
     * Standard media collection names
     */
    public const COLLECTIONS = [
        'question' => [
            'images' => 'question-images',
            'videos' => 'question-videos',
            'documents' => 'question-documents',
        ],
        'choice' => [
            'images' => 'choice-images',
            'icons' => 'choice-icons',
        ],
        'survey' => [
            'banners' => 'survey-banners',
            'logos' => 'survey-logos',
            'attachments' => 'survey-attachments',
        ],
        'survey_page' => [
            'images' => 'page-images',
            'backgrounds' => 'page-backgrounds',
        ],
    ];

    /**
     * Standard media reference structure for Question.config
     */
    public const MEDIA_REFERENCE_STRUCTURE = [
        'type' => 'string', // 'image', 'video', 'document', 'icon'
        'collection' => 'string', // collection name
        'media_id' => 'integer', // media ID
        'url' => 'string', // media URL
        'alt_text' => 'string|null', // alt text for accessibility
        'caption' => 'string|null', // caption text
        'display_order' => 'integer', // display order
    ];

    /**
     * Get standard media collection name
     */
    public static function getCollectionName(string $model, string $type): string
    {
        return self::COLLECTIONS[$model][$type] ?? "{$model}-{$type}";
    }

    /**
     * Create standardized media reference for Question.config
     */
    public static function createMediaReference(
        string $type,
        string $collection,
        int $mediaId,
        string $url,
        ?string $altText = null,
        ?string $caption = null,
        int $displayOrder = 0
    ): array {
        return [
            'type' => $type,
            'collection' => $collection,
            'media_id' => $mediaId,
            'url' => $url,
            'alt_text' => $altText,
            'caption' => $caption,
            'display_order' => $displayOrder,
        ];
    }

    /**
     * Validate media reference structure
     */
    public static function validateMediaReference(array $reference): bool
    {
        $requiredKeys = ['type', 'collection', 'media_id', 'url'];
        
        foreach ($requiredKeys as $key) {
            if (!isset($reference[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get media references from Question.config
     */
    public static function getMediaReferences(array $config): array
    {
        return $config['media_references'] ?? [];
    }

    /**
     * Add media reference to Question.config
     */
    public static function addMediaReference(array $config, array $reference): array
    {
        if (!self::validateMediaReference($reference)) {
            throw new \InvalidArgumentException('Invalid media reference structure');
        }

        $config['media_references'] = $config['media_references'] ?? [];
        $config['media_references'][] = $reference;

        return $config;
    }

    /**
     * Remove media reference from Question.config
     */
    public static function removeMediaReference(array $config, int $mediaId): array
    {
        if (!isset($config['media_references'])) {
            return $config;
        }

        $config['media_references'] = array_filter(
            $config['media_references'],
            fn($ref) => $ref['media_id'] !== $mediaId
        );

        return $config;
    }

    /**
     * Update media reference in Question.config
     */
    public static function updateMediaReference(array $config, int $mediaId, array $newReference): array
    {
        if (!self::validateMediaReference($newReference)) {
            throw new \InvalidArgumentException('Invalid media reference structure');
        }

        if (!isset($config['media_references'])) {
            return $config;
        }

        foreach ($config['media_references'] as $key => $reference) {
            if ($reference['media_id'] === $mediaId) {
                $config['media_references'][$key] = $newReference;
                break;
            }
        }

        return $config;
    }
} 