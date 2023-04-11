<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationRequest extends BaseRequest
{
    #[Type('string')]
    #[NotBlank()]
    #[Length(max: 180)]
    public $login;

    #[Type('string')]
    #[NotBlank()]
    #[Email()]
    #[Length(max: 255)]
    public $email;

    #[Type('string')]
    #[NotBlank()]
    #[Length(max: 255)]
    public $password;

    #[Type('string')]
    #[NotBlank()]
    #[Length(max: 255)]
    public $firstname;

    #[Type('string')]
    #[NotBlank()]
    #[Length(max: 255)]
    public $lastname;
}
