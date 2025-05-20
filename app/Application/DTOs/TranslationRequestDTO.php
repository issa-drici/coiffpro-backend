<?php

namespace App\Application\DTOs;

class TranslationRequestDTO
{
    public function __construct(
        public string $text,
        public string $sourceLanguage,
        public array $targetLanguages
    ) {
    }
} 