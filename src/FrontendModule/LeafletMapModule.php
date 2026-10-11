<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Leaflet Map Bundle
 * @copyright  Copyright (c) 2022, fschmid-dev
 * @author     fschmid <https://fschmid.dev>
 * @link       https://github.com/fschmid-dev/contao-leaflet-map-bundle
 */

namespace FSchmidDev\LeafletMapBundle\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Template;
use Contao\CoreBundle\Twig\FragmentTemplate;
use FSchmidDev\LeafletMapBundle\Event\PrepareMarkerEvent;
use FSchmidDev\LeafletMapBundle\Model\Marker;
use JsonException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(LeafletMapModule::TYPE, category: 'miscellaneous')]
class LeafletMapModule extends AbstractFrontendModuleController
{
    public const TYPE = 'leaflet_map';

    public function __construct(private EventDispatcherInterface $dispatcher)
    {
    }

    /**
     * @throws JsonException
     */
    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        // GDPR - settings
        $acceptLoad = $model->acceptLoad;

        $GLOBALS['TL_CSS'][] = 'bundles/fschmiddevleafletmap/leaflet/leaflet.css';
        $GLOBALS['TL_BODY'][] = Template::generateScriptTag('bundles/fschmiddevleafletmap/leaflet/leaflet.js');

        if ($acceptLoad) {
            $dataPrivacyInfo = $model->dataPrivacyInfo;
            $dataPrivacyUrl = $model->dataPrivacyUrl;

            $adapter = $this->getContaoAdapter(PageModel::class);
            $dataPrivacyPage = $dataPrivacyUrl > 0 ? $adapter->findByPk($dataPrivacyUrl) : null;

            $template->set('acceptLoad', $acceptLoad);
            $template->set('dataPrivacyInfo', $dataPrivacyInfo);
            $template->set('dataPrivacyPage', $dataPrivacyPage);
        }

        $template->set('map_data', $this->createData($model));
        $GLOBALS['TL_BODY'][] = Template::generateScriptTag('bundles/fschmiddevleafletmap/leaflet-map.js', false, null, null, null, null, true);

        return $template->getResponse();
    }

    /**
     * Everything the browser needs to draw the map, as JSON for leaflet-map.js.
     *
     * @return array<string, mixed>
     */
    private function createData(ModuleModel $model): array
    {
        $location = json_decode((string) $model->location, true) ?: [];
        $markers = [];

        foreach (StringUtil::deserialize($model->markers, true) as $marker) {
            $markerLocation = json_decode((string) ($marker['location'] ?? ''), true) ?: [];
            if (($markerLocation['location'] ?? '') === '') {
                continue;
            }

            $markers[] = new Marker(
                $markerLocation['location'],
                (string) $markerLocation['location_latitude'],
                (string) $markerLocation['location_longitude'],
                $marker['popup'] ?? null
            );
        }

        $event = new PrepareMarkerEvent($markers);
        $this->dispatcher->dispatch($event, PrepareMarkerEvent::NAME);

        $center = [(float) ($location['location_latitude'] ?? 51.16), (float) ($location['location_longitude'] ?? 10.45)];

        return [
            'center' => $center,
            'zoom' => (int) ($model->zoomLevel ?? 13),
            'autoZoom' => (bool) $model->autoZoom,
            'acceptLoad' => (bool) $model->acceptLoad,
            'filters' => $event->getFilters(),
            'filterAll' => $event->getFilterAllLabel(),
            'markers' => array_values(array_map(
                static fn (Marker $marker): array => [
                    'lat' => (float) ($marker->getLatitude() ?: $center[0]),
                    'lng' => (float) ($marker->getLongitude() ?: $center[1]),
                    'popup' => (string) $marker->getPopup(),
                    'filter' => $marker->getFilterValues(),
                ],
                $event->getMarkers(),
            )),
        ];
    }
}
