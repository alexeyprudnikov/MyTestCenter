<?php

namespace App\Entity;

use App\Entity\Traits\Timestamp;
use App\Enum\Language;
use App\Enum\WorkflowType;
use App\Repository\TestRepository;
use BackedEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Test
{
    use Timestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 16, enumType: WorkflowType::class)]
    private ?WorkflowType $type = null;

    #[ORM\Column(length: 16, enumType: Language::class)]
    private ?Language $language = null;

    #[ORM\Column]
    private ?int $questions_count = null;

    /**
     * array as [min, max]
     */
    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $answers_count = [];

    /**
     * array as [min, max]
     */
    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $right_answers_count = [];

    #[ORM\Column]
    private ?int $success_quote = null;

    #[ORM\Column(length: 32)]
    private ?string $hash = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "tests")]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, History>
     */
    #[ORM\OneToMany(targetEntity: History::class, mappedBy: 'test', orphanRemoval: true)]
    private Collection $history;

    public function __construct()
    {
        $this->history = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getType(): ?WorkflowType
    {
        return $this->type;
    }

    public function setType(WorkflowType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getQuestionsCount(): ?int
    {
        return $this->questions_count;
    }

    public function setQuestionsCount(int $questions_count): static
    {
        $this->questions_count = $questions_count;

        return $this;
    }

    public function getAnswersCount(): array
    {
        return $this->answers_count;
    }

    public function setAnswersCount(array $answers_count): static
    {
        $this->answers_count = $answers_count;

        return $this;
    }

    public function getRightAnswersCount(): array
    {
        return $this->right_answers_count;
    }

    public function setRightAnswersCount(array $right_answers_count): static
    {
        $this->right_answers_count = $right_answers_count;

        return $this;
    }

    public function getSuccessQuote(): ?int
    {
        return $this->success_quote;
    }

    public function setSuccessQuote(int $success_quote): static
    {
        $this->success_quote = $success_quote;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): static
    {
        $this->hash = $hash;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isTypeTest(): bool
    {
        return $this->getType() === WorkflowType::TEST;
    }

    public function isTypeLearning(): bool
    {
        return $this->getType() === WorkflowType::LEARNING;
    }

    /**
     * @return Collection<int, History>
     */
    public function getHistory(): Collection
    {
        return $this->history;
    }

    public function addHistory(History $history): static
    {
        if (!$this->history->contains($history)) {
            $this->history->add($history);
            $history->setTest($this);
        }

        return $this;
    }

    public function removeHistory(History $history): static
    {
        if ($this->history->removeElement($history)) {
            // set the owning side to null (unless already changed)
            if ($history->getTest() === $this) {
                $history->setTest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, History>
     */
    public function getCompletedHistory(): Collection
    {
        return $this->getHistory()->filter(
            fn (History $history) => $history->getIsReady() === true
        );
    }
}
