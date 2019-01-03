<?php

namespace App\Controller;

use App\Entity\PopularityResult;
use App\Exception\JsonResponseNotExistException;
use App\Exception\NoTermInDbException;
use App\Responses\JsonResponseFactory;
use App\Services\ServiceProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PopularityScoreController extends AbstractController
{
    protected $jsonResponse;

    protected $statusCode;

    private $serviceProvider;

    /**
     * PopularityScoreController constructor.
     */
    public function __construct(ServiceProviderInterface $serviceProvider)
    {
        $this->serviceProvider = $serviceProvider;
    }


    /**
     * @Route("/score/{version}", name="score_show")
     */
    public function show($version = 'v1', Request $request)
    {
        $term = $request->query->get('term');

        if ($term === null || $term == '')
        {
            return $this->json([], 422);
        }

        $entityManager = $this->getDoctrine()->getManager();

        try {
            $this->jsonResponse = JsonResponseFactory::create($version);

            $scoreInDb = $entityManager->getRepository(PopularityResult::class)
                ->findOneByTerm($term);

            return $this->setStatusCode(200)->respond($this->jsonResponse->transformNormalData([
                'term' => $scoreInDb->getTerm(),
                'score' => $scoreInDb->getScore(),
            ]));

        } catch (JsonResponseNotExistException $e) {

            return $this->setStatusCode(404)->respond([]);

        } catch (NoTermInDbException $e) {
            $popularityResuls = new PopularityResult();
            $popularityResuls->setTerm($term);
            $popularityResuls->setScore($this->serviceProvider->getScore($term));

            $entityManager->persist($popularityResuls);
            $entityManager->flush();

            return $this->setStatusCode(200)->respond($this->jsonResponse->transformNormalData([
                'term' => $term,
                'score' => $this->serviceProvider->getScore($term),
            ]));
        }
    }

    public function setStatusCode($statusCode): PopularityScoreController
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    protected function respond($data): JsonResponse
    {
        $responseHeader = [];
        if ($this->jsonResponse !== null) {
            $responseHeader = $this->jsonResponse->getResponseHeader();
        }

        return $this->json($data, $this->getStatusCode(), $responseHeader);
    }
}
