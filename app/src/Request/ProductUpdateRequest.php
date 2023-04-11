<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\NotNull;

class ProductUpdateRequest extends BaseRequest
{

    #[Type('string')]
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
    #[Positive()]
    public $price = null;
}
