<?php

namespace App\Dto\Schema\GoogleGenAi;

use App\Dto\AiPrompt;
use App\Entity\Test;

class TestResponse
{
    public static function getSchema(Test $test): array
    {
        $prompt = new AiPrompt($test);
        $responseSchema = [
            'description' =>  "{$prompt} Don't change properties and don't add other properties.",
            'type' => 'ARRAY',
            'items' => [
                'type' => 'OBJECT',
                'properties' => [
                    'question' => [
                        'description' =>  'Text of question.',
                        'type' => 'STRING'
                    ],
                    'answers' => [
                        'description' =>  'Answers for the question.',
                        'type' => 'ARRAY',
                        'items' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'answer' => [
                                    'description' =>  'Text of answer.',
                                    'type' => 'STRING'
                                ],
                                'is_correct' => [
                                    'description' =>  'Is answer correct or not correct.',
                                    'type' => 'BOOLEAN'
                                ]
                            ],
                            'required' => ['answer', 'is_correct'],
                        ]
                    ]
                ],
                'required' => ['question', 'answers'],
            ]
        ];
        if ($test->isTypeLearning()) {
            $responseSchema['items']['properties']['explanation_headline'] = [
                'description' =>  'Headline of explanation.',
                'type' => 'STRING'
            ];
            $responseSchema['items']['properties']['explanation_text'] = [
                'description' =>  'Text of explanation.',
                'type' => 'STRING'
            ];
        }
        return $responseSchema;
    }
}
