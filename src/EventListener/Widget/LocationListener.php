<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Leaflet Map Bundle
 * @copyright  Copyright (c) 2022, fschmid-dev
 * @author     fschmid <https://fschmid.dev>
 * @link       https://github.com/fschmid-dev/contao-leaflet-map-bundle
 */

namespace FSchmidDev\LeafletMapBundle\EventListener\Widget;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LocationListener
{
    private const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/search';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[AsCallback(table: 'tl_module', target: 'fields.location.save')]
    public function saveLocation($value, DataContainer $dataContainer)
    {
        $data = json_decode($value, true, 512, \JSON_THROW_ON_ERROR);
        $previous = $this->decode($dataContainer->getCurrentRecord()['location'] ?? null);

        if (
            (
                $data['location'] !== $previous['location']
                && $data['location_latitude'] === $previous['location_latitude']
                && $data['location_longitude'] === $previous['location_longitude']
            )
            || '' === $data['location_latitude']
            || '' === $data['location_longitude']
        ) {
            $this->getLatLongCoordinates($data);
        }

        return json_encode($data, \JSON_THROW_ON_ERROR);
    }

    #[AsCallback(table: 'tl_module', target: 'fields.location.load')]
    public function loadLocation($value, DataContainer $dataContainer)
    {
        if (null !== $value) {
            return json_decode($value, true, 512, \JSON_THROW_ON_ERROR);
        }

        return $value;
    }

    /**
     * @return array{location: string, location_latitude: string, location_longitude: string}
     */
    private function decode(?string $value): array
    {
        $data = $value ? json_decode($value, true) : null;

        return array_merge(
            ['location' => '', 'location_latitude' => '', 'location_longitude' => ''],
            \is_array($data) ? $data : [],
        );
    }

    private function getLatLongCoordinates(array &$data): void
    {
        if ('' === trim($data['location'])) {
            return;
        }

        try {
            // Nominatim's usage policy requires an identifying User-Agent
            $json = $this->httpClient->request('GET', self::NOMINATIM_URL, [
                'query' => ['format' => 'jsonv2', 'limit' => 1, 'q' => $data['location']],
                'headers' => ['User-Agent' => 'fschmid-dev/contao-leaflet-map-bundle (+https://github.com/fschmid-dev/contao-leaflet-map-bundle)'],
                'timeout' => 5,
            ])->toArray();
        } catch (\Throwable $e) {
            $this->logger->warning('Leaflet map geocoding failed: '.$e->getMessage());

            return;
        }

        if (isset($json[0]['lat'], $json[0]['lon'])) {
            $data['location_latitude'] = (string) $json[0]['lat'];
            $data['location_longitude'] = (string) $json[0]['lon'];
        }
    }
}
