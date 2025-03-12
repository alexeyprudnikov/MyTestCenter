<?php

namespace App\Dto;

readonly class QuestionSchema
{
    /**
     * @param string $question
     * @param string|null $explanation_headline
     * @param string|null $explanation_text
     * @param string[] $answers
     */
    public function __construct(
        public string  $question,
        public ?string $explanation_headline,
        public ?string $explanation_text,
        public array   $answers
    ) {}
}
