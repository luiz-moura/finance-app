<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('transactions-table')]
class TransactionsTableComponent
{
    public array $transactions;
}
