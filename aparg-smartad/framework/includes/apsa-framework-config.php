<?php

defined('ABSPATH') or die('No script kiddies please!');
/**
 * The base configurations of the framework.
 */
// Aparg site link
define("APSA_APARG_LINK", "https://aparg.com/");

// framework version
global $apsa_framework_version;
$apsa_framework_version = '1.11';

// framework db version
global $apsa_framework_db_version;
$apsa_framework_db_version = '1.0';

// tables and options names for framework
global $apsa_uninstall;
$apsa_uninstall = array(
    'apsa_tables' => array(
        'apsa_campaigns',
        'apsa_campaign_options',
        'apsa_element_statistics',
		'apsa_elements',
        'apsa_element_options'
    ),
    'apsa_options' => array(
        'widget_apsa_campaign',
        'apsa_extra_options',
        'apsa_framework_version',
        'apsa_framework_db_version'
    )
);

/*
 * analytic event names 
 */
global $apsa_fr_event_name;
$apsa_fr_event_name = 'view';

/*
 * animation effects
 */
global $apsa_effects;
$apsa_effects = array();

$apsa_popup_effects = array();
$apsa_embed_effects = array();
$apsa_sticky_effects = array();
$apsa_sticky_positions = array();
$apsa_overlay_patterns = array();

// popup effects
$apsa_popup_effects['fade'] = array('fadeIn animated', 'fadeOut animated', 0.2);
$apsa_popup_effects['fadeDownBig'] = array('fadeInDownBig animated', 'fadeOutDownBig animated', 0.6);
$apsa_popup_effects['fadeLeftBig'] = array('fadeInLeftBig animated', 'fadeOutLeftBig animated', 0.6);
$apsa_popup_effects['fadeRightBig'] = array('fadeInRightBig animated', 'fadeOutRightBig animated', 0.6);
$apsa_popup_effects['fadeUpBig'] = array('fadeInUpBig animated', 'fadeOutUpBig animated', 0.6);
$apsa_popup_effects['fadeDown'] = array('fadeInDown animated', 'fadeOutDown animated', 0.2);
$apsa_popup_effects['fadeLeft'] = array('fadeInLeft animated', 'fadeOutLeft animated', 0.2);
$apsa_popup_effects['fadeRight'] = array('fadeInRight animated', 'fadeOutRight animated', 0.2);
$apsa_popup_effects['fadeUp'] = array('fadeInUp animated', 'fadeOutUp animated', 0.2);

$apsa_popup_effects['bounceSmall'] = array('bounce animated', 'fadeOut animated', 0.2);
$apsa_popup_effects['flash'] = array('flash animated', 'fadeOut animated', 0.2);
$apsa_popup_effects['pulse'] = array('pulse animated', 'fadeOut animated', 0.2);
$apsa_popup_effects['rubberBand'] = array('rubberBand animated', 'fadeOut animated', 0.2);
$apsa_popup_effects['shake'] = array('shake animated', 'fadeOut animated', 0.2);
$apsa_popup_effects['swing'] = array('swing animated', 'fadeOut animated', 0.2);
$apsa_popup_effects['tada'] = array('tada animated', 'fadeOut animated', 0.2);
$apsa_popup_effects['wobble'] = array('wobble animated', 'fadeOut animated', 0.2);
$apsa_popup_effects['jello'] = array('jello animated', 'fadeOut animated', 0.2);
$apsa_popup_effects['hinge'] = array('fadeIn animated', 'hinge animated');

$apsa_popup_effects['bounce'] = array('bounceIn animated', 'bounceOut animated');
$apsa_popup_effects['bounceUp'] = array('bounceInUp animated', 'bounceOutUp animated', 0.3);
$apsa_popup_effects['bounceDown'] = array('bounceInDown animated', 'bounceOutDown animated', 0.3);
$apsa_popup_effects['bounceLeft'] = array('bounceInLeft animated', 'bounceOutLeft animated', 0.3);
$apsa_popup_effects['bounceRight'] = array('bounceInRight animated', 'bounceOutRight animated', 0.3);

