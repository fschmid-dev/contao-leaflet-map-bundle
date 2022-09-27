<?php

namespace FSchmidDev\LeafletMapBundle\Widget;

use Contao\Widget;

class Location extends Widget
{
    protected $strTemplate = 'be_widget';

    protected $blnSubmitInput = true;

    public function validate(): void
    {
        $varValue = $this->getPost($this->strName);

        parent::validate();
    }

    public function generate(): string
    {
        $a = 0;

        // <input type="text" name="location" id="ctrl_location" class="tl_text" value="asdf" onfocus="Backend.getScrollOffset()">
        // <input type="text" name="location" id="ctrl_location" class="tl_text_3" value="asdf" onfocus="Backend.getScrollOffset()">

        // Location - Address
        $arrFields[] = sprintf(
            '<input type="text" name="%s" id="ctrl_%s" class="tl_text" value="%s" onfocus="Backend.getScrollOffset()">',
            $this->strName,
            $this->strId,
            $this->varValue
        );

        // Location - Latitude
        $arrFields[] = sprintf(
            '<input type="text" name="%s" id="ctrl_%s" class="tl_text_3" value="%s" onfocus="Backend.getScrollOffset()">',
            $this->strName . '_lat',
            $this->strId . '_lat',
            $this->varValue
        );

        // Location - Longitude
        $arrFields[] = sprintf(
            '<input type="text" name="%s" id="ctrl_%s" class="tl_text_3" value="%s" onfocus="Backend.getScrollOffset()">',
            $this->strName . '_long',
            $this->strId . '_long',
            $this->varValue
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