<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('account-balance')]
class AccountBalanceComponent
{
    public string $balance;
}
