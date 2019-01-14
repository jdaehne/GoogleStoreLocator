<?php

/**
 * GoogleStoreLocator
 *
 * A dynamic Store Locator Snippet for MODX using the Google-Map-API
 *
 *
 * @package googlestorelocator
 */

// load Class
$modelPath = $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/googlestorelocator/model/googlestorelocator/';
$modx->loadClass('GoogleStoreLocator', $modelPath, true, true);


// Configuration parameters
$config['parents'] = array_unique(explode(',', $modx->getOption('parents', $scriptProperties, $modx->resource->get('id'), true)));
$config['tvname_zipcode'] = $modx->getOption('tvNameZipcode', $scriptProperties, 'gslZipcode', true);
$config['tvname_city'] = $modx->getOption('tvNameCity', $scriptProperties, 'gslCity', true);
$config['tvname_street'] = $modx->getOption('tvNameStreet', $scriptProperties, 'gslStreet', true);
$config['tvname_housenumber'] = $modx->getOption('tvNameHousenumber', $scriptProperties, 'gslHousenumber', true);
$config['tvname_state'] = $modx->getOption('tvNameState', $scriptProperties, 'gslState', true);
$config['tvname_country'] = $modx->getOption('tvNameCountry', $scriptProperties, 'gslCountry', true);
$config['include_tvs'] = $modx->getOption('includeTVs', $scriptProperties, false, true);
$config['tv_prefix'] = $modx->getOption('tvPrefix', $scriptProperties);
$config['tv_prefix'] = isset($config['tv_prefix']) ? trim($config['tv_prefix']) : 'tv.';
$config['unit'] = $modx->getOption('unit', $scriptProperties, 'K', true);
$config['sortby'] = $modx->getOption('sortby', $scriptProperties, 'menuindex', true);
$config['sortdir'] = $modx->getOption('sortdir', $scriptProperties, 'desc', true);
$config['limit'] = (int) $modx->getOption('limit', $scriptProperties, 0, true);
$config['offset'] = (int) $modx->getOption('offset', $scriptProperties, 0, true);
$config['marker_image'] = $modx->getOption('markerImage', $scriptProperties);
$config['marker_image_location'] = $modx->getOption('markerImageLocation', $scriptProperties, 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png', true);
$config['debug'] = $modx->getOption('debug', $scriptProperties, false);
$config['default_radius'] = $modx->getOption('defaultRadius', $scriptProperties, 20, true);
$config['location'] = $modx->getOption('location', $scriptProperties);
$config['location_radius'] = $modx->getOption('locationRadius', $scriptProperties, 20, true);
$config['where'] = $modx->getOption('where', $scriptProperties);
$config['where'] = !empty($config['where']) ? $modx->fromJSON($config['where']) : array();
$config['region'] = $modx->getOption('region', $scriptProperties);
$config['total_var'] = $modx->getOption('totalVar', $scriptProperties, 'total', true);

// Templating parameters
$config['tpl_form'] = $modx->getOption('tplForm', $scriptProperties, 'gslFormTpl', true);
$config['tpl_store'] = $modx->getOption('tplStore', $scriptProperties, 'gslStoreTpl', true);
$config['tpl_map'] = $modx->getOption('tplMap', $scriptProperties, 'gslMapTpl', true);
$config['tpl_map_marker'] = $modx->getOption('tplMapMarker', $scriptProperties, 'gslMapMarkerTpl', true);
$config['tpl_map_marker_content'] = $modx->getOption('tplMapMarkerContent', $scriptProperties, 'gslMapMarkerContentTpl', true);
$config['tpl_noresult'] = $modx->getOption('tplNoResult', $scriptProperties, 'gslNoResultTpl', true);
$config['tpl_message'] = $modx->getOption('tplMessage', $scriptProperties, 'gslMessageTpl', true);

// Map parameters
$config['zoom'] = $modx->getOption('zoom', $scriptProperties, 8, true);
$config['zoom_search'] = $modx->getOption('zoomSearch', $scriptProperties, 11, true);
$config['zoom_position'] = $modx->getOption('zoomPosition', $scriptProperties, 11, true);
$config['lat_center'] = $modx->getOption('latCenter', $scriptProperties, '49.14721', true);
$config['lng_center'] = $modx->getOption('lngCenter', $scriptProperties, '8.2202', true);
$config['map_css'] = $modx->getOption('mapCSS', $scriptProperties, 'height: 400px; margin: 30px 0;', true);
$config['map_style'] = $modx->getOption('mapStyle', $scriptProperties, '', true);
$config['auto_zoom_center'] = $modx->getOption('autoZoomCenter', $scriptProperties);


$storeList = new GoogleStoreLocator($modx, $config);

// get stores
$stores = $storeList->getStores();

// sort stores
$stores = $storeList->sortBy($stores, $config['sortby'], $config['sortdir']);

// get lat and lng of location set by Property or search form
if (!empty($_REQUEST['location']) or !empty($config['location'])) {
    $stores = $storeList->setLocation($stores);
}

// filter stores if set in properties
if (count($config['where'])  >= 1 and is_array($stores) and count($stores) >= 1) {
    $key = explode(':', key($config['where']));
    $operator = $key[1];
    $key = $key[0];
    $value = reset($config['where']);
    $stores = $storeList->filterStores($stores, $key, $value, $operator);
}

// set total placeholder
$modx->setPlaceholder($config['total_var'], count($stores));

// limit and offset stores (array, offset, limit)
if ($config['limit'] > 0) {
    $stores = array_slice($stores, $config['offset'] <= 0 ? 0 : $config['offset'], $config['limit']);
}

// render form
$storeList->renderForm();

// render map
$storeList->renderMap($stores);

// render stores
$storeList->renderStores($stores);

// debug mode
if ($config['debug'] == true) {
    echo '<pre>';
    print_r($stores);
}

return;
