<?php

namespace App\Dto\Schema\GoogleGenAi;

class RecipesResponse
{
    // @see https://spec.openapis.org/oas/v3.0.3#schema-object
    public static array $schema = [
        'description' =>  'List 5 popular german meat recipes by including the following properties. Don\'t change properties and don\'t add other properties. Use german language for output.',
        'type' => 'ARRAY',
        'items' => [
            'type' => 'OBJECT',
            'properties' => [
                'name' => [
                    'description' =>  'Names of recipe.',
                    'type' => 'STRING'
                ],
                'ingredients' => [
                    'description' =>  'Requirement ingredients for running the recipe.',
                    'type' => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'name' => [
                                'description' =>  'Requirement ingredient for running the recipe.',
                                'type' => 'STRING'
                            ],
                            'amount' => [
                                'description' =>  'Requirement amount of ingredient for running the recipe with unit. Unit is grams or entities.',
                                'type' => 'STRING'
                            ],
                            'cost' => [
                                'description' =>  'Cost of requirement ingredient for running the recipe. Unit is euro.',
                                'type' => 'NUMBER'
                            ]
                        ],
                        'required' => ['name', 'amount', 'cost'],
                        'propertyOrdering' => ['name', 'amount', 'cost']
                    ]
                ],
                'total_costs' => [
                    'description' =>  'Total cost of ingredients for running the recipe. Unit is euro.',
                    'type' => 'NUMBER'
                ],
                'cooking_instruction' => [
                    'description' =>  'Cooking instructions for running the recipe.',
                    'type' => 'STRING'
                ],
                'cooking_time' => [
                    'description' =>  'Total cooking time for running the recipe. Units are hours and minutes.',
                    'type' => 'STRING'
                ],
            ],
            'required' => ['name', 'ingredients', 'total_costs', 'cooking_instruction', 'cooking_time'],
            'propertyOrdering' => ['name', 'ingredients', 'total_costs', 'cooking_instruction', 'cooking_time']
        ]
    ];
}
