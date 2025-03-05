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
    #[Route('/article/{articleId}/comments', name: 'app_commentaire_index', methods: ['GET'])]
    public function index(int $articleId, CommentaireRepository $commentaireRepository, EntityManagerInterface $entityManager): Response
{
    // Récupérer l'article par ID avec l'EntityManager
    $article = $entityManager->getRepository(Article::class)->find($articleId);

    if (!$article) {
        throw $this->createNotFoundException('Article not found');
    }

    // Récupérer les commentaires associés à l'article
    $comments = $commentaireRepository->findBy(['article' => $article]);

    // Passer la variable 'comments' à la vue
    return $this->render('commentaire/index.html.twig', [
        'comments' => $comments,
        'article' => $article, // Passer l'article à la vue si vous en avez besoin
    ]);
}
    // Route to add a new comment
   #[Route('/article/{articleId}/comment/new', name: 'app_commentaire_new', methods: ['GET', 'POST'])]
public function new(int $articleId, Request $request, EntityManagerInterface $entityManager): Response
{
    // Créez un nouveau commentaire et associez-le à l'article
    $commentaire = new Commentaire();
    $article = $entityManager->getRepository(Article::class)->find($articleId);
    $commentaire->setArticle($article);

    // Créez le formulaire
    $form = $this->createForm(CommentaireType::class, $commentaire);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Persistez le commentaire dans la base de données
        $entityManager->persist($commentaire);
        $entityManager->flush();

        // Redirigez l'utilisateur vers la liste des commentaires
        return $this->redirectToRoute('app_commentaire_index', ['articleId' => $articleId]);
    }

    // Affichez le formulaire avec les erreurs de validation
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
