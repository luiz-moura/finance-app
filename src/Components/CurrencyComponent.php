<?php

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('currency')]
class CurrencyComponent
{
    public array $coins;
}
