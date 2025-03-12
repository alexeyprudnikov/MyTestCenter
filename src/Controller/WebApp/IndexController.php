<?php

namespace App\Controller\WebApp;

use App\Dto\Request\ResultRequest;
use App\Dto\Request\HistoryRequest;
use App\Dto\WorkflowFixture;
use App\Entity\Test;
use App\Repository\TestRepository;
use App\Service\TestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(name: 'web_app_')]
class IndexController extends AbstractController
{
    public function __construct(
        private readonly TestService $testService,
        private readonly TestRepository $testRepository
    ) {

    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('web_app/start/index.html.twig');
    }

    #[Route('/start/{hash}', name: 'start')]
    public function start(
        string $hash
    ): Response
    {
        $test = $this->testRepository->findOneBy(['hash' => $hash]);
        if (null === $test) {
            throw $this->createNotFoundException("test $hash not found");
        }

        return $this->render(
            'web_app/workflow/start.html.twig',
            [
                'title' => $test->getTitle(),
                'isLearning' => $test->isTypeLearning(),
                'hash' => $hash
            ]
        );
    }

    #[Route('/load_saved', name: 'load_saved', methods: ['POST'])]
    public function loadSavedData(
        #[MapRequestPayload] HistoryRequest $historyRequest
    ): JsonResponse
    {
        $test = $this->testRepository->findOneBy(['hash' => $historyRequest->test_hash]);
        if (null === $test) {
            throw $this->createNotFoundException("test {$historyRequest->test_hash} not found");
        }
        if ($this->getParameter('is_sandbox')) {
            $data = $test->isTypeLearning() ? WorkflowFixture::$response_LEADNING : WorkflowFixture::$response_TEST;
        } else {
            $data = $this->testService->loadSavedData($historyRequest, $test);
        }
        return $this->response($data, $historyRequest->email, $test->getHash(), $test->isTypeLearning());
    }

    #[Route('/load_ai', name: 'load_ai', methods: ['POST'])]
    public function loadAiData(
        #[MapRequestPayload] HistoryRequest $historyRequest
    ): JsonResponse
    {
        $test = $this->testRepository->findOneBy(['hash' => $historyRequest->test_hash]);
        if (null === $test) {
            throw $this->createNotFoundException("test {$historyRequest->test_hash} not found");
        }
        $data = $this->testService->loadAiData($historyRequest, $test);
        return $this->response($data, $historyRequest->email, $test->getHash(), $test->isTypeLearning());
    }

    #[Route('/check', name: 'check', methods: ['POST'])]
    public function check(
        #[MapRequestPayload] ResultRequest $resultRequest
    ): JsonResponse
    {
        $test = $this->testRepository->findOneBy(['hash' => $resultRequest->test_hash]);
        if (null === $test) {
            throw $this->createNotFoundException("test {$resultRequest->test_hash} not found");
        }
        $content = $this->testService->checkResult($resultRequest, $test);
        return $this->json(['content' => $content]);
    }

    /**
     * @param array $data
     * @param string $email
     * @param string $testHash
     * @param bool $isLearning
     * @return JsonResponse
     */
    private function response(array $data, string $email, string $testHash, bool $isLearning): JsonResponse
    {
        // no data
        if (count($data) === 0) {
            return $this->json(['content' => '']);
        }
        // some message only
        if (count($data) === 1) {
            return $this->json(['content' => $data[0]]);
        }
        $questionCollection = $this->testService->prepareCollection($data);
        $content = $this->renderView(
            'web_app/workflow/form.html.twig',
            [
                'email' => $email,
                'hash' => $testHash,
                'questionCollection' => $questionCollection,
                'isLearning' => $isLearning
            ]
        );
        return $this->json(['content' => $content]);
    }
}
