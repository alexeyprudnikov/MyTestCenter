<?php

namespace App\Twig;

use App\Dto\QuestionSchema;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('count_right_answers', [$this, 'countRightAnswers']),
        ];
    }

    /**
     * @param QuestionSchema $question
     * @return int
     */
    public function countRightAnswers(QuestionSchema $question): int
    {
        return array_reduce($question->answers, function ($carry, $answer) {
            $add = ($answer['is_correct'] ?? false) === true ? 1 : 0;
            return $carry + $add;
        }, 0);
    }
}
