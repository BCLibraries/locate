<?php

namespace App\Service;

use App\Repository\ShelfRepository;
use App\Service\CallNoNormalizer\LCNormalizer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Create a message to send with call number and link to map
 */
class MessageWriter
{
    private static array $valid_libraries = ['onl', 'law'];
    private ShelfRepository $shelf_repository;
    private LCNormalizer $normalizer;
    private UrlGeneratorInterface $router;

    public function __construct(
        ShelfRepository       $shelf_repository,
        LCNormalizer          $normalizer,
        UrlGeneratorInterface $router,
    )
    {
        $this->shelf_repository = $shelf_repository;
        $this->normalizer = $normalizer;
        $this->router = $router;
    }

    /**
     * Build a message to send
     *
     * @param string $library
     * @param string $call_number
     * @param string $title
     * @return string
     * @throws \Exception thrown for invalid messages
     */
    public function createMessage(string $library, string $call_number, string $title): string
    {
        if ($this->isNotValidCallNumber($call_number)) {
            throw new \Exception("$call_number is not a valid call number");
        }

        if ($this->isNotValidLibrary($library)) {
            throw new \Exception("$library is not a valid library code");
        }

        $clean_title = $this->cleanTitle($title);
        $clean_title = $this->truncateTitle($clean_title);
        $location_string = $this->createLocationString($library, $call_number);

        $url = $this->createURL($library, $call_number, $clean_title);

        return "Boston College Library book $call_number $title is in $location_string $url";
    }


    /**
     * Is the call number invalid?
     *
     * @param string $call_number
     * @return bool
     */
    private function isNotValidCallNumber(string $call_number): bool
    {
        // @todo actually validate the call number
        return false;
    }

    /**
     * Is the library fake?
     *
     * @param string $library
     * @return bool
     */
    private function isNotValidLibrary(string $library): bool
    {
        return (!in_array($library, self::$valid_libraries, true));
    }

    /**
     * Clean the title for emailing
     *
     * @param string $title the title to clean
     * @return string the cleaned title
     */
    private function cleanTitle(string $title): string
    {
        // @todo verify that the title exists in Alma
        return strip_tags($title);
    }

    /**
     * Build the location string
     *
     * @param string $library the library code
     * @param string $call_number the call number
     * @return string e.g. "O'Neill Library, Stacks, Row 13"
     */
    private function createLocationString(string $library, string $call_number): string
    {
        // Find the shelf.
        $normalized_call_number = $this->normalizer->normalize($call_number);
        $shelf = $this->shelf_repository->findOneByLibraryAndCallNumber($library, $normalized_call_number);

        // Build the string.
        $map = $shelf->getMap();
        return $map->getLibrary()->getLabel() . ", " . $map->getLabel() . ", Row " . $shelf->getCode();
    }

    /**
     * Make long titles short
     *
     * @param $title string the full title
     * @param $max_chars int defaults to 20
     * @return string the truncated title
     */
    private function truncateTitle($title, int $max_chars = 20): string
    {
        if (strlen($title) > $max_chars) {
            $last = ($max_chars - 1) - strlen($title);
            $title = substr($title, 0, strrpos($title, ' ', $last)) . '...';
        }
        return $title;
    }

    /**
     * Build the URL to the map page
     *
     * @param string $library
     * @param string $call_number
     * @param string $title
     * @return string
     */
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
