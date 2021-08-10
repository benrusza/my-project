<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Form\PostTypeEdit;
use App\Repository\PostRepository;
use Doctrine\DBAL\Query\QueryBuilder;
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
        $user = $this->getUser();
        $posts = $postRepository->findAll();
        

        return $this->render('post/index.html.twig', [
           'posts' => $posts,
           'user' => $user
        ]);
    }

     /**
     * @Route("/create", name="create")
     * 
     */
    public function create(Request $request){

        $post = new Post();
        $user = $this->getUser();
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
            'user'=>$user,
            'form'=>$form->createView()
        ]);

    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Request $request,Post $post){

        $user = $this->getUser();
        $form = $this->createForm(PostTypeEdit::class, $post);

        $form->handleRequest($request);
        

        if($form->isSubmitted()){
            //entity manager
            $em = $this->getDoctrine()->getManager();

            
            

            $em->persist($post);
            $em->flush();
            $this->addFlash('success','info updated');
            return $this->redirect($this->generateUrl('post.index'));
        }

        

        return $this->render('post/edit.html.twig',[
            'user'=>$user,
            'form'=>$form->createView()
        ]);

    }

    

    /**
     * @Route("/show/{id}", name="show",)
     * @return Response
     * @param Post $post
     */

    public function show(Post $post){
        $user = $this->getUser();
 
        return $this->render('post/show.html.twig',[
            'post' => $post,
            'user' => $user
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

    /**
     * @Route("/rent/{id}", name="rent",)
     
     */
    public function rent(Post $post){

        if($post->getStock()>0){
            $user = $this->getUser();
            if($user->getMovieRentId()==0 || $user->getMovieRentId()==null){


                $em = $this->getDoctrine()->getManager();

                $post->setStock($post->getStock()-1);

                $em->merge($post);
                $em->flush();

                $user->setMovieRentId($post->getId());

                $em->merge($user);
                $em->flush();

                $this->addFlash('success','rented movie '.$post->getTitle());
            }else{
                $this->addFlash('success','you already rented a movie!');
            }
            


        }else{
            $this->addFlash('success','no stock of '.$post->getTitle().' now');
        }

        return $this->redirect($this->generateUrl('post.index'));
        

    }
    
     /**
     * @Route("/putBackMovie/{id}", name="putBackMovie",)
     *@param Post $post
     */

    public function putBackMovie(Post $post){
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $post->setStock($post->getStock()+1);

        $em->merge($post);
        $em->flush();

        $user->setMovieRentId(0);

        $em->merge($user);
        $em->flush();
        $this->addFlash('success','returned');
            return $this->redirect($this->generateUrl('post.index'));

     }


}
