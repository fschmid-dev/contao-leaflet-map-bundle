<?php

namespace FSchmidDev\LeafletMapBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use FSchmidDev\LeafletMapBundle\FSchmidDevLeafletMapBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(FSchmidDevLeafletMapBundle::class)->setLoadAfter([ContaoCoreBundle::class])
        ];
    }
}