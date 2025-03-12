<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ResultRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    public string $test_hash;

    public array $answers = [];
}
