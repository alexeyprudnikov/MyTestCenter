<?php

namespace App\Service;

use App\Dto\QuestionSchema;
use App\Dto\Request\HistoryRequest;
use App\Dto\Request\ResultRequest;
use App\Dto\Schema\GoogleGenAi\TestResponse;
use App\Entity\History;
use App\Entity\Test;
use App\Enum\AiSource;
use App\Service\Ai\GoogleGenAiService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

readonly class TestService
{
    public const AI_SOURCE = AiSource::GOOGLE_GEN_AI;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private GoogleGenAiService  $googleGenAiService,
    ) {}

    /**
     * @param HistoryRequest $historyRequest
     * @param Test $test
     * @return array
     */
    public function loadSavedData(HistoryRequest $historyRequest, Test $test): array
    {
        $historyRepository  = $this->entityManager->getRepository(History::class);
        /**
         * find if test by user already started
         */
        $history = $historyRepository->findOneBy([
            'test' => $test,
            'email' => $historyRequest->email,
        ]);

        if ($history?->getIsReady() === true) {
            $resultMessage = $this->getResultMessage($history->getCorrectQuote(), $test->getSuccessQuote());
            return ["You have already participated this test.<br>{$resultMessage}"];
        }
        return $history?->getData() ?? [];
    }

    /**
     * @param HistoryRequest $historyRequest
     * @param Test $test
     * @return array
     */
    public function loadAiData(HistoryRequest $historyRequest, Test $test): array
    {
        $data = $this->requestAi($test);
        // some message only
        if (count($data) === 1) {
            return $data;
        }
        // re-sort answers randomise
        array_walk($data, function (&$item) {
            shuffle($item['answers']);
        });
        /**
         * write data to history
         */
        $history = (new History())
            ->setTest($test)
            ->setEmail($historyRequest->email)
            ->setFirstName($historyRequest->first_name)
            ->setLastName($historyRequest->last_name)
            ->setData($data);
        $this->entityManager->persist($history);
        $this->entityManager->flush();
        return $data;
    }

    /**
     * @param Test $test
     * @return array
     */
    private function requestAi(Test $test): array
    {
        switch (self::AI_SOURCE) {
            case AiSource::GOOGLE_GEN_AI:
                $responseSchema = TestResponse::getSchema($test);
                return $this->googleGenAiService->setResponseSchema($responseSchema)->request();
            default:
                return ['no ai selected'];
        }
    }

    /**
     * @param array<int, mixed> $data
     * @return ArrayCollection
     */
    public function prepareCollection(array $data): ArrayCollection
    {
        $questionCollection = new ArrayCollection();
        foreach ($data as $step) {
            $questionSchema = new QuestionSchema(
                question: $step['question'] ?? '',
                explanation_headline: $step['explanation_headline'] ?? null,
                explanation_text: $step['explanation_text'] ?? null,
                answers: $step['answers'] ?? []
            );
            $questionCollection->add($questionSchema);
        }
        return $questionCollection;
    }

    public function checkResult(ResultRequest $resultRequest, Test $test): string
    {
        $historyRepository  = $this->entityManager->getRepository(History::class);
        /**
         * find if test by user already started
         */
        $history = $historyRepository->findOneBy([
            'test' => $test,
            'email' => $resultRequest->email,
        ]);
        if ($history === null) {
            return 'no result\'s checking error';
        }

        $correctQuote = $this->countResult($history->getData(), $resultRequest->answers);

        $history
            ->setAnswers($resultRequest->answers)
            ->setIsReady(true)
            ->setCorrectQuote($correctQuote);

        $this->entityManager->persist($history);
        $this->entityManager->flush();

        return $this->getResultMessage($correctQuote, $test->getSuccessQuote());
    }

    private function countResult(array $testData, array $answers): int
    {
        $totalQuestions = count($testData);
        $correctAnswersCount = 0;
        foreach ($answers as $questionIndex => $questionAnswers) {
            $isCorrect = true;
            foreach ($questionAnswers as $answerValue) {
                if ($testData[$questionIndex]['answers'][intval($answerValue)]['is_correct'] === false) {
                    $isCorrect = false;
                    break;
                }
            }
            if ($isCorrect) {
                $correctAnswersCount++;
            }
        }
        return round($correctAnswersCount*100/$totalQuestions);
    }

    private function getResultMessage(int $correctQuote, int $successQuote): string
    {
        return $correctQuote < $successQuote ? "You have not passed the test.<br>Your quote is {$correctQuote}% and success quote is {$successQuote}%." : "You have passed the test!<br>Your quote is {$correctQuote}% and success quote is {$successQuote}%.";
    }
}
