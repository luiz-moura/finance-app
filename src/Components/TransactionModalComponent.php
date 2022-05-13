<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('transaction-modal')]
class TransactionModalComponent
{
    public $categories;
}
