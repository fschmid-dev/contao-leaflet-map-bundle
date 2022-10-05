<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Leaflet Map Bundle
 * @copyright  Copyright (c) 2022, fschmid-dev
 * @author     fschmid <https://fschmid.dev>
 * @link       https://github.com/fschmid-dev/contao-leaflet-map-bundle
 */

namespace FSchmidDev\LeafletMapBundle\Event;

use FSchmidDev\LeafletMapBundle\Model\Marker;
use Symfony\Contracts\EventDispatcher\Event;

class PrepareMarkerEvent extends Event
{
    public const NAME = 'fschmid-dev.leaflet-map.markers.prepare';

    public function __construct(private array $markers)
    {
    }

    /**
     * @return Marker[]
     */
    public function getMarkers(): array
    {
        return $this->markers;
    }

    public function setMarkers(array $markers): self
    {
        $this->markers = $markers;

        return $this;
    }
}
