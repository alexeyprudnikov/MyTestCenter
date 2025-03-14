<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DaVinciService
{
    private HttpClientInterface $availabilityClient;
    private array $body;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly SerializerInterface $serializer,
    )
    {
        $this->availabilityClient = $this->httpClient->withOptions(
            (new HttpOptions())
                ->setBaseUri('https://var.ssc-test.davincicloud.de/restbaseaddress/')
                ->setHeaders([
                    'Content-Type' =>'application/json',
                    'Accept' => 'application/json'
                ])
            ->toArray()
        );
        $this->body = [
            "Authentication" => [
                "User" => "IN",
                "Password" => "Web"
            ]
        ];
    }

    public function proceed(): array
    {
        try {
            $request = $this->availabilityClient->request(
                'POST',
                'GetSingleServicesAndAvailabilities',
                [
                    'body' => $this->serializer->encode($this->getAvailabilityBody(), 'json')
                ]
            );
            if ($request->getStatusCode() !== 200) {
                return ['error' => 'bad request'];
            }
            return $this->serializer->decode($request->getContent(), 'json');
        } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function getAvailabilityBody(): array
    {
        return array_merge(
            $this->body, [
                "SearchCriteria" => [
                    "SearchText" => "NMANK",
                    "Agency" => 1,
                    "From" => "/Date(1749754499000)/",
                    "To" => "/Date(1750013699000)/",
                    "Durations" => [
                        5
                    ],
                    "Adults" => 2,
                    "CatalogCode" => "AP25",
                    "ServiceTypeToSearch" => 0
                ],
                "BookingDateTime" => "/Date(1671667200000)/",
                "BookingSource" => 0,
                "ShowNotAvailable" => true,
                "PurchasePrices" => false,
                "IsInteractiveRequest" => true,
                "DoNotCalculatePrices" => false,
                "DoNotReturnServiceTypes" => false
            ]
        );
    }
}
