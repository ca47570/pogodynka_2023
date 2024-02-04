<?php

namespace App\Controller;

use App\Entity\Measurement;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'app_weather_api')]
    public function index(#[MapQueryParameter] string $city,#[MapQueryParameter] string $country,
                          #[MapQueryParameter] string $format,
                          #[MapQueryParameter('twig')] ?bool $twig = false,
                          WeatherUtil $util): Response
    {
        $measurements = $util->getWeatherForCountryAndCity($country, $city);
        if (strtolower($format) === 'json') {
            if($twig === true){
                return $this->render('weather_api/index.json.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ]);
            }
            return $this->json([
                'city' => $city,
                'country' => $country,
                'measurements' => array_map(fn(Measurement $m) => [
                    'date' => $m->getDate()->format('Y-m-d'),
                    'celsius' => $m->getCelsius(),
                ], $measurements),
            ]);
        }elseif(strtolower($format) === 'csv'){
            if($twig === true){
                return $this->render('weather_api/index.csv.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ]);
            }
            $csv = "city,country,date,celsius<br>";
            $csv .= implode(
                "<br>",
                array_map(fn(Measurement $m) => sprintf(
                    '%s,%s,%s,%s',
                    $city,
                    $country,
                    $m->getDate()->format('Y-m-d'),
                    $m->getCelsius(),
                ), $measurements)
            );

            return new Response($csv, 200, [
            ]);
        }else{
            if($twig === true){
                return $this->render('weather_api/index.json.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ]);
            }
            return $this->json([
                'city' => $city,
                'country' => $country,
                'measurements' => array_map(fn(Measurement $m) => [
                    'date' => $m->getDate()->format('Y-m-d'),
                    'celsius' => $m->getCelsius(),
                ], $measurements),
            ]);
        }
    }

}
