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
    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
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

            $data = $template->getData();
            $data['acceptLoad'] = $acceptLoad;
            $data['dataPrivacyInfo'] = $dataPrivacyInfo;
            $data['dataPrivacyPage'] = $dataPrivacyPage;
            $template->setData($data);
        }

        $jsScript = $this->createScript($model);
        $GLOBALS['TL_BODY'][] = Template::generateInlineScript($jsScript);

        return new Response($template->parse());
    }

    private function createScript(ModuleModel $model): string
    {
        $data = json_decode($model->location, true, 512, \JSON_THROW_ON_ERROR);
        ['location' => $location, 'location_latitude' => $locationLatitude, 'location_longitude' => $locationLongitude] = $data;

        $markersArray = StringUtil::deserialize($model->markers);
        $markers = [];

        foreach ($markersArray as $index => $marker) {
            $markers[] = new Marker(
                $marker['location'],
                $marker['location_latitude'],
                $marker['location_longitude'],
                $marker['popup']
            );
        }

        $prepareMarkerEvent = new PrepareMarkerEvent($markers);

        $this->dispatcher->dispatch($prepareMarkerEvent, PrepareMarkerEvent::NAME);

        $markers = $prepareMarkerEvent->getMarkers();

        $markersCode = '';
        foreach ($markers as $index => $marker) {
            /* @var Marker $marker */
            $markersCode .= sprintf(
                'var marker_%s = L.marker([%s, %s]).addTo(map);',
                $index,
                $marker->getLatitude() ?: $locationLatitude,
                $marker->getLongitude() ?: $locationLongitude
            );

            if ($marker->getPopup()) {
                $markersCode .= sprintf(
                    "marker_%s.bindPopup('%s');",
                    $index,
                    $marker->getPopup()
                );
            }
        }

        $jsScript = <<<JS
var map = L.map('leaflet_map_$model->id').setView([$locationLatitude, $locationLongitude], 13);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
}).addTo(map);

            $markersCode
JS;

        if ($model->acceptLoad) {
            $jsScript = <<<JS
var sessionStorageKey = 'leafletMapBundle-acceptLoad';

document.addEventListener('click', function (event){
   var target = event.target;

   if (target.classList.contains('leaflet-map__data-privacy-accept')) {
       acceptMap();
   }
});

function acceptMap() {
    sessionStorage.setItem(sessionStorageKey, true);

    initMap();
}

function initMap() {
    $jsScript
}

if (sessionStorage.getItem(sessionStorageKey)) {
    initMap();
}

JS;
        }

        return $jsScript;
    }
}
