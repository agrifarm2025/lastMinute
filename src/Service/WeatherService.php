<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private $httpClient;
    private $apiKey;
    private $apiEndpoint;

    public function __construct(HttpClientInterface $httpClient, string $apiKey, string $apiEndpoint)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->apiEndpoint = $apiEndpoint;
    }

    public function getForecastByCoordinates(float $latitude, float $longitude, string $units = 'metric'): array
    {
        // Use the API endpoint from the configuration
        $response = $this->httpClient->request('GET', $this->apiEndpoint, [
            'query' => [
                'lat' => $latitude,
                'lon' => $longitude,
                'appid' => $this->apiKey,
                'units' => $units,
            ],
        ]);

        // Decode the JSON response into an array
        return $response->toArray();
    }
}