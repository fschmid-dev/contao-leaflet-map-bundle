<?php

use FSchmidDev\LeafletMapBundle\FrontendModule\LeafletMapModule;

$GLOBALS['TL_DCA']['tl_module']['palettes'][LeafletMapModule::TYPE] =
    '{title_legend},name,type;'
    . '{config_legend},location;'
    . '{template_legend:hide},customTpl;'
    . '{protected_legend:hide},protected;'
    . '{expert_legend:hide},cssID';

$GLOBALS['TL_DCA']['tl_module']['fields']['location'] = [
    'exclude' => true,
    'inputType' => 'location',
    'sql' => [
        'type' => 'string',
        'default' => NULL,
        'notnull' => false,
    ]
];
