<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @Route("/search", name="search.")
 */
class SearchMovieController extends AbstractController
{

    private $client;
    /**
     * @Route("/{name}", name="search_movie")
     * @return Response
     * @param string $name
     */
    public function index(HttpClientInterface $client,string $name): Response
    {
        $user = $this->getUser();
        $this->client=$client;
        $info=$this->fetchInformation($name);

        $data = json_decode($info);

        return $this->render('search_movie/index.html.twig', [
            'info' => $data->results,
            'user' => $user
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
         
         // var_dump($content);
        return $content;

        
    }

    /**
     * @Route("/create/{name}", name="create")
     * * @return Response
     * @param string $name
     */
    public function create(HttpClientInterface $client, string $name){
        $this->client=$client;
        $info=$this->fetchInformation($name);

        $data = json_decode($info);

        $post = new Post();
        $user = $this->getUser();
        $year=$data->results[0]->description;
        preg_match_all('!\d+!', $year, $year);
        var_dump($year);
        $year = $year[0][0];

        $post->setTitle($data->results[0]->title);
        $post->setImage($data->results[0]->image);
        $post->setYear($year);
        $post->setStock(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        return $this->redirect($this->generateUrl('post.index'));

        
        }

     
}