$apsa_popup_effects['flip'] = array('flip animated', 'fadeOut animated', 0.2);
$apsa_popup_effects['flipX'] = array('flipInX animated', 'flipOutX animated', 0.1);
$apsa_popup_effects['flipY'] = array('flipInY animated', 'flipOutY animated', 0.1);

$apsa_popup_effects['lightSpeed'] = array('lightSpeedIn animated', 'lightSpeedOut animated');

$apsa_popup_effects['rotate'] = array('rotateIn animated', 'rotateOut animated', 0.2);
$apsa_popup_effects['rotateDownLeft'] = array('rotateInDownLeft animated', 'rotateOutDownLeft animated', 0.2);
$apsa_popup_effects['rotateDownRight'] = array('rotateInDownRight animated', 'rotateOutDownRight animated', 0.2);
$apsa_popup_effects['rotateUpLeft'] = array('rotateInUpLeft animated', 'rotateOutUpLeft animated', 0.2);
$apsa_popup_effects['rotateUpRight'] = array('rotateInUpRight animated', 'rotateOutUpRight animated', 0.2);

$apsa_popup_effects['slideUp'] = array('slideInUp animated', 'slideOutUp animated', 0.2);
$apsa_popup_effects['slideDown'] = array('slideInDown animated', 'slideOutDown animated', 0.2);
$apsa_popup_effects['slideLeft'] = array('slideInLeft animated', 'slideOutLeft animated', 0.2);
$apsa_popup_effects['slideRight'] = array('slideInRight animated', 'slideOutRight animated', 0.2);

$apsa_popup_effects['zoom'] = array('zoomIn animated', 'zoomOut animated', 0.4);
$apsa_popup_effects['zoomDown'] = array('zoomInDown animated', 'zoomOutDown animated', 0.1);
$apsa_popup_effects['zoomLeft'] = array('zoomInLeft animated', 'zoomOutLeft animated', 0.1);
$apsa_popup_effects['zoomUp'] = array('zoomInUp animated', 'zoomOutUp animated', 0.1);
$apsa_popup_effects['zoomRight'] = array('zoomInRight animated', 'zoomOutRight animated', 0.1);

$apsa_popup_effects['roll'] = array('rollIn animated', 'rollOut animated', 0.2);

$apsa_effects['popup'] = $apsa_popup_effects;

// embed effects
$apsa_embed_effects['fade'] = 'fadeIn animated';
$apsa_embed_effects['fadeDown'] = 'fadeInDown animated';
$apsa_embed_effects['fadeLeft'] = 'fadeInLeft animated';
$apsa_embed_effects['fadeRight'] = 'fadeInRight animated';
$apsa_embed_effects['fadeUp'] = 'fadeInUp animated';

$apsa_embed_effects['bounceSmall'] = 'bounce animated';
$apsa_embed_effects['flash'] = 'flash animated';
$apsa_embed_effects['pulse'] = 'pulse animated';
$apsa_embed_effects['rubberBand'] = 'rubberBand animated';
$apsa_embed_effects['shake'] = 'shake animated';
$apsa_embed_effects['swing'] = 'swing animated';
$apsa_embed_effects['tada'] = 'tada animated';
$apsa_embed_effects['wobble'] = 'wobble animated';
$apsa_embed_effects['jello'] = 'jello animated';

$apsa_embed_effects['flip'] = 'flip animated';
$apsa_embed_effects['flipX'] = 'flipInX animated';
$apsa_embed_effects['flipY'] = 'flipInY animated';

$apsa_embed_effects['bounce'] = 'bounceIn animated';

$apsa_embed_effects['lightSpeed'] = 'lightSpeedIn animated';

$apsa_embed_effects['rotate'] = 'rotateIn animated';
$apsa_embed_effects['rotateDownLeft'] = 'rotateInDownLeft animated';
$apsa_embed_effects['rotateDownRight'] = 'rotateInDownRight animated';
$apsa_embed_effects['rotateUpLeft'] = 'rotateInUpLeft animated';
$apsa_embed_effects['rotateUpRight'] = 'rotateInUpRight animated';

