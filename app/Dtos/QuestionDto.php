<?php

namespace App\Dtos;

class QuestionDto
{
    public int $page_id;
    public string $type;
    public string $title;
    public bool $is_required;
    public ?string $help_text;
    public ?string $placeholder;
    public ?array $config;
    public int $order_index;

    public function __construct(array $data)
    {
        $this->page_id = $data['page_id'];
        $this->type = $data['type'];
        $this->title = $data['title'];
        $this->is_required = (bool)($data['is_required'] ?? false);
        $this->help_text = $data['help_text'] ?? null;
        $this->placeholder = $data['placeholder'] ?? null;
        $this->config = $data['config'] ?? null;
        $this->order_index = $data['order_index'] ?? 0;
    }

    public function toArray(): array
    {
        return [
            'page_id' => $this->page_id,
            'type' => $this->type,
            'title' => $this->title,
            'is_required' => $this->is_required,
            'help_text' => $this->help_text,
            'placeholder' => $this->placeholder,
            'config' => $this->config,
            'order_index' => $this->order_index,
        ];
    }
} 