<?php

namespace App\Dtos;

class MediaDto
{
    public function __construct(
        public ?int $id,
        public int $question_id,
        public string $file_name,
        public string $file_path,
        public string $mime_type,
        public int $size,
        public ?string $alt_text,
        public ?string $caption,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            question_id: $data['question_id'],
            file_name: $data['file_name'],
            file_path: $data['file_path'],
            mime_type: $data['mime_type'],
            size: $data['size'],
            alt_text: $data['alt_text'] ?? null,
            caption: $data['caption'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'question_id' => $this->question_id,
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'alt_text' => $this->alt_text,
            'caption' => $this->caption,
        ];
    }
} 