<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class HistoryRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    public string $email;

    public ?string $first_name = null;

    public ?string $last_name = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    public string $test_hash;
}
