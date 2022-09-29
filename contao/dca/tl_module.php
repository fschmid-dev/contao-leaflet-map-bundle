<?php

use FSchmidDev\LeafletMapBundle\FrontendModule\LeafletMapModule;

$GLOBALS['TL_DCA']['tl_module']['palettes'][LeafletMapModule::TYPE] =
    '{title_legend},name,type;'
    . '{config_legend},location,markers;'
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

$GLOBALS['TL_DCA']['tl_module']['fields']['markers'] = [
    'exclude' => true,
    'inputType' => 'multiColumnWizard',
    'eval' => [
        'columnFields' => [
            'location' => [
                'label' => [0 => false],
                'exclude' => true,
                'inputType' => 'location',
            ],
            'popup' => [
                'label' => [0 => false],
                'exclude'                 => true,
                'inputType'               => 'textarea',
                'eval'                    => [
                    'rte'=>'tinyMCE|tinyMCE'
                    // pipe separator needed for some reason inside multicolumnwizard
                    // see: vendor/menatwork/contao-multicolumnwizard-bundle/src/EventListener/Mcw/TinyMce.php - Line 49
                ],
            ],
        ],
    ],
    'sql' => [
        'type' => 'blob',
        'default' => null,
        'notnull' => false,
    ],
];