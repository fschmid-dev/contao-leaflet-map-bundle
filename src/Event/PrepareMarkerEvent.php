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

    /**
     * @var array<string, string> filter value => button label
     */
    private array $filters = [];

    private string $filterAllLabel = 'Alle';

    public function __construct(private array $markers)
    {
    }

    /**
     * Shows filter buttons above the map, e.g. one per day.
     *
     * @param array<string, string> $filters filter value => button label
     */
    public function setFilters(array $filters, string $allLabel = 'Alle'): self
    {
        $this->filters = $filters;
        $this->filterAllLabel = $allLabel;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getFilterAllLabel(): string
    {
        return $this->filterAllLabel;
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
