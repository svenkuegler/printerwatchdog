<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FilterUrlExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('filterUrl', [$this, 'getFilterUrl']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('filterUrl', [$this, 'getFilterUrl']),
        ];
    }

    public function getFilterUrl(array $filter, $current)
    {
        $newFilter = [];
        foreach ($filter as $item) {
            if($item != $current) $newFilter[] = $item;
        }

        if(count($newFilter) > 0) {
            return "?filter=" . implode(":", $newFilter);
        }

        return '';
    }
}
