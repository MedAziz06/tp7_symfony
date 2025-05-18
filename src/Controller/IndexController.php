<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Form\CategoryType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Entity\CategorySearch;
use App\Form\CategorySearchType;
use App\Entity\PriceSearch;
use App\Form\PriceSearchType;

class IndexController extends AbstractController
{
    #[Route('/', name: 'article_list')]
    public function index(Request $request, ArticleRepository $articleRepository): Response
    {
        $propertySearch = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $propertySearch);
        $form->handleRequest($request);
        
        // Initialement le tableau des articles est vide,
        // c.a.d on affiche les articles que lorsque l'utilisateur
        // clique sur le bouton rechercher
        $articles = [];
        
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère le nom d'article tapé dans le formulaire
            $nom = $propertySearch->getNom();
            
            if ($nom != "") {
                // Si on a fourni un nom d'article on affiche tous les articles ayant ce nom
                $articles = $articleRepository->findBy(['nom' => $nom]);
            } else {
                // Si aucun nom n'est fourni on affiche tous les articles
                $articles = $articleRepository->findAll();
            }
        } else {
            // Si le formulaire n'est pas soumis, afficher tous les articles par défaut
            $articles = $articleRepository->findAll();
        }
        
        return $this->render('articles/index.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles
        ]);
    }

    #[Route('/article/new', name: 'new_article')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article ajouté avec succès');
            return $this->redirectToRoute('article_list');
        }

        return $this->render('articles/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/article/{id}', name: 'article_show')]
    public function show(Article $article): Response
    {
        return $this->render('articles/show.html.twig', [
            'article' => $article
        ]);
    }

    #[Route('/article/edit/{id}', name: 'article_edit')]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Article modifié avec succès');
            return $this->redirectToRoute('article_list');
        }

        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    #[Route('/article/delete/{id}', name: 'article_delete')]
    public function delete(Article $article, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($article);
        $entityManager->flush();
        
        $this->addFlash('success', 'Article supprimé avec succès');
        return $this->redirectToRoute('article_list');
    }

    #[Route('/category/newCat', name: 'new_category')]
    public function newCategory(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
            
            $this->addFlash('success', 'Catégorie ajoutée avec succès');
            return $this->redirectToRoute('category_list');
        }

        return $this->render('categories/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/categories', name: 'category_list')]
    public function categoryList(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('categories/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/category/{id}', name: 'category_show')]
    public function showCategory(Category $category): Response
    {
        return $this->render('categories/show.html.twig', [
            'category' => $category
        ]);
    }

    #[Route('/category/edit/{id}', name: 'category_edit')]
    public function editCategory(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Catégorie modifiée avec succès');
            return $this->redirectToRoute('category_list');
        }

        return $this->render('categories/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    #[Route('/category/delete/{id}', name: 'category_delete')]
    public function deleteCategory(Category $category, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si la catégorie contient des articles
        if (count($category->getArticles()) > 0) {
            $this->addFlash('error', 'Impossible de supprimer cette catégorie car elle contient des articles');
            return $this->redirectToRoute('category_list');
        }
        
        $entityManager->remove($category);
        $entityManager->flush();
        
        $this->addFlash('success', 'Catégorie supprimée avec succès');
        return $this->redirectToRoute('category_list');
    }

    #[Route('/art_cat/', name: 'article_par_cat')]
    public function articlesParCategorie(Request $request, ArticleRepository $articleRepository): Response
    {
        $categorySearch = new CategorySearch();
        $form = $this->createForm(CategorySearchType::class, $categorySearch);
        $form->handleRequest($request);
        
        $articles = [];
        
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $categorySearch->getCategory();
            
            if ($category != "") {
                $articles = $category->getArticles();
            } else {
                $articles = $articleRepository->findAll();
            }
        }
        
        return $this->render('articles/articlesParCategorie.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles
        ]);
    }
    
    #[Route('/art_prix/', name: 'article_par_prix')]
    public function articlesParPrix(Request $request, ArticleRepository $articleRepository): Response
    {
        $priceSearch = new PriceSearch();
        $form = $this->createForm(PriceSearchType::class, $priceSearch);
        $form->handleRequest($request);
        
        $articles = [];
        
        if ($form->isSubmitted() && $form->isValid()) {
            $minPrice = $priceSearch->getMinPrice();
            $maxPrice = $priceSearch->getMaxPrice();
            
            $articles = $articleRepository->findByPriceRange($minPrice, $maxPrice);
        }
        
        return $this->render('articles/articlesParPrix.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles
        ]);
    }
}