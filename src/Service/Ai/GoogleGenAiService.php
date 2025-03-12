<?php

namespace App\Service\Ai;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class GoogleGenAiService implements AiServiceInterface
{
    // https://ai.google.dev/gemini-api/docs/text-generation?hl=de&lang=rest
    private const BASE_URL = 'https://generativelanguage.googleapis.com/v1beta/models';
    // flash 2.0 released
    // https://developers.googleblog.com/en/gemini-2-family-expands/
    private const MODEL = 'gemini-1.5-flash';

    private Client $client;
    private array $responseSchema = [];

    public function __construct(
        private readonly string $key
    ) {
        $this->client = new Client();
    }

    public function setResponseSchema(array $responseSchema): self
    {
        $this->responseSchema = $responseSchema;
        return $this;
    }

    /**
     * @return array
     */
    public function request(): array
    {
        if (empty($this->responseSchema)) {
            return ['no response schema defined'];
        }
        $responseSchemaString = json_encode($this->responseSchema);
        $requestBody = [
            'contents' => [
                'parts' => [
                    'text' => "Follow JSON schema.<JSONSchema>{$responseSchemaString}</JSONSchema>"
                ]
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json'
            ]
        ];
        $request = new Request(
            method: 'POST',
            uri: $this->getRequestUrl(),
            headers: [
                'Content-Type' => 'application/json',
            ],
            body: json_encode($requestBody)
        );
        $promise = $this->client->sendAsync($request)->then(
            function (ResponseInterface $response) {
                $content = json_decode($response->getBody()->getContents(), true);
                $text = $content['candidates'][0]['content']['parts'][0]['text'] ?? "{}";
                return json_decode($text, true) ?? ['response decoding error'];
            },
            function (RequestException $e) {
                return [$e->getMessage()];
            }
        );
        return $promise->wait();
    }

    private function getRequestUrl(): string
    {
        return sprintf(
            '%s/%s:generateContent?key=%s',
            self::BASE_URL,
            self::MODEL,
            $this->key,
        );
    }
}
