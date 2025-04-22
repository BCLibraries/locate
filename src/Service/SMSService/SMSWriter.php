<?php

namespace App\Service\SMSService;

use App\Repository\ShelfRepository;
use App\Service\CallNoNormalizer\LCNormalizer;
use App\Service\MessageWriter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SMSWriter
{
    private HttpClientInterface $client;

    private static array $valid_libraries = ['onl', 'law'];
    private ShelfRepository $shelf_repository;
    private LCNormalizer $normalizer;
    private UrlGeneratorInterface $router;
    private string $clickatell_password;
    private string $clickatell_id;

    public function __construct(
        HttpClientInterface $client,
        string              $clickatell_password,
        string              $clickatell_id
    )
    {
        $this->client = $client;
        $this->clickatell_password = $clickatell_password;
        $this->clickatell_id = $clickatell_id;
    }

    public function send(string $phone_number, string $message)
    {
        if ($this->isNotValidPhoneNumber($phone_number)) {
            throw new \Exception("$phone_number is not a valid North American phone number");
        }

        $url_base = 'http://api.clickatell.com/http/sendmsg';
        $this->client->request('POST', $url_base, [
                'body' => [
                    'text' => $message,
                    'user' => 'bclibraries',
                    'password' => $this->clickatell_password,
                    'api_id' => $this->clickatell_id,
                    'from' => '16462573676',
                    'to' => "1{$phone_number}",
                    'mo' => "1"
                ]
            ]
        );
    }

    private function isNotValidPhoneNumber(string $phone_number): bool
    {
        // Strip non-numerics.
        $phone_number = preg_replace('/\D/', '', $phone_number);

        // Strip leading 1 if they added it.
        if (strlen($phone_number) === 11 && substr($phone_number, 0, 1) === '1') {
            $phone_number = substr($phone_number, 1);
        }

        // It's a legit phone number if it has ten digits and the first one isn't one.
        return strlen($phone_number) !== 10 || substr($phone_number, 0, 1) === '1';
    }
}
