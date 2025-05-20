<?php

namespace App\Infrastructure\Services;

use OpenAI\Client;
use App\Application\DTOs\TranslationRequestDTO;

class OpenAITranslationService
{
    private array $supportedLanguages = ['fr', 'en', 'es', 'de', 'it', 'nl', 'pt', 'ar'];

    public function __construct(private Client $openAI) {}

    public function translate(TranslationRequestDTO $request): array
    {
        $systemPrompt = <<<EOT
Tu es TranslatorJsonGPT, une IA qui traduit des objets d'un json dans plusieurs langues.
Tu es un expert en traduction et tu connais les différentes syntaxes, tournures de phrases, façons de s'exprimer dans chacune des langues.
Tu es expert dans la traduction tout en gardant le sens initial et en utilisant des expressions idiomatiques appropriées dans les langues de destination.

Règles :
- Génère UNIQUEMENT du JSON valide
- Rapproche-toi au maximum du sens voulu tout en gardant une expression naturelle dans chaque langue
- Traduis toujours à partir de la valeur en français (ne traduis jamais la valeur en français)
- Si un mot semble être brandé, ne le traduit pas et garde le tel quel
- N'ajoute aucun caractère d'échappement ou retour à la ligne
- Le JSON doit contenir toutes les langues supportées
EOT;

        $response = $this->openAI->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'user',
                    'content' => "Traduis ce texte du {$request->sourceLanguage} vers : " .
                                implode(', ', $request->targetLanguages) . ".\n\n" .
                                "Texte à traduire : {$request->text}"
                ],
            ],
            'temperature' => 0.3,
            'response_format' => ['type' => 'json_object']
        ]);

        $translations = json_decode($response->choices[0]->message->content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [$request->sourceLanguage => $request->text];
        }

        return $translations;
    }

    public function getSupportedLanguages(): array
    {
        return $this->supportedLanguages;
    }
}
