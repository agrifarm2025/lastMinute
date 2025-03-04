<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\TransportException;

class CropInfoService
{
    private HttpClientInterface $client;
    private LoggerInterface $logger;
    private string $baseUrl;

    public function __construct(HttpClientInterface $client, LoggerInterface $logger, string $baseUrl = 'https://api-cropcalendar.apps.fao.org/api/v1')
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Fetches crop information for Tunisia.
     *
     * @return array Crop details including name, scientific name, family, and images.
     */
    public function getCropsForTunisia(): array
    {
        $apiUrl = sprintf('%s/countries/TN/crops?language=en', $this->baseUrl);

        try {
            $response = $this->client->request('GET', $apiUrl);

            if ($response->getStatusCode() !== 200) {
                $this->logger->error("Failed to fetch crop info: HTTP " . $response->getStatusCode());
                return [];
            }

            return $response->toArray();
        } catch (ClientException | TransportException $e) {
            $this->logger->error("API Request Failed: " . $e->getMessage());
            return [];
        }
    }
}

