<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Twig\Environment;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index()
    {

        return $this->render('main/index.html.twig');
    }

    /**
     * @Route("/custom/{name?}", name="custom")
     */

    public function custom(Request $request,$name){

        //dump($request->get(key:'name'));
       
        return $this->render('main/custom.html.twig',[
            'name'=>$name
        ]);

    }


}
