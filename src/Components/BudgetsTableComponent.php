<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('budgets-table')]
class BudgetsTableComponent
{
    public array $budgets;
}
