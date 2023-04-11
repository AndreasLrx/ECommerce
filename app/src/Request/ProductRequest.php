<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Url;

class ProductRequest extends BaseRequest
{

    #[Type('string')]
    #[NotBlank()]
    #[Length(max: 255)]
    public $name = null;

    #[Type('string')]
    #[NotNull()]
    #[Length(max: 255)]
    public $description = null;

    #[Type('string')]
    #[NotNull()]
    #[Length(max: 255)]
    #[Url()]
    public $photo = null;

    #[Type('float')]
    #[NotBlank()]
    #[Positive()]
    public $price = null;
}
