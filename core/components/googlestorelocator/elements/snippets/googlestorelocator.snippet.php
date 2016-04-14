<?php
// Configuration parameters
$parents = array_unique(explode(',', $modx->getOption('parents', $scriptProperties, $modx->resource->get('id'), true)));
$tvname_zip = $modx->getOption('tvNameZip', $scriptProperties, 'gslZip', true);
$tvname_city = $modx->getOption('tvNameCity', $scriptProperties, 'gslCity', true);
$tvname_street = $modx->getOption('tvNameStreet', $scriptProperties, 'gslStreet', true);
$tvname_state = $modx->getOption('tvNameState', $scriptProperties, 'gslState', true);
$tvname_country = $modx->getOption('tvNameCountry', $scriptProperties, 'gslCountry', true);
$include_tvs = $modx->getOption('includeTVs', $scriptProperties, false, true);
$tv_prefix = $modx->getOption('tvPrefix', $scriptProperties, 'tv', true);
$unit = $modx->getOption('unit', $scriptProperties, 'K', true);
$default_radius = $modx->getOption('defaultRadius', $scriptProperties, 20, true);
$limit = $modx->getOption('limit', $scriptProperties, 0, true);
$offset = $modx->getOption('offset', $scriptProperties, 0, true);
$location = $modx->getOption('location', $scriptProperties);
$location_radius = $modx->getOption('locationRadius', $scriptProperties, 0, true);
$marker_image = $modx->getOption('markerImage', $scriptProperties);
$marker_image_location = $modx->getOption('markerImageLocation', $scriptProperties, 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png', true);
$tv_filter = $modx->getOption('tvFilter', $scriptProperties);
$sortby = $modx->getOption('sortby', $scriptProperties, 'menuindex', true);
$sortby_tv = $modx->getOption('sortbyTV', $scriptProperties);
$region = $modx->getOption('region', $scriptProperties);


// Templating parameters
$tpl_form = $modx->getOption('tplForm', $scriptProperties, 'gslFormTpl', true);
$tpl_store = $modx->getOption('tplStore', $scriptProperties, 'gslStoreTpl', true);
$tpl_marker = $modx->getOption('tplMarker', $scriptProperties, 'gslMapMarkerInfoTpl', true);
$tpl_noresult = $modx->getOption('tplNoResult', $scriptProperties, 'gslNoResultTpl', true);


// Map parameters
$zoom = $modx->getOption('zoom', $scriptProperties, 8, true);
$zoom_search = $modx->getOption('zoomSearch', $scriptProperties, 11, true);
$zoom_position = $modx->getOption('zoomPosition', $scriptProperties, 11, true);
$lat_center = $modx->getOption('latCenter', $scriptProperties, '49.14721', true);
$lng_center = $modx->getOption('lngCenter', $scriptProperties, '8.2202', true);
$map_css = $modx->getOption('mapCSS', $scriptProperties, 'height: 400px; margin: 30px 0;', true);
$map_style = $modx->getOption('mapStyle', $scriptProperties, '', true);
$auto_zoom_center = $modx->getOption('autoZoomCenter', $scriptProperties);



// **********************************************************************
//Functions
//Function calculates distance between two points
if(!function_exists(distance)) {
	function distance($lat1, $lng1, $lat2, $lon2, $unit) {
      $theta = $lng1 - $lon2;
      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
      $unit = strtoupper($unit);
    
      if ($unit == "K") {
        return ($miles * 1.609344);
      } else if ($unit == "N") {
          return ($miles * 0.8684);
        } else {
            return $miles;
          }
    }
}

//Filters and orders Stores by Radius and given Position    
if(!function_exists(filterStoresByDistance)) {
	function filterStoresByDistance($stores, $radius, $lat, $lng, $unit = "K") {    
        //Check if Store is in the radius
        foreach ($stores as $id => $store) {
            $distance = distance($lat, $lng, $store['lat'], $store['long'], $unit);
            $distance = intval($distance);
    
            if ($distance <= $radius or $radius == 0){
                $stores_radius[$id] = $distance;
            }
        }
        
        //Sort Radius-Store-List for Distance and rebuild stores-Array
        asort($stores_radius);
        foreach ($stores_radius as $id => $store) {
            $stores[$id][placeholder][distance] = $store;
            $stores_tmp[] = $stores[$id];
        }
        
        return $stores_tmp;
	}
}
// **********************************************************************


//If default Storelist is not chached: create
if ($modx->cacheManager->get('stores') != true){

    $c = $modx->newQuery('modResource');
    $c->sortby($sortby, "ASC");
    $c->where(array(
        'parent:IN' => $parents,
        'deleted' => false,
        'published' => true,
    ));
    $resources = $modx->getCollection('modResource', $c);
    
    $stores = array();
    
    foreach($resources as $item) {
        $page = $modx->getObject('modResource', $item->id);
        
        $fields = $item->toArray();

        //Creating Lat & Lng of Store
        unset($address);
        $address .= ($page->getTVValue($tvname_street) != '' ? $page->getTVValue($tvname_street).',' : '');
        $address .= ($page->getTVValue($tvname_zip) != '' ? $page->getTVValue($tvname_zip).',' : '');
        $address .= ($page->getTVValue($tvname_city) != '' ? $page->getTVValue($tvname_city).',' : '');
        $address .= ($page->getTVValue($tvname_state) != '' ? $page->getTVValue($tvname_state).',' : '');
        $address .= ($page->getTVValue($tvname_country) != '' ? $page->getTVValue($tvname_country).',' : '');
        $address = urlencode($address);
        $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false');
        $output = json_decode($geocode);
        $lat = str_replace(",", ".", (!empty($output->results[0]->geometry->location->lat) ? $output->results[0]->geometry->location->lat : 0));
        $lng = str_replace(",", ".", (!empty($output->results[0]->geometry->location->lng) ? $output->results[0]->geometry->location->lng : 0));
    
        unset($placeholder);

        //Get included Tvs to placeholder
        if ($include_tvs != false){
            $tvs = explode(",", $include_tvs);
            $placeholder = array();
            foreach ($tvs as $tv){
                $placeholder[$tv_prefix.'.'.$tv] = $page->getTVValue($tv);
            }
        }
        
        //Set other placeholders
        $placeholder[zip] = $page->getTVValue($tvname_zip);
        $placeholder[city] = $page->getTVValue($tvname_city);
        $placeholder[street] = $page->getTVValue($tvname_street);
        $placeholder[country] = $page->getTVValue($tvname_country);
        $placeholder[state] = $page->getTVValue($tvname_state);
        $placeholder[lat] = $lat;
        $placeholder[long] = $lng;
        
        $placeholder = array_merge($fields, $placeholder);
        
        //Let the user see all available placeholders with the placeholder: "placehiolders"
        $placeholder["placeholders"] = print_r($placeholder,true);

        $element = array(
            lat => $lat,
            long => $lng,
            placeholder => $placeholder,
            );
           
        $stores[] = $element;
    }

    //Sorting Stores by TV
    if (!empty($sortby_tv) and isset($stores[0][placeholder]["tv.".$sortby_tv])) {
        foreach ($stores as $id => $store) {
            $stores_sort[$id] = $store[placeholder]["tv.".$sortby_tv];
        }
        asort($stores_sort);
        unset($stores_tmp);
        foreach ($stores_sort as $id => $store) {
            $stores[$id][placeholder]["tv.".$sortby_tv] = $store;
            $stores_tmp[] = $stores[$id];
        }
        $stores = $stores_tmp;
    }
    
    //Storelist to cache
    $modx->cacheManager->set('stores', $stores, 31556926); 
}


//Getting Storelist of the cache
$stores =  $modx->cacheManager->get('stores');


//Total
$total = count($stores);


//If getting userPosition - sort Stores
if (!empty($_REQUEST['lat']) and !empty($_REQUEST['lng'])) {
    $stores = filterStoresByDistance($stores, 0, $_REQUEST['lat'], $_REQUEST['lng'], $unit);
    
    //Centering Map to Position
    $lat_center = $_REQUEST['lat'];
    $lng_center = $_REQUEST['lng'];
    $zoom = $zoom_position;
}



//If search-Form is send - sort Stores
//Get lat & lng of Input Addresss or location-Property & Limit Store List to Radius
if (!empty($_REQUEST['location']) or !empty($location)){
    $address = urlencode(!empty($_REQUEST['location']) ? $_REQUEST['location'] : $location);
    $region = $region ? '&region='.$region : '';
    $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false'.$region);
    $output = json_decode($geocode);
    $lat = str_replace(",", ".", $output->results[0]->geometry->location->lat);
    $lng = str_replace(",", ".", $output->results[0]->geometry->location->lng);
    $radius = (int) isset($_REQUEST['radius']) ? $_REQUEST['radius'] : $location_radius;

    //Centering Map to Position
    $lat_center = $lat;
    $lng_center = $lng;
    $zoom = $zoom_search;
    
    //Get Filtered Stores
    $stores = filterStoresByDistance($stores, $radius, $lat, $lng, $unit);
}


//Filtering Stores BY TV
if (!empty($tv_filter)) { 
    $tmp_stores = array();
    list($tv, $operand) = explode("==", $tv_filter);
    foreach ($stores as $store) {
        if ($store[placeholder]["tv.$tv"] == $operand) {
            $tmp_stores[] = $store;
        }
    }
    $stores = $tmp_stores;
}


//Form to Placeholder
$formOutput = $modx->getChunk($tpl_form, array(
	'location' => $_REQUEST['location'],
	'radius' => isset($_REQUEST['radius']) ? $_REQUEST['radius'] : $default_radius,
));


//Storelist + 
//Map Info Window +
//Map Marker to Placeholder
$i=0;
if (is_array($stores)) {
    foreach ($stores as $store) {
        
        //limiot & offset for pagination
        if ($i++ < $offset) continue;
        if ($i > $offset + $limit and $limit > 0) break;
        
        //Storelist
        $storeListOutput .= $modx->getChunk($tpl_store, $store[placeholder]);
   
        //Info Window
        $mapMarkerInfoOutput = $modx->getChunk($tpl_marker, $store[placeholder]);
        
        //Marker setzen
        $mapMarkerOutput .= $modx->getChunk('gslMapMarkerTpl', array(
            'lat' => $store[lat],
            'long' => $store[long],
            'content' => $mapMarkerInfoOutput,
            'markerImage' => $marker_image,
        ));
    }
}else {
    $storeListOutput = $modx->getChunk($tpl_noresult);
}

//Map to Placeholder
$mapOutput = $modx->getChunk('gslMapTpl', array(
	'marker' => preg_replace( "/\r|\n/", "", $mapMarkerOutput),
	'zoom' => $zoom,
	'latCenter' => $lat_center,
	'longCenter' => $lng_center,
	'mapCSS' => $map_css,
	'apiKey' => $modx->getOption('googlestorelocator_googleapikey'),
	'mapStyle' => $map_style,
	'showLocation' => isset($_REQUEST['location']) ? true : false,
	'markerImageLocation' => $marker_image_location,
	'autoZoomCenter' => $auto_zoom_center,
));


// Parse output to placeholders
$modx->toPlaceHolders(array(
	'stores' => $storeListOutput,
	'form' => $formOutput,
	'map' => $mapOutput,
	'total' => $total,
	'totalResult' => count($stores),
), 'gsl');