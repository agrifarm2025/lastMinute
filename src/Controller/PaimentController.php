<?php

namespace App\Controller;

use App\Entity\Commande;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class PaimentController extends AbstractController
{
    #[Route('/valider-commande', name: 'app_paiment')]
    public function index(): Response
    {
        return $this->render('paiment/index.html.twig', [
            'controller_name' => 'PaimentController',
        ]);
    }

    #[Route('/checkout/{id}', name: 'checkout')]
    public function checkout(EntityManagerInterface $em, string $stripeSK, int $id): Response
    {
        $commande = $em->getRepository(Commande::class)->find($id);
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }

        Stripe::setApiKey($stripeSK);
        $session = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Commande #' . $commande->getId(),
                    ],
                    'unit_amount' => (int) ($commande->getPrix() * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }

    #[Route('/success_url', name: 'success_url')]
    public function success_url(EntityManagerInterface $em): Response
    {
        $commande = $em->getRepository(Commande::class)->findOneBy([], ['id' => 'DESC']);
        if (!$commande) {
            throw $this->createNotFoundException('Aucune commande trouvée.');
        }

        $pdfContent = $this->genererFacture($commande, $commande->getProduits(), $commande->getPrix());

        $commande->setStatus('Approuvé');
        $em->flush(); // Sauvegarde dans la base de données

        // Sauvegarder le PDF temporairement (optionnel)
        $pdfFilePath = $this->getParameter('kernel.project_dir') . '/public/factures/facture_' . $commande->getId() . '.pdf';
        file_put_contents($pdfFilePath, $pdfContent);

        // Rediriger vers la liste des commandes après le paiement réussi
        return $this->redirectToRoute('liste_commandes');
    }

    #[Route('/cancel_url', name: 'cancel_url')]
    public function cancel_url(): Response
    {
        return $this->render('paiment/cancel_url.html.twig');
    }

    #[Route('/generer-facture/{id}', name: 'generer_facture')]
    public function genererFactureAction(EntityManagerInterface $em, int $id): Response
    {
        $commande = $em->getRepository(Commande::class)->find($id);
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }

        $pdfContent = $this->genererFacture($commande, $commande->getProduits(), $commande->getPrix());

        // Créer une réponse avec le contenu du PDF
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="facture_' . $commande->getId() . '.pdf"');

        return $response;
    }

    #[Route('/liste-commandes', name: 'liste_commandes')]
public function listeCommandes(EntityManagerInterface $em, Security $security): Response
{
    // Récupérer l'utilisateur connecté
    $user = $security->getUser();

    if (!$user instanceof UserInterface) {
        throw $this->createAccessDeniedException("Vous devez être connecté pour voir vos commandes.");
    }

    // Filtrer les commandes pour ne récupérer que celles de l'utilisateur connecté
    $commandes = $em->getRepository(Commande::class)->findBy(['user' => $user]);

    return $this->render('commande/liste.html.twig', [
        'commandes' => $commandes,
    ]);
}

private function genererFacture(Commande $commande, Collection $produitsCommande, float $total): string
{
    $html = $this->renderView('commande/facture.html.twig', [
        'commande' => $commande,
        'produitsCommande' => $produitsCommande,
        'total' => $total,
        'adress' => $commande->getAdress(),
        'dateCommande' => $commande->getDateCreationCommande()->format('Y-m-d H:i:s'), // Formater la date ici
    ]);

    // Vérifier l'output HTML pour éviter les erreurs de rendu
    file_put_contents('facture_debug.html', $html);

    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isHtml5ParserEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return $dompdf->output();
}
}