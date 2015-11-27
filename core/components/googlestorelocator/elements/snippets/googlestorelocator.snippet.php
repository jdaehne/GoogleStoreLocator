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
    
            if ($distance <= $radius){
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

    $resources = $modx->getCollection('modResource', array(
        'parent:IN' => $parents,
        'deleted' => false,
        'published' => true,
        ));
    
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
        $address = str_replace(' ', '+', $address);
        $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false');
        $output = json_decode($geocode);
        $lat = (!empty($output->results[0]->geometry->location->lat) ? $output->results[0]->geometry->location->lat : 0);
        $lng = (!empty($output->results[0]->geometry->location->lng) ? $output->results[0]->geometry->location->lng : 0);
    
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
    
    //Storelist to cache
    $modx->cacheManager->set('stores', $stores, 31556926); 
}


//Getting Storelist of the cache
$stores =  $modx->cacheManager->get('stores');


//Total
$total = count($stores);


//If getting userPosition - sort Stores
if (!empty($_REQUEST['lat']) and !empty($_REQUEST['lng'])) {
    $stores = filterStoresByDistance($stores, 999999, $_REQUEST['lat'], $_REQUEST['lng'], $unit);
    
    //Centering Map to Position
    $lat_center = $_REQUEST['lat'];
    $lng_center = $_REQUEST['lng'];
    $zoom = $zoom_position;
}



//If search-Form is send - sort Stores
//Get lat & lng of Input Addresss & Limit Store List to Radius
if (!empty($_REQUEST['location'])){
    $address = str_replace(' ', '+', $_REQUEST['location']);
    $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false');
    $output = json_decode($geocode);
    $lat = $output->results[0]->geometry->location->lat;
    $lng = $output->results[0]->geometry->location->lng;
    $radius = (int) $_REQUEST['radius'];
    
    //Centering Map to Position
    $lat_center = $lat;
    $lng_center = $lng;
    $zoom = $zoom_search;
    
    //Get Filtered Stores
    $stores = filterStoresByDistance($stores, $radius, $lat, $lng, $unit);
}


//Form to Placeholder
$formOutput = $modx->getChunk($tpl_form, array(
	'location' => $_REQUEST['location'],
	'radius' => isset($_REQUEST['radius']) ? $_REQUEST['radius'] : $default_radius,
));


//Storelist + 
//Map Info Window +
//Map Marker to Placeholder
if (is_array($stores)) {
    foreach ($stores as $store) {
        //Storelist
        $storeListOutput .= $modx->getChunk($tpl_store, $store[placeholder]);
   
        //Info Window
        $mapMarkerInfoOutput = $modx->getChunk($tpl_marker, $store[placeholder]);
        
        //Marker setzen
        $mapMarkerOutput .= $modx->getChunk('gslMapMarkerTpl', array(
            'lat' => $store[lat],
            'long' => $store[long],
            'content' => $mapMarkerInfoOutput,
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
));


// Parse output to placeholders
$modx->toPlaceHolders(array(
	'stores' => $storeListOutput,
	'form' => $formOutput,
	'map' => $mapOutput,
	'total' => $total,
	'totalResult' => count($stores),
), 'gsl');