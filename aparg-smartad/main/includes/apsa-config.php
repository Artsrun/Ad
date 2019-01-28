<?php

defined('ABSPATH') or die('No script kiddies please!');
/**
 * The base configurations of the SmartAd.
 */
// smartad db version
global $apsa_db_version;
$apsa_db_version = '1.1';

// smartad plugin version
global $apsa_plugin_version;
$apsa_plugin_version = '1.7';

// tables and options names for Smartad
global $apsa_child_uninstall;
$apsa_child_uninstall = array(
    'apsa_tables' => array(
        'apsa_custom_ads'
    ),
    'apsa_options' => array(
        'apsa_db_version',
        'apsa_plugin_version',
        'apsa_options'
    )
);

global $apsa_plugin_data;
$apsa_plugin_data = array(
    'plugin_data' => array(
        'name' => 'SmartAd',
        'icon_path' => plugin_dir_url(__FILE__) . '../view/admin/images/plugin-icon.png'
    ),
    'element_data' => array(
        array(
            'name' => 'image',
            'icon_path' => plugin_dir_url(__FILE__) . '../view/admin/images/commertials-types/image.png',
            'campaign_types' => array('background', 'embed', 'popup', 'sticky')
        ),
        array(
            'name' => 'video',
            'icon_path' => plugin_dir_url(__FILE__) . '../view/admin/images/commertials-types/video.png',
            'campaign_types' => array('embed', 'popup', 'sticky')
        ),
        array(
            'name' => 'flash',
            'icon_path' => plugin_dir_url(__FILE__) . '../view/admin/images/commertials-types/flash.png',
            'campaign_types' => array('embed', 'popup', 'sticky')
        ),
        array(
            'name' => 'code',
            'icon_path' => plugin_dir_url(__FILE__) . '../view/admin/images/commertials-types/html.png',
            'campaign_types' => array('embed', 'popup', 'sticky')
        ),
        array(
            'name' => 'iframe',
            'icon_path' => plugin_dir_url(__FILE__) . '../view/admin/images/commertials-types/iframe.png',
            'campaign_types' => array('embed', 'popup', 'sticky')
        ),
          array(
            'name' => 'custom',//===========Custom=====
            'icon_path' => plugin_dir_url(__FILE__) . '../view/admin/images/commertials-types/custom.png',
            'campaign_types' => array('embed', 'popup', 'sticky')
        )
    ),
    'anticache' => 'true',
    'campaign_types' => array(
        'background' => 'true',
        'embed' => 'true',
        'popup' => 'true',
        'sticky' => 'true'
    ),
    'visual_composer' => array(
        'enable' => 'true',
        'icon_path' => plugin_dir_url(__FILE__) . '../view/admin/images/composer-icon.png'
    ),
    'event_name' => 'visit',
);
