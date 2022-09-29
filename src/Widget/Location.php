<?php

namespace FSchmidDev\LeafletMapBundle\Widget;

use Contao\Widget;

class Location extends Widget
{
    public const DEFAULT_DATA = [
        'location' => '',
        'location_latitude' => '',
        'location_longitude' => '',
    ];

    protected $strTemplate = 'be_widget';

    protected $blnSubmitInput = true;

    public function validate(): void
    {
        $data = self::DEFAULT_DATA;
        $data['location'] = $this->getPost($this->strName);
        $data['location_latitude'] = $this->getPost(
            str_replace('location', 'location_latitude', $this->strName),
        );
        $data['location_longitude'] = $this->getPost(
            str_replace('location', 'location_longitude', $this->strName),
        );

        $this->varValue = json_encode($data, JSON_THROW_ON_ERROR);
    }

    public function generate(): string
    {
        $value = $this->varValue;
        if (is_string($value)) {
            $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        }
        $data = self::DEFAULT_DATA;
        if ($value) {
            $data = array_merge($data, $value);
        }

        // Location - Address
        $arrFields[] = sprintf(
            '<input type="text" name="%s" id="ctrl_%s" class="tl_text" value="%s" onfocus="Backend.getScrollOffset()">',
            $this->strName,
            $this->strId,
            $data['location'],
        );

        // Location - Latitude
        $arrFields[] = sprintf(
            '<input type="text" name="%s" id="ctrl_%s" class="tl_text_3" value="%s" onfocus="Backend.getScrollOffset()">',
            str_replace('location', 'location_latitude', $this->strName),
            $this->strId . '_latitude',
            $data['location_latitude'],
        );

        // Location - Longitude
        $arrFields[] = sprintf(
            '<input type="text" name="%s" id="ctrl_%s" class="tl_text_3" value="%s" onfocus="Backend.getScrollOffset()">',
            str_replace('location', 'location_longitude', $this->strName),
            $this->strId . '_longitude',
            $data['location_longitude'],
        );

        return sprintf(
            '<div id="ctrl_%s" class="tl_locations%s">%s</div>',
            $this->strId,
            ($this->strClass ? ' ' . $this->strClass : ''),
            implode('', $arrFields)
        );
    }
}

class_alias(Location::class, 'Location');