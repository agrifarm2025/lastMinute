<?php
// src/Controller/SmsController.php
namespace App\Controller;

use App\Entity\Farm;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;

class SmsController extends AbstractController
{
    private $twilioClient;

    public function __construct(Client $twilioClient)
    {
        $this->twilioClient = $twilioClient;
    }

    #[Route("/send-sms/{id}", name:"send_sms")]
    public function sendSms($id,ManagerRegistry $m): Response
    {  
        $em = $m->getManager();
        $farm = $em->getRepository(Farm::class)->find($id);

        try {
            $message = $this->twilioClient->messages->create(
                '+21655771406', // to
                [
                    'from' => '+14347710556',
                    'body' => 'Alert from Farming Assistant: Catastrophic weather detected - '. $farm->getWeather(),
                ]
            );

            return new Response('SMS sent successfully! Message SID: ' . $message->sid);
        } catch (\Exception $e) {
            return new Response('Failed to send SMS: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}