<?php

namespace FSchmidDev\LeafletMapBundle\EventListener\Widget;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;

class LocationListener
{
    #[AsCallback(table: 'tl_module', target: 'fields.location.save')]
    public function saveLocation($value, DataContainer $dataContainer) {
        $a = 0;

        return 'asdf';
    }

    #[AsCallback(table: 'tl_module', target: 'fields.location.load')]
    public function loadLocation($value, DataContainer $dataContainer) {
        $a = 0;

        return $value;
    }
}