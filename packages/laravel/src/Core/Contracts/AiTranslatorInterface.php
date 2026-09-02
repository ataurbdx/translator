<?php

namespace Ataurbdx\TranslatorEngine\Core\Contracts;

interface AiTranslatorInterface
{
    /**
     * Translate a single text string from source to target locale.
     */
    public function translate(string $text, string $to, ?string $from = null, array $context = []): string;

    /**
     * Translate a batch/array of text strings.
     */
    public function translateBatch(array $items, string $to, ?string $from = null, array $context = []): array;
}
