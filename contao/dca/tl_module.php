<?php

use FSchmidDev\LeafletMapBundle\FrontendModule\LeafletMapModule;

$GLOBALS['TL_DCA']['tl_module']['palettes'][LeafletMapModule::TYPE] =
    '{title_legend},name,type;'
    . '{config_legend},zoomLevel,autoZoom,location,markers;'
    . '{gdpr_legend},acceptLoad,dataPrivacyInfo,dataPrivacyUrl;'
    . '{template_legend:hide},customTpl;'
    . '{protected_legend:hide},protected;'
    . '{expert_legend:hide},cssID';

$GLOBALS['TL_DCA']['tl_module']['fields']['zoomLevel'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => [
        'rgxp'=>'digit',
        'tl_class'=> 'w50 clr',
    ],
    'sql' => [
        'type' => 'integer',
        'default' => 13,
        'notnull' => true,
    ],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['autoZoom'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => [
        'tl_class'=> 'w50 m12',
    ],
    'sql' => [
        'type' => 'string',
        'length' => '1',
        'default' => '',
    ],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['location'] = [
    'exclude' => true,
    'inputType' => 'location',
    'eval' => [
        'tl_class' => 'clr',
    ],
    'sql' => [
        'type' => 'string',
        'default' => NULL,
        'notnull' => false,
    ]
];

$GLOBALS['TL_DCA']['tl_module']['fields']['markers'] = [
    'exclude' => true,
    'inputType' => 'group',
    'palette' => ['location', 'popup'],
    'fields' => [
        '&location' => [],
        'popup' => [
            'inputType' => 'textarea',
            'eval' => ['rte' => 'tinyMCE'],
        ]
    ],
    /*
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
    */
    'sql' => [
        'type' => 'blob',
        'length' => \Doctrine\DBAL\Platforms\MySQLPlatform::LENGTH_LIMIT_BLOB,
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
        'type' => 'text',
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
