<?php

namespace App\Controller;

use App\Repository\ShelfRepository;
use App\Service\CallNoNormalizer\LCNormalizer;
use App\Service\MapRewriter\BadShelfQueryException;
use App\Service\MapRewriter\MapFileReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    private ShelfRepository $shelf_repository;
    private LCNormalizer $normalizer;
    private MapFileReader $map_file_reader;

    public function __construct(ShelfRepository $shelf_repository, LCNormalizer $normalizer, MapFileReader $map_file_reader)
    {

        $this->shelf_repository = $shelf_repository;
        $this->normalizer = $normalizer;
        $this->map_file_reader = $map_file_reader;
    }

    /**
     * Index controller
     *
     * @Route("/map/{library_code}/{call_number}", name="map_index")
     *
     * @param Request $request
     * @return Response
     * @throws BadShelfQueryException
     */
    public function index(string $library_code, string $call_number, Request $request): Response
    {
        $title = $request->query->get('title');

        $normalized_call_number = $this->normalizer->normalize($call_number);
        $shelf = $this->shelf_repository->findOneByLibraryAndCallNumber($library_code, $normalized_call_number);
        $map = $shelf->getMap();
        $library = $map->getLibrary();

        $page_title = $this->buildPageTitle($map);

        return $this->render('map/index.html.twig', [
            'library_code' => $library_code,
            'library_display' => $library->getLabel(),
            'map_display' => $map->getLabel(),
            'shelf_display' => $shelf->getCode(),
            'call_number' => $call_number,
            'title' => $title,
            'svg' => $this->map_file_reader->readSvg($map)->asXML(),
            'page_title' => $page_title
        ]);
    }

    /**
     * Return JSON with shelf info for call number
     *
     * @Route("/shelf", name="shelf")
     *
     * @param LCNormalizer $normalizer
     * @param ShelfRepository $shelf_repository
     * @param Request $request
     * @return JsonResponse
     */
    public function shelf(LCNormalizer $normalizer, ShelfRepository $shelf_repository, Request $request): JsonResponse
    {
        $library_code = $request->query->get('lib');
        $call_number = $request->query->get('callno');
        $normalized_call_number = $normalizer->normalize($call_number);
        $shelf = $shelf_repository->findOneByLibraryAndCallNumber($library_code, $normalized_call_number);
        return $this->json(['shelf' => [
            'code' => $shelf->getCode(),
            'id' => $shelf->getId(),
            'callNoStart' => $shelf->getStartCallNumber(),
            'callNoEnd' => $shelf->getEndCallNumber()
        ]]);
    }

    /**
     * @param LCNormalizer $normalizer
     * @param $call_number
     * @param ShelfRepository $shelf_repository
     * @param $library_code
     * @param MapFileReader $map_file_reader
     * @return string
     */
    private function getSVGString(LCNormalizer $normalizer, ShelfRepository $shelf_repository, MapFileReader $map_file_reader, $library_code, $call_number): string
    {
        $normalized_call_number = $normalizer->normalize($call_number);
        $shelf = $shelf_repository->findOneByLibraryAndCallNumber($library_code, $normalized_call_number);
        return $map_file_reader->readSvg($shelf->getMap())->asXML();
    }

    /**
     * @param \App\Entity\Map|null $map
     * @return string
     */
    private function buildPageTitle(?\App\Entity\Map $map): string
    {
        $map_label = $map->getLabel();
        $library_label = $map->getLibrary()->getLabel();

        $page_title = "$library_label - $map_label";
        return $page_title;
    }
}
