<?php

namespace App\Controller;

use App\Service\MessageWriter;
use App\Service\SMSService\SMSWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SMSController extends AbstractController
{
    private SMSWriter $client;
    private MessageWriter $message_writer;

    public function __construct(SMSWriter $client, MessageWriter $message_writer)
    {
        $this->client = $client;
        $this->message_writer = $message_writer;
    }

    /**
     * @Route("/sms", name="sms",  methods={"POST"})
     */
    public function index(Request $request): Response
    {
        $request->getSchemeAndHttpHost();

        $phone = $request->request->get('phone');
        $library = $request->request->get('library');
        $call_number = $request->request->get('call_number');
        $title = $request->request->get('title');

        $message = $this->message_writer->createMessage($library, $call_number, $title);
        $this->client->send($phone, $message);

        return $this->render('sms/index.html.twig', [
            'controller_name' => 'SMSController',
        ]);
    }
}
