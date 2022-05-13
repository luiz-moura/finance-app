<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('categories-table')]
class CategoriesTableComponent
{
    public $categories;
}