$apsa_embed_effects['slideUp'] = 'slideInUp animated';
$apsa_embed_effects['slideDown'] = 'slideInDown animated';
$apsa_embed_effects['slideLeft'] = 'slideInLeft animated';
$apsa_embed_effects['slideRight'] = 'slideInRight animated';

$apsa_embed_effects['zoom'] = 'zoomIn animated';
$apsa_embed_effects['zoomDown'] = 'zoomInDown animated';
$apsa_embed_effects['zoomLeft'] = 'zoomInLeft animated';
$apsa_embed_effects['zoomRight'] = 'zoomInRight animated';
$apsa_embed_effects['zoomUp'] = 'zoomInUp animated';

$apsa_embed_effects['roll'] = 'rollIn animated';

$apsa_effects['embed'] = $apsa_embed_effects;

// sticky effects

$apsa_effects['sticky'] = $apsa_popup_effects;

$apsa_sticky_positions['top_left'] = 'top-left';
$apsa_sticky_positions['top_center'] = 'top-center';
$apsa_sticky_positions['top_right'] = 'top-right';
$apsa_sticky_positions['middle_left'] = 'middle-left';
$apsa_sticky_positions['middle_right'] = 'middle-right';
$apsa_sticky_positions['bottom_left'] = 'bottom-left';
$apsa_sticky_positions['bottom_center'] = 'bottom-center';
$apsa_sticky_positions['bottom_right'] = 'bottom-right';

$apsa_effects['sticky_positions'] = $apsa_sticky_positions;

// overlay patterns
$apsa_overlay_patterns['3px Tile'] = '3px-tile.png';
$apsa_overlay_patterns['absurdity'] = 'absurdity.png';
$apsa_overlay_patterns['asfalt Dark'] = 'asfalt-dark.png';
$apsa_overlay_patterns['black Linen'] = 'black-linen.png';
$apsa_overlay_patterns['bright Squares'] = 'bright-squares.png';
$apsa_overlay_patterns['carbon Fibre'] = 'carbon-fibre.png';
$apsa_overlay_patterns['cardboard Flat'] = 'cardboard-flat.png';
$apsa_overlay_patterns['connected'] = 'connected.png';
$apsa_overlay_patterns['cubes'] = 'cubes.png';
$apsa_overlay_patterns['dark Circles'] = 'dark-circles.png';
$apsa_overlay_patterns['diamond Upholstery'] = 'diamond-upholstery.png';
$apsa_overlay_patterns['dimension'] = 'dimension.png';
$apsa_overlay_patterns['dust'] = 'dust.png';
$apsa_overlay_patterns['escheresque'] = 'escheresque.png';
$apsa_overlay_patterns['gplay'] = 'gplay.png';
$apsa_overlay_patterns['hexellence'] = 'hexellence.png';
$apsa_overlay_patterns['large Leather'] = 'large-leather.png';
$apsa_overlay_patterns['light Honeycomb'] = 'light-honeycomb.png';
$apsa_overlay_patterns['light Wool'] = 'light-wool.png';
$apsa_overlay_patterns['shattered'] = 'shattered.png';
$apsa_overlay_patterns['strange Bullseyes'] = 'strange-bullseyes.png';
$apsa_overlay_patterns['subtle White Feathers'] = 'subtle-white-feathers.png';
$apsa_overlay_patterns['vaio'] = 'vaio.png';
$apsa_overlay_patterns['white Wall'] = 'white-wall.png';
$apsa_overlay_patterns['brick Wall'] = 'brick-wall.png';

$apsa_effects['patterns'] = $apsa_overlay_patterns;

/*
 * plugin names for anticache
 */
global $apsa_cache_plugins;
$apsa_cache_plugins = array(
    'WP Super Cache',
    'WP Fastest Cache',
    'Comet Cache',
    'Cache Enabler',
    'Hyper Cache',
    'W3 Total Cache',
    'Cachify',
    'WP Fast Cache',
    'Super Static Cache',
    'Gator Cache'
);
