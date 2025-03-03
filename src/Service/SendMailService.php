<?php
// src/Service/SendMailService.php
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendMailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $context
    ): void {
        // Create the email
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("emails/$template.html.twig")
            ->context($context);

        // Send the email
        $this->mailer->send($email);
    }

    public function sendEmailWithCode(string $to, string $code): void
    {
        $this->send(
            'janmedali3@gmail.com', // From
            $to, // To
            'Your Verification Code', // Subject
            'verification_code', // Template name (without .html.twig)
            ['code' => $code] // Context (variables passed to the template)
        );
    }
}