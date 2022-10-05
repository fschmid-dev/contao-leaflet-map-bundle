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
use Symfony\Component\HttpFoundation\RequestStack;

class LocationListener
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    #[AsCallback(table: 'tl_module', target: 'fields.location.save')]
    public function saveLocation($value, DataContainer $dataContainer)
    {
        $data = json_decode($value, true, 512, \JSON_THROW_ON_ERROR);

        ['location' => $location, 'location_latitude' => $locationLatitude, 'location_longitude' => $locationLongitude] = $data;
        ['location' => $previousLocation, 'location_latitude' => $previousLocationLatitude, 'location_longitude' => $previousLocationLongitude] = $dataContainer->activeRecord->location;

        if (
            (
                $location !== $previousLocation &&
                $locationLatitude === $previousLocationLatitude &&
                $locationLongitude === $previousLocationLongitude
            ) ||
            '' === $locationLatitude ||
            '' === $locationLongitude
        ) {
            $this->getLatLongCoordinates($data);
        }

        return json_encode($data, \JSON_THROW_ON_ERROR);
    }

    #[AsCallback(table: 'tl_module', target: 'fields.location.load')]
    public function loadLocation($value, DataContainer $dataContainer)
    {
        return json_decode($value, true, 512, \JSON_THROW_ON_ERROR);
    }

    private function getLatLongCoordinates(array &$data): void
    {
        $url = sprintf(
            'https://nominatim.openstreetmap.org/search?format=json&q=%s',
            urlencode($data['location'])
        );
        $userAgent = $this->requestStack->getCurrentRequest()->server->get('HTTP_USER_AGENT') ?: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36';

        $ch = curl_init();
        curl_setopt($ch, \CURLOPT_URL, $url);
        curl_setopt($ch, \CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, \CURLOPT_USERAGENT, $userAgent);
        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response, true, 512, \JSON_THROW_ON_ERROR);
        if (empty($json)) {
            return;
        }

        $data['location_latitude'] = $json[0]['lat'] ?? 'Konnte nicht ermittelt werden!';
        $data['location_longitude'] = $json[0]['lon'] ?? 'Konnte nicht ermittelt werden!';
    }
}
