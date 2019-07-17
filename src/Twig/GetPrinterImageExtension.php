<?php

namespace App\Twig;

use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class GetPrinterImageExtension extends AbstractExtension
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('getPrinterImage', [$this, 'getPrinterImage']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getPrinterImage', [$this, 'getPrinterImage']),
        ];
    }

    public function getPrinterImage($value)
    {
        $value = strtolower(trim(preg_replace('/\s+/', '-', preg_replace('/[^a-zA-Z0-9\s]/', '', $value)))).".jpg";
        $path = $this->kernel->getProjectDir() . "/public/images/printer/";

        if(file_exists($path.$value)) {
            return sprintf('<img src="/images/printer/%s" title="Printer Image"/>', $value);
        }

        return '<img src="/images/print-solid.svg" class="pt-3 pb-3" style="filter: invert(99%) sepia(5%) saturate(991%) hue-rotate(166deg) brightness(117%) contrast(68%);" title="Default Image"/>';
    }
}
