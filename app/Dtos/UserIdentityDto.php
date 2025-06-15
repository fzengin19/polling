<?php


namespace App\Dtos;

readonly class UserIdentityDto
{
    public function __construct(mixed $userId = null, mixed $anonId = null) {
        
        $this->userId = $this->normalizeUserId($userId);
        $this->anonId = $this->normalizeAnonId($anonId);

        if (is_null($this->userId) && is_null($this->anonId)) {
            throw new \InvalidArgumentException("No identity provided. Either user ID or anonymous ID must be set.");
        }
    }

    private function normalizeUserId(mixed $userId): ?int
    {
        if (is_numeric($userId) && (int)$userId > 0) {
            return (int)$userId;
        }
        return null;
    }

    private function normalizeAnonId(mixed $anonId): ?string
    {
        if (is_string($anonId) && strlen(trim($anonId)) > 0) {
            return $anonId;
        }
        return null;
    }

    public function isAnonymous(): bool
    {
        return $this->userId === null;
    }

    public readonly ?int $userId;
    public readonly ?string $anonId;
}
