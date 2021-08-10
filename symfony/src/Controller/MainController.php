<?php

namespace App\Controller;

use App\Form\ProfileType;
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
        $user = $this->getUser();
        return $this->render('main/index.html.twig',[
            'user' => $user
        ]);
    }

   

    /**
     * @Route("/profile/{email}", name="profile")
     * *@param User $user
     */

    public function profile(Request $request,$email){
        
        $user = $this->getUser();

       

        $form = $this->createForm(ProfileType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            //entity manager
            $em = $this->getDoctrine()->getManager();

           

            $em->persist($user);
            $em->flush();

            $this->addFlash('success','info updated');

            return $this->redirect($this->generateUrl('post.index'));
        }

        

      
       
        return $this->render('main/custom.html.twig',[
            'name' => $email,
            'user' => $user,
            'form'=>$form->createView()
        ]);

    }


}
