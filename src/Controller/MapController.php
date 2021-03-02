<?php

namespace App\Controller;

use App\Repository\ShelfRepository;
use App\Service\CallNoNormalizer\LCNormalizer;
use App\Service\MapRewriter\BadShelfQueryException;
use App\Service\MapRewriter\MapRewriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{

    /**
     * @var MapRewriter
     */
    private $map_rewriter;

    /**
     * Return an SVG with a call number mapped out
     *
     * @Route("/map", name="map")
     *
     * @param ShelfRepository $shelf_repository
     * @param LCNormalizer $normalizer
     * @param Request $request
     * @return Response
     * @throws BadShelfQueryException
     */
    public function index(ShelfRepository $shelf_repository, LCNormalizer $normalizer, Request $request): Response
    {
        $library_code = $request->query->get('lib');
        $call_number = $request->query->get('callno');
        $normalized_call_number = $normalizer->normalize($call_number);

        $shelf = $shelf_repository->findOneByLibraryAndCallNumber($library_code, $normalized_call_number);

        $rewriter = new MapRewriter($shelf->getMap());
        $svg_string = $rewriter->addArrow($shelf->getCode());

        $response = new Response();
        $response->setContent($svg_string);
        $response->headers->set('Content-type','image/svg+xml');


        return $response;
    }
}
