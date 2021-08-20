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
    /**
     * Index controller
     *
     * @Route("/map", name="map_index")
     *
     * @param ShelfRepository $shelf_repository
     * @param LCNormalizer $normalizer
     * @param MapFileReader $map_file_reader
     * @param Request $request
     * @return Response
     * @throws BadShelfQueryException
     */
    public function index(ShelfRepository $shelf_repository, LCNormalizer $normalizer, MapFileReader $map_file_reader, Request $request)
    {
        $library_code = $request->query->get('lib');
        $call_number = $request->query->get('callno');

        $page_title = "O'Neill Library - 5th Floor : Left of Main Stairway";

        $svg = $this->getSVGString($normalizer, $shelf_repository, $map_file_reader, $library_code, $call_number);

        return $this->render('map/index.html.twig', [
            'library_code' => $library_code,
            'call_number' => $call_number,
            'svg' => $svg,
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
}
