<?php

namespace App\Controller;

use App\Entity\Livres;
use App\Form\LivresType;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LivresController extends AbstractController
{



    #[Route('/admin/livre/delete/{id}', name: 'app_livre_delete')]
    public function delete(LivresRepository $rep,Livres $livre):Response
    {
  //$livre=$rep->find($id);
  $rep->remove($livre);
  $rep->flush();
        dd($livre);// livre supprimé = id null
    }


    #[Route('/admin/livres', name: 'app_livre_all')]
    public function all(LivresRepository $rep, PaginatorInterface $paginator, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $query = $rep->createQueryBuilder('l')->getQuery();
        $livres = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            8
        );
        
        return $this->render('livres/all.html.twig', [
            'livres' => $livres
        ]);
    }


    #[Route('/livres', name: 'app_livre_all1')]
    public function all1(LivresRepository $rep, PaginatorInterface $paginator, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $query = $rep->createQueryBuilder('l')->getQuery();
        $livres = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            8
        );
        
        return $this->render('livres/index.html.twig', [
            'livres' => $livres
        ]);
    }




    #[Route('/admin/livre/show3', name: 'app_livre_show3')]
    public function show3(LivresRepository $rep):Response
    {
        $livre=$rep->findBy(['titre'=>'titre 1'],['editeur'=>'editeur 1'],/*['prix'=>'ASC']*/);
        if(!$livre){
            throw $this->createNotFoundException('le livre n\'existe pas');
        }
        dd($livre);
    }


    #[Route('/admin/livre/show2', name: 'app_livre_show2')]
    public function show2(LivresRepository $rep):Response
    {
        $livre=$rep->findOneBy(['titre'=>'titre 1']);
        if(!$livre){
            throw $this->createNotFoundException('le livre n\'existe pas');
        }
        dd($livre);
    }


//paramconverter
    #[Route('/admin/livre/show/{id}', name: 'app_livre_show')]
    public function show(Livres $livre):Response
    {
        
        if(!$livre){
            throw $this->createNotFoundException("le livre ayant id : {$livre->getId()} n\'existe pas");
        }
        return $this->render('livres/show.html.twig', ['livre'=>$livre]);    }    


    

  /*  #[Route('/admin/livre', name: 'app_livre_create')]
    public function create(EntityManagerInterface $em):Response //EntityManager ($em) qui sert à gérer les entités Doctrine (création, mise à jour, suppression, etc.).
    {
        $livre=new Livres();
        $livre->setTitre('titre 1')
              ->setSlug('slug 1')
              ->setImage('https://images.app.goo.gl/P42ziByspY6PYLh97')
              ->setResume('Lorem ipsum dolor sit amet consectetur adipisicing elit. Asperiores, umque deleniti harum? Blanditiis a cupiditate ipsum dignissimos quos!')
              ->setEditeur('editeur 1')
              ->setDateEdition(new \DateTime(datetime: "2024-03-10"))
              ->setPrix(12.5)
              ->setIsbn('599-548-1486-45');
              $em->persist($livre);//ne permet pas d'inserer dans la BD
              $em->flush();// permet  d'inserer dans la BD
              dd($livre);

    }*/



    #[Route('admin/Livres/create', name: 'app_Livres_create')]
    public function create(EntityManagerInterface $em,Request $request): Response
    {
        $livre=new Livres();
        $form=$this->createForm(LivresType::class,$livre);
        $form->handleRequest($request); //yasmaa ken fama des données soumises ou non __ Cette ligne permet à Symfony de remplir l’objet $categorie avec les données soumises 
        if($form->isSubmitted() && $form->isValid()){
            //dd($categorie);
            $em->persist($livre);
            $em->flush();
            $this->addFlash('success',"le livre a été ajoutée avec succées");
            //$this->addFlash('success',"un mail est envoyée a l'admin");
            return $this->redirectToRoute('admin/livres');
        }


        return $this->render('livres/create.html.twig',['form'=>$form]);
        
    }
}
