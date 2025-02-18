<?php
namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    // Route to view all comments related to a specific article
    #[Route('/article/{articleId}/comments', name: 'app_commentaire_index', methods: ['GET'])]
    public function index(int $articleId, CommentaireRepository $commentaireRepository): Response
    {
        $comments = $commentaireRepository->findBy(['article' => $articleId]);

        return $this->render('commentaire/index.html.twig', [
            'comments' => $comments,
        ]);
    }

    // Route to add a new comment
    #[Route('/article/{articleId}/comment/new', name: 'app_commentaire_new', methods: ['GET', 'POST'])]
    public function new(int $articleId, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a new Commentaire object and associate it with the article
        $commentaire = new Commentaire();
        $commentaire->setArticle($entityManager->getRepository(Article::class)->find($articleId));

        // Create the form for the comment
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            

            // Persist the comment to the database
            $entityManager->persist($commentaire);
            $entityManager->flush();

            // Redirect back to the article's comment list
            return $this->redirectToRoute('app_commentaire_index', ['articleId' => $articleId]);
        }

        return $this->render('commentaire/new.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form->createView(),
        ]);
    }

    // Route to delete a comment
    #[Route('/comment/{id}/delete', name: 'app_commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        // Check the CSRF token for delete confirmation
        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {
            // Remove the comment from the database
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }

        // Redirect back to the article's comment list
        return $this->redirectToRoute('app_commentaire_index', ['articleId' => $commentaire->getArticle()->getId()]);
    }
}
