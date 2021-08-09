<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SearchMovieController extends AbstractController
{

    private $client;
    /**
     * @Route("/search/{name}", name="search_movie")
     * @return Response
     * @param string $name
     */
    public function index(HttpClientInterface $client,string $name): Response
    {
        $this->client=$client;
        $info=$this->fetchInformation($name);

        $data = json_decode($info);

        return $this->render('search_movie/index.html.twig', [
            'info' => $data->results,
        ]);
    }

    public function fetchInformation(string $name)
    {
        
        $response = $this->client->request(
            'GET',
            'https://imdb-api.com/en/API/SearchMovie/k_gk1mupnb/'.$name
         
        );

        $statusCode = $response->getStatusCode();
          $contentType = $response->getHeaders()['content-type'][0];
          $content = $response->getContent();
         
         
        return $content;

        
    }
}
