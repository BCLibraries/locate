<?php

namespace App\Service\SMSService;

use App\Repository\ShelfRepository;
use App\Service\CallNoNormalizer\LCNormalizer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SMSClient
{
    private HttpClientInterface $client;

    private static array $valid_libraries = ['onl', 'law'];
    private ShelfRepository $shelf_repository;
    private LCNormalizer $normalizer;
    private UrlGeneratorInterface $router;
    private string $clickatell_password;
    private string $clickatell_id;

    public function __construct(
        HttpClientInterface   $client,
        ShelfRepository       $shelf_repository,
        LCNormalizer          $normalizer,
        UrlGeneratorInterface $router,
        string                $clickatell_password,
        string                $clickatell_id
    )
    {
        $this->client = $client;
        $this->shelf_repository = $shelf_repository;
        $this->normalizer = $normalizer;
        $this->router = $router;
        $this->clickatell_password = $clickatell_password;
        $this->clickatell_id = $clickatell_id;
    }

    public function send(string $phone_number, string $library, string $call_number, string $title)
    {
        if ($this->isNotValidPhoneNumber($phone_number)) {
            throw new \Exception("$phone_number is not a valid North American phone number");
        }

        if ($this->isNotValidLibrary($library)) {
            throw new \Exception("$library is not a valid library code");
        }

        if ($this->isNotValidCallNumber($call_number)) {
            throw new \Exception("$call_number is not a valid call number");
        }

        $url_base = 'http://api.clickatell.com/http/sendmsg';
        $this->client->request('POST', $url_base, [
                'body' => [
                    'text' => $this->createMessage($library, $call_number, $title),
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

    private function isNotValidCallNumber(string $call_number): bool
    {
        return false;
    }

    private function isNotValidLibrary(string $library): bool
    {
        return (!in_array($library, SMSClient::$valid_libraries, true));
    }

    private function cleanTitle(string $title): string
    {
        // @todo verify that the title exists in Alma
        return strip_tags($title);
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

    private function createMessage(string $library, string $call_number, string $title): string
    {
        $clean_title = $this->cleanTitle($title);
        $clean_title = $this->truncateTitle($clean_title);
        $location_string = $this->createLocationString($library, $call_number);

        $url = $this->createURL($library, $call_number, $clean_title);

        return "Boston College Library book $call_number $title is in $location_string $url";
    }

    private function createLocationString(string $library, string $call_number)
    {
        // Find the shelf.
        $normalized_call_number = $this->normalizer->normalize($call_number);
        $shelf = $this->shelf_repository->findOneByLibraryAndCallNumber($library, $normalized_call_number);

        // Build the string.
        $map = $shelf->getMap();
        return $map->getLibrary()->getLabel() . ", " . $map->getLabel() . ", Row " . $shelf->getCode();
    }

    private function truncateTitle($title, $max_chars = 20)
    {
        if (strlen($title) > $max_chars) {
            $last = ($max_chars - 1) - strlen($title);
            $title = substr($title, 0, strrpos($title, ' ', $last)) . '...';
        }
        return $title;
    }

    private function createURL(string $library, string $call_number, string $title): string
    {
        $url_params = [
            'library_code' => $library,
            'call_number' => $call_number,
            'title' => $title
        ];
        return $this->router->generate('map_index', $url_params, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}