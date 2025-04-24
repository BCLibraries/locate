<?php

namespace App\Controller;

use App\Service\EmailService\Emailer;
use App\Service\MessageWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    private Emailer $mailer;
    private MessageWriter $message_writer;

    public function __construct(Emailer $mailer, MessageWriter $message_writer)
    {
        $this->mailer = $mailer;
        $this->message_writer = $message_writer;
    }

    /**
     * @Route("/email", name="email",  methods={"POST"})
     */
    public function index(Request $request): Response
    {
        $email = $request->request->get('email');
        $library = $request->request->get('library');
        $call_number = $request->request->get('call_number');
        $title = $request->request->get('title');

        $message = $this->message_writer->createMessage($library, $call_number, $title);
        $this->mailer->send($email, $message, "BC Libraries book - $title");

        return $this->render('sms/index.html.twig', [
            'controller_name' => 'EmailController',
        ]);
    }
}
