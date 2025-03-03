<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class QRCodeController extends AbstractController
{
    #[Route('/generate-qr/{email}', name: 'generate_qr', methods: ['GET'])]
    public function generateQrCode(string $email, UrlGeneratorInterface $urlGenerator): Response
    {
        // Generate the absolute URL for scan-qr
        $url = $urlGenerator->generate('scan_qr', ['email' => $email], UrlGeneratorInterface::ABSOLUTE_URL);

        // Generate QR Code using GoQR API
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($url) . "&size=300x300";

        return new Response(
            "<html><body>
                <h3>Scan the QR Code:</h3>
                <img src='$qrCodeUrl' />
            </body></html>",
            200
        );
    }

    #[Route('/scan-qr', name: 'scan_qr', methods: ['GET', 'POST'])]
    public function scanQrCode(Request $request, SessionInterface $session): Response
    {
        // Get email from the QR code URL
        $email = $request->query->get('email');

        if (!$email) {
            return new JsonResponse(['error' => 'Missing email'], 400);
        }

        // Generate a random 6-digit security code
        $securityCode = random_int(100000, 999999);

        // Store the code in the session
        $session->set('security_code', $securityCode);
        $session->set('email', $email);

        return new Response(
            "<html><body>
                <h3>Your Security Code</h3>
                <p><strong>Code:</strong> $securityCode</p>
                <form method='POST' action='".$this->generateUrl('validate_code')."'>
                    <input type='hidden' name='email' value='$email' />
                    <input type='text' name='entered_code' placeholder='Enter Security Code' required>
                    <button type='submit'>Submit</button>
                </form>
            </body></html>",
            200
        );
    }

    #[Route('/validate-code', name: 'validate_code', methods: ['POST'])]
    public function validateCode(Request $request, SessionInterface $session): Response
    {
        $enteredCode = $request->request->get('entered_code');
        $storedCode = $session->get('security_code');

        if ($enteredCode == $storedCode) {
            // Clear the session code after successful validation
            $session->remove('security_code');

            return $this->redirectToRoute('crop_affichage');
        }

        return new Response(
            "<html><body>
                <h3>Invalid Code</h3>
                <p>The security code you entered is incorrect.</p>
                <a href='".$this->generateUrl('scan_qr', ['email' => $session->get('email')])."'>Try Again</a>
            </body></html>",
            403
        );
    }
}
