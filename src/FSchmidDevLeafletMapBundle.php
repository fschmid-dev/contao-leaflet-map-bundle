<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Leaflet Map Bundle
 * @copyright  Copyright (c) 2022, fschmid-dev
 * @author     fschmid <https://fschmid.dev>
 * @link       https://github.com/fschmid-dev/contao-leaflet-map-bundle
 */

namespace FSchmidDev\LeafletMapBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FSchmidDevLeafletMapBundle extends Bundle
{
    public function getPath(): string
    {
        if (null === $this->path) {
            $this->path = \dirname(__DIR__);
        }

        return $this->path;
    }
}
