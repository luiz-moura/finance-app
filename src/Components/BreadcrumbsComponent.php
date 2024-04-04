<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('breadcrumbs')]
class BreadcrumbsComponent
{
    public array $items;
}
