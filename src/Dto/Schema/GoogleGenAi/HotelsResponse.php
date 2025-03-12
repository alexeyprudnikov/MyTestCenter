<?php

namespace App\Dto\Schema\GoogleGenAi;

class HotelsResponse
{
    // @see https://spec.openapis.org/oas/v3.0.3#schema-object
    public static array $schema = [
        'description' =>  'Think of 5 hotels from Tolkien\'s \'The Lord of the Rings\' Middle-earth by including the following properties. Don\'t change properties and don\'t add other properties. Use german language for output.',
        'type' => 'ARRAY',
        'items' => [
            'type' => 'OBJECT',
            'properties' => [
                'name' => [
                    'description' =>  'Name of hotel.',
                    'type' => 'STRING'
                ],
                'location' => [
                    'description' =>  'Location of hotel. Format: place, country.',
                    'type' => 'STRING'
                ],
                'description' => [
                    'description' =>  'Description of hotel. Should be between 100 and 200 characters long.',
                    'type' => 'STRING'
                ],
                'dangers' => [
                    'description' =>  'Warning about dangerous creatures you may encounter near the hotel. Should be maximal 100 characters long.',
                    'type' => 'STRING'
                ],
                'features' => [
                    'description' =>  'Features of hotel.',
                    'type' => 'OBJECT',
                    'items' => [
                        'type' => 'ARRAY',
                        'properties' => [
                            'title' => [
                                'description' =>  'Title of feature.',
                                'type' => 'STRING'
                            ],
                            'icon' => [
                                'description' =>  'Bootstrap icon class name with bi- prefix.',
                                'type' => 'STRING'
                            ],
                        ],
                        'required' => ['title', 'icon'],
                        'propertyOrdering' => ['title', 'icon']
                    ]
                ],
            ],
            'required' => ['name', 'location', 'description', 'features'],
            'propertyOrdering' => ['name', 'location', 'description', 'features']
        ]
    ];
}
