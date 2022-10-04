<?php

use FSchmidDev\LeafletMapBundle\FrontendModule\LeafletMapModule;

$GLOBALS['TL_DCA']['tl_module']['palettes'][LeafletMapModule::TYPE] =
    '{title_legend},name,type;'
    . '{config_legend},location,markers;'
    . '{gdpr_legend},acceptLoad,dataPrivacyInfo,dataPrivacyUrl;'
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
                'label' => &$GLOBALS['TL_LANG']['tl_module']['location'],
                'exclude' => true,
                'inputType' => 'location',
            ],
            'popup' => [
                'label' => &$GLOBALS['TL_LANG']['tl_module']['popup'],
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

$GLOBALS['TL_DCA']['tl_module']['fields']['acceptLoad'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'sql' => [
        'type' => 'string',
        'length' => '1',
        'default' => '',
    ],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['dataPrivacyInfo'] = [
    'exclude' => true,
    'inputType' => 'textarea',
    'eval' => [
        'rte' => 'tinyMCE',
    ],
    'sql' => [
        'type' => 'string',
        'default' => null,
        'notnull' => false,
    ],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['dataPrivacyUrl'] = [
    'exclude'                 => true,
    'inputType'               => 'pageTree',
    'foreignKey'              => 'tl_page.title',
    'eval'                    => array('fieldType'=>'radio'),
    'sql'                     => "int(10) unsigned NOT NULL default 0",
    'relation'                => array('type'=>'hasOne', 'load'=>'lazy')
];
