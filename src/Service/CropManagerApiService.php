<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CropManageApiService
{
    private $client;
    private $accessToken;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->authenticate(); // Automatically authenticate when service is initialized
    }

    // 🔹 Authenticate and get access token
    private function authenticate()
    {
        $response = $this->client->request('POST', 'https://api.cropmanage.ucanr.edu/Token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'username' => 'janmedali3@gmail.com', // Replace with your API username
                'password' => 'b!V2PjkQNftgCe6', // Replace with your API password
                'grant_type' => 'password',
            ],
        ]);

        $data = $response->toArray();
        $this->accessToken = $data['access_token'] ?? null;
    }

    // 🔹 Fetch soil types and remove duplicates
    public function getSoilTypes()
    {
        $response = $this->client->request('GET', 'https://api.cropmanage.ucanr.edu/v2/soil-types.json', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
        ]);

        $soilTypes = $response->toArray(); // Convert JSON response to PHP array

        // 🔹 Remove duplicate names
        $seenNames = [];
        $uniqueSoilTypes = [];
    
        foreach ($soilTypes as $soil) {
            $nameLower = strtolower($soil['Name']); // Convert to lowercase for case-insensitive comparison
    
            if (!in_array($nameLower, $seenNames, true)) {
                $uniqueSoilTypes[] = $soil;
                $seenNames[] = $nameLower; // Store lowercase version to prevent duplicates
            }
        }
    
        return $uniqueSoilTypes; // // Return filtered soil types
    }
}
