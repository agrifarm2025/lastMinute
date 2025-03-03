<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use Psr\Log\LoggerInterface;
use App\Form\ArticleType;
use App\Form\CommentaireType;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/article/new', name: 'article_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $imageName = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move($this->getParameter('upload_directory'), $imageName);
                $article->setImage('uploads/' . $imageName);
            }

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article created successfully!');
            return $this->redirectToRoute('article_list');
        }

        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/article/{id}/edit', name: 'article_edit')]
    public function edit(Request $request,Article $article, EntityManagerInterface $entityManager): Response
    {
        // Create the form for editing the article
        $form = $this->createForm(ArticleType::class, $article);
    
        // Store the old image for later deletion if needed
        $oldImage = $article->getImage();
    
        // Handle the request and process the form submission
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the new image if uploaded
            $image = $form->get('image')->getData();
            
            if ($image) {
                // Generate a new filename and upload the image
                $imageName = md5(uniqid()) . '.' . $image->guessExtension();
                $uploadPath = $this->getParameter('upload_directory'); // The directory to store images
                $image->move($uploadPath, $imageName);
                $article->setImage('uploads/' . $imageName);
    
                // Optionally delete the old image if necessary
                if ($oldImage) {
                    $oldImagePath = $uploadPath . '/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            } else {
                // If no new image, keep the old image
                $article->setImage($oldImage);
            }
    
            // Save the changes to the database
            $entityManager->flush();
    
            // Flash message for successful update
            $this->addFlash('success', 'Article updated successfully!');
    
            // Redirect to the article show page
            return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
        }
    
        // Render the edit form
        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }
    
#[Route('/article/{id}', name: 'article_show')]
public function show(Article $article, Request $request, CommentaireRepository $commentaireRepository, EntityManagerInterface $entityManager): Response
{
    // Create a new Commentaire object
    $comment = new Commentaire();

    // Create the form for adding a new comment
    $formCommentaire = $this->createForm(CommentaireType::class, $comment);

    // Handle the form submission
    $formCommentaire->handleRequest($request);

    // Check if the form is submitted and valid
    if ($formCommentaire->isSubmitted() && $formCommentaire->isValid()) {
        // Set the article for the comment (relation with Article)
        $comment->setArticle($article);

        // Persist the comment entity (save it to the database)
        $entityManager->persist($comment);
        $entityManager->flush();

        // Redirect or render the article with the comment
        return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
    }

    // Render the article page with the form
    return $this->render('article/show.html.twig', [
        'article' => $article,
        'formCommentaire' => $formCommentaire->createView(),
    ]);
}

    #[Route('/articles', name: 'article_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Article::class)->findAll();

        return $this->render('article/list.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/{id}/delete', name: 'article_delete', methods: ['POST'])]
public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
{
    if (!$article) { 
        $this->addFlash('error', 'Article not found.');
        return $this->redirectToRoute('article_list');
    }

    foreach ($article->getCommentaire() as $commentaire) {
        $entityManager->remove($commentaire);
    }

    $entityManager->remove($article);
    $entityManager->flush();

    $this->addFlash('success', 'Article and related comments deleted successfully.');
    
    return $this->redirectToRoute('article_list'); 
}


#[Route('/articlesback', name: 'article_listback')]
public function listback(EntityManagerInterface $entityManager): Response
{
    $articles = $entityManager->getRepository(Article::class)->findAll();

    return $this->render('article/ListArticleBack.html.twig', [
        'articles' => $articles,
    ]);
}
#[Route('/article/delete/{id}', name: 'article_deleteback')]
public function deleteback(EntityManagerInterface $entityManager, int $id): Response
{
    $article = $entityManager->getRepository(Article::class)->find($id);

    if ($article) {
        $entityManager->remove($article);
        $entityManager->flush();
    }

    return $this->redirectToRoute('article_listback');
}

}