<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post", name="post.")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PostRepository $postRepository): Response
    {

        $posts = $postRepository->findAll();
        

        return $this->render('post/index.html.twig', [
           'posts' => $posts,
        ]);
    }

     /**
     * @Route("/create", name="create")
     */
    public function create(Request $request){

        $post = new Post();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            //entity manager
            $em = $this->getDoctrine()->getManager();

            /**@var UploadedFile $file */
            $file =$request->files->get('post')['image'];

            if($file){
                $filename=md5(uniqid()).'.'. $file->guessClientExtension();

                $file->move(
                    $this->getParameter('uploads_dir'),
                    $filename
                );

                $post->setImage($filename);
            }


            $em->persist($post);
            $em->flush();

            return $this->redirect($this->generateUrl('post.index'));
        }

        

        return $this->render('post/create.html.twig',[
            'form'=>$form->createView()
        ]);

    }

    /**
     * @Route("/show/{id}", name="show",)
     * @return Response
     * @param Post $post
     */

    public function show(Post $post){

 
        return $this->render('post/show.html.twig',[
            'post' => $post
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete",)
     
     */
    public function delete(Post $post){

        $em = $this->getDoctrine()->getManager();

        $em->remove($post);
        $em->flush();
        $this->addFlash('success','post deleted');

        return $this->redirect($this->generateUrl('post.index'));
    }

}
