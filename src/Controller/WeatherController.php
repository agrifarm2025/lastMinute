<?php
namespace App\Controller;

use App\Service\WeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    private $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    #[Route("/weather/{lon}/{lat}", name: "weather")]
    public function forecastByCoordinates(float $lat, float $lon): Response
    {
        // Fetch the weather forecast using latitude and longitude
        $forecast = $this->weatherService->getForecastByCoordinates($lat, $lon);

        // Check for catastrophic weather conditions
        $warning = $this->checkForCatastrophicWeather($forecast);

        // Group forecasts by day
        $groupedForecast = $this->groupForecastByDay($forecast);

        // Render the forecast in a Twig template
        return $this->render('weather/index.html.twig', [
            'groupedForecast' => $groupedForecast,
            'warning' => $warning ??null ,
            'description' => $forecast['list'][0]['weather'][0]['description'],

        ]);
    }

    /**
     * Check for catastrophic weather conditions.
     */
    public function checkForCatastrophicWeather(array $forecast): ?string
    {
        $catastrophicConditions = [
            'light rain', 'thunderstorm', 'hail', 'extreme heat', 'frost',
            'strong winds', 'blizzard', 'tornado', 'dense fog', 'extreme cold'
        ];

        foreach ($forecast['list'] as $item) {
            $weatherDescription = strtolower($item['weather'][0]['description']);
            foreach ($catastrophicConditions as $condition) {
                if (str_contains($weatherDescription, $condition)) {
                    return "Warning: Catastrophic weather detected - " . $weatherDescription;
                }
                else {
return null;               }
                }
            }
        }
    

    /**
     * Group forecast data by day.
     */
    private function groupForecastByDay(array $forecast): array
    {
        $groupedForecast = [];
        foreach ($forecast['list'] as $item) {
            $date = date('Y-m-d', $item['dt']);
            $groupedForecast[$date][] = $item;
        }
        return $groupedForecast;
    }
}