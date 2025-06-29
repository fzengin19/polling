<?php

namespace App\Dtos;

use App\Core\BaseDto;
use Illuminate\Http\UploadedFile;

class MediaDto extends BaseDto
{
    public function __construct(
        public readonly int $question_id,
        public readonly UploadedFile $file,
        public readonly ?string $alt_text = null,
        public readonly ?string $caption = null,
    ) {}

    public static function fromArray(array $data, UploadedFile $file): self
    {
        return new self(
            question_id: $data['question_id'],
            file: $file,
            alt_text: $data['alt_text'] ?? null,
            caption: $data['caption'] ?? null,
        );
    }
} 