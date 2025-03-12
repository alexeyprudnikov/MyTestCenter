<?php

namespace App\Dto;

class WorkflowFixture
{
    public static array $response_TEST = [
        [
            'question' => 'test question 1',
            'answers' => [
                [
                    'answer' => 'test answer 1',
                    'is_correct' => true,
                ],
                [
                    'answer' => 'test answer 2',
                    'is_correct' => false,
                ],
                [
                    'answer' => 'test answer 3',
                    'is_correct' => false,
                ]
            ]
        ],
        [
            'question' => 'test question 2',
            'answers' => [
                [
                    'answer' => 'test answer 1',
                    'is_correct' => false,
                ],
                [
                    'answer' => 'test answer 2',
                    'is_correct' => true,
                ],
                [
                    'answer' => 'test answer 3',
                    'is_correct' => false,
                ],
                [
                    'answer' => 'test answer 4',
                    'is_correct' => true,
                ],
                [
                    'answer' => 'test answer 5',
                    'is_correct' => false,
                ]
            ]
        ]
    ];

    public static array $response_LEADNING = [
        [
            'question' => 'test question 1',
            'explanation' => 'Learning for question 1, answer 1 is correct',
            'answers' => [
                [
                    'answer' => 'test answer 1',
                    'is_correct' => true,
                ],
                [
                    'answer' => 'test answer 2',
                    'is_correct' => false,
                ],
                [
                    'answer' => 'test answer 3',
                    'is_correct' => false,
                ]
            ]
        ],
        [
            'question' => 'test question 2',
            'explanation' => 'Learning for question 2, answers 2 and 4 are correct',
            'answers' => [
                [
                    'answer' => 'test answer 1',
                    'is_correct' => false,
                ],
                [
                    'answer' => 'test answer 2',
                    'is_correct' => true,
                ],
                [
                    'answer' => 'test answer 3',
                    'is_correct' => false,
                ],
                [
                    'answer' => 'test answer 4',
                    'is_correct' => true,
                ],
                [
                    'answer' => 'test answer 5',
                    'is_correct' => false,
                ]
            ]
        ]
    ];
}
