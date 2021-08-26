<?php

namespace App\Controller;

use App\Service\SMSService\SMSClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SMSController extends AbstractController
{
    private SMSClient $client;

    public function __construct(SMSClient $client)
    {
        $this->client = $client;
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

        $this->client->send($phone, $library, $call_number, $title);

        return $this->render('sms/index.html.twig', [
            'controller_name' => 'SMSController',
        ]);
    }
}
