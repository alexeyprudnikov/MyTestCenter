<?php

namespace App\Controller\WebApp;

use App\Dto\Schema\GoogleGenAi\HotelsResponse;
use App\Dto\Schema\GoogleGenAi\RecipesResponse;
use App\Service\Ai\GoogleGenAiService;
use App\Service\DaVinciService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(name: 'test_')]
class TestController extends AbstractController
{
    #[Route('/test_gemini', name: 'index')]
    public function index(): Response
    {
        return $this->render(
            'web_app/test_gemini/index.html.twig',
            [
                'title' => 'Test Google Gemini Generative AI',
                'responseSchemaRecipes' => RecipesResponse::$schema,
                'responseSchemaHotels' => HotelsResponse::$schema,
            ]
        );
    }

    #[Route('/test_gemini/proceed/{type}', name: 'proceed')]
    public function proceed(
        string             $type,
        GoogleGenAiService $googleGenAiService,
    ): JsonResponse
    {
        $start = microtime(true);
        $responseSchema = match ($type) {
            'recipes' => RecipesResponse::$schema,
            'hotels' => HotelsResponse::$schema,
            default => null,
        };
        if ($responseSchema === null) {
            return $this->json(['content' => 'not correct schema type']);
        }
        $list = $googleGenAiService
            ->setResponseSchema($responseSchema)
            ->request();
        $executionTime = number_format(microtime(true) - $start, 2, '.', '');
        $content = $this->renderView(
            "web_app/test_gemini/response_{$type}.html.twig",
            [
                'list' => $list,
                'executionTime' => $executionTime
            ]
        );
        return $this->json(['content' => $content]);
    }

    #[Route('/test_davinci', name: 'davinci')]
    public function daVinci(
        DaVinciService $daVinciService,
    ): JsonResponse
    {
        return $this->json($daVinciService->proceed());
    }
}
