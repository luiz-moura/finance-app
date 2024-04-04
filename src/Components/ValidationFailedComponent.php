<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('validation-failed')]
class ValidationFailedComponent
{
    public array $errors;
}
