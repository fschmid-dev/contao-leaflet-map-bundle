<?php

namespace FSchmidDev\LeafletMapBundle\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\ModuleModel;
use Contao\StringUtil;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(LeafletMapModule::TYPE, category: "miscellaneous")]
class LeafletMapModule extends AbstractFrontendModuleController
{
    public const TYPE = 'leaflet_map';

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $data = json_decode($model->location, true, 512, JSON_THROW_ON_ERROR);
        ['location' => $location, 'location_latitude' => $locationLatitude, 'location_longitude' => $locationLongitude] = $data;

        $markersCode = '';
        $markers = StringUtil::deserialize($model->markers);
        foreach ($markers as $index => $marker) {
            $markersCode .= sprintf(
                'var marker_%s = L.marker([%s, %s]).addTo(map);',
                $index,
                $marker['location_latitude'] ?: $locationLatitude,
                $marker['location_longitude'] ?: $locationLongitude
            );

            if (isset($marker['popup']) && $marker['popup'] !== '') {
                $markersCode .= sprintf(
                    "marker_%s.bindPopup('%s');",
                    $index,
                    $marker['popup']
                );
            }
        }

        $GLOBALS['TL_CSS'][] = 'bundles/fschmiddevleafletmap/leaflet/leaflet.css';
        $GLOBALS['TL_BODY'][] = Template::generateScriptTag('bundles/fschmiddevleafletmap/leaflet/leaflet.js');
        $GLOBALS['TL_BODY'][] = Template::generateInlineScript(<<<JS
            var map = L.map('leaflet_map_$model->id').setView([$locationLatitude, $locationLongitude], 13);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);
            
            $markersCode
JS);

        return new Response($template->parse());
    }
}