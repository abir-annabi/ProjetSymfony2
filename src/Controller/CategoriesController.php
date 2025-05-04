<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

final class CategoriesController extends AbstractController
{
    #[Route('admin/categories', name: 'admin/categories')]
    public function index(CategoriesRepository $rep): Response
    {
        $categories=$rep->findAll();
        return $this->render('categories/index.html.twig', [
            'categories'=>$categories
        ]);
    }


    #[Route('admin/categories/create', name: 'app_categories')]
    public function create(EntityManagerInterface $em,Request $request): Response
    {
        $categorie=new Categories();
        $form=$this->createForm(CategoriesType::class,$categorie);
        $form->handleRequest($request); //yasmaa ken fama des données soumises ou non __ Cette ligne permet à Symfony de remplir l’objet $categorie avec les données soumises 
        if($form->isSubmitted() && $form->isValid()){
            //dd($categorie);
            $em->persist($categorie);
            $em->flush();
            $this->addFlash('success',"la catégorie ".$categorie->getLibelle()." a été ajoutée avec succées");
            //$this->addFlash('success',"un mail est envoyée a l'admin");
            return $this->redirectToRoute('admin/categories');
        }


        return $this->render('categories/create.html.twig',['form'=>$form]);
        
    }





    #[Route('admin/categories/update/{id}', name: 'app_categories/update')]
    public function update(EntityManagerInterface $em,Request $request, Categories $categorie): Response
    {
        $form=$this->createForm(CategoriesType::class,$categorie);
        $form->handleRequest($request); //yasmaa ken fama des données soumises ou non __ Cette ligne permet à Symfony de remplir l’objet $categorie avec les données soumises 
        if($form->isSubmitted() && $form->isValid()){
            //dd($categorie);
            $em->flush();
            $this->addFlash('success',"la catégorie ".$categorie->getLibelle()." a été modifié avec succées");
            //$this->addFlash('success',"un mail est envoyée a l'admin");
            return $this->redirectToRoute('admin/categories');
        }


        return $this->render('categories/create.html.twig',['form'=>$form]);
        
    }


}
