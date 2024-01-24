<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LastCallController extends AbstractController
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/last_call', name: 'app_last_call')]
    public function index(): Response
    {
        return $this->render('last_call/index.html.twig', [
            'controller_name' => 'LastCallController',
        ]);
    }

    #[Route('/last_call/station/{stop_area_id}/{stop_area_name}', name: 'app_last_call_station')]
    public function station(string $stop_area_id, string $stop_area_name): Response
    {
        $response = $this->client->request(
            'GET',
            'https://api.navitia.io/v1/coverage/fr-idf/stop_areas/'.$stop_area_id.'/departures?',
            [
                'headers' => [
                    'Authorization' => 'Basic MWI1YTUxNDItNjQzNS00MzI3LWFmNDMtNWM3MTBiMDVkOWMwOg==',
                ],
            ]
        );

        return $this->render('last_call/station.html.twig', [
            'controller_name' => 'LastCallController',
            'stop_area' => $response->toArray(),
            'stop_area_id' => $stop_area_id,
            'stop_area_name' => $stop_area_name
        ]);
    }
}