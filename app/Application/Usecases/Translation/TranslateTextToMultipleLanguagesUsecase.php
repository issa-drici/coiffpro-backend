<?php

namespace App\Application\Usecases\Translation;

use App\Application\DTOs\TranslationRequestDTO;
use App\Infrastructure\Services\OpenAITranslationService;

class TranslateTextToMultipleLanguagesUsecase
{
    public function __construct(
        private OpenAITranslationService $translationService
    ) {}

    public function execute(array $sourceText): array
    {
        if (empty($sourceText)) {
            return [];
        }

        // Détecter la langue source (on prend la première clé)
        $sourceLanguage = array_key_first($sourceText);
        $textToTranslate = $sourceText[$sourceLanguage];

        // Obtenir les langues cibles (toutes sauf la source)
        $targetLanguages = array_diff(
            $this->translationService->getSupportedLanguages(),
            [$sourceLanguage]
        );
        // Créer la requête de traduction
        $request = new TranslationRequestDTO(
            text: $textToTranslate,
            sourceLanguage: $sourceLanguage,
            targetLanguages: $targetLanguages
        );

        // Obtenir les traductions
        $translations = $this->translationService->translate($request);

        // Fusionner avec le texte source
        return array_merge(
            $sourceText,
            $translations
        );
    }
}
