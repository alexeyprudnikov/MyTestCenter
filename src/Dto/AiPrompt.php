<?php

namespace App\Dto;

use App\Entity\Test;

class AiPrompt
{
    private array $promptParts_TEST = [
        'List %d questions on the topic "%s" with %s answers and only %s, other answers should be not correct.',
        'Use %s language for output.'
    ];
    private array $promptParts_LEARNING = [
        'List %d questions on the topic "%s" with %s answers and only %s, other answers should be not correct.',
        'Add an explanation of correct answers to each question, this explanation should not have correct answer directly, but only general headline and text, and the text should be between 500 and 1000 characters long.',
        'Use %s language for output.'
    ];

    private const DEFAULT_ANSWERS = 3;
    private const DEFAULT_RIGHT_ANSWERS = 1;
    private const DEFAULT_LANGUAGE = 'english';

    public function __construct(
        private readonly Test $test
    ) {}

    public function __toString(): string
    {
        $answersCountMin = $this->test->getAnswersCount()[0] ?? self::DEFAULT_ANSWERS;
        $answersCountMax = $this->test->getAnswersCount()[1] ?? self::DEFAULT_ANSWERS;
        $answersCount = $answersCountMin === $answersCountMax ? $answersCountMax : "from $answersCountMin to $answersCountMax";

        $rightAnswersCountMin = $this->test->getRightAnswersCount()[0] ?? self::DEFAULT_RIGHT_ANSWERS;
        $rightAnswersCountMax = $this->test->getRightAnswersCount()[1] ?? self::DEFAULT_RIGHT_ANSWERS;
        $rightAnswersCount = $rightAnswersCountMin === $rightAnswersCountMax ? "$rightAnswersCountMax correct answer" : "from $rightAnswersCountMin to $rightAnswersCountMax correct answers";

        $promptParts = $this->test->isTypeLearning() ? $this->promptParts_LEARNING : $this->promptParts_TEST;
        return sprintf(
            implode(' ', $promptParts),
            $this->test->getQuestionsCount(),
            $this->test->getDescription(),
            $answersCount,
            $rightAnswersCount,
            $this->test->getLanguage()?->value ?? self::DEFAULT_LANGUAGE
        );
    }
}
