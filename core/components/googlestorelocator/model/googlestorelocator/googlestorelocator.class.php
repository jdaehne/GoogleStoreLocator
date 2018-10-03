<?php

class GoogleStoreLocator {


    /**
     * GoogleStoreLocator constructor
     *
     * @param MODX $modx A reference to the MODX instance.
     * @param array $options An array of options. Optional.
     */
     public function __construct(MODX $modx, array $config = array())
    {
        // init modx
        $this->modx = $modx;

        // config
        $this->apikeyServer = $this->modx->getOption('googlestorelocator.apikey_server');
        $this->apikeyMap = $this->modx->getOption('googlestorelocator.apikey_map');
        $this->parents = $config['parents'];
        $this->tvname_street = $config['tvname_street'];
        $this->tvname_housenumber = $config['tvname_housenumber'];
        $this->tvname_zipcode = $config['tvname_zipcode'];
        $this->tvname_city = $config['tvname_city'];
        $this->tvname_state = $config['tvname_state'];
        $this->tvname_country = $config['tvname_country'];
        $this->include_tvs = $config['include_tvs'];
        $this->tv_prefix = $config['tv_prefix'];
        $this->unit = $config['unit'];
        $this->tpl_map = $config['tpl_map'];
        $this->tpl_map_marker = $config['tpl_map_marker'];
        $this->tpl_map_marker_content = $config['tpl_map_marker_content'];
        $this->tpl_form = $config['tpl_form'];
        $this->tpl_store = $config['tpl_store'];
        $this->tpl_noresult = $config['tpl_noresult'];
        $this->tpl_message = $config['tpl_message'];
        $this->marker_image = $config['marker_image'];
        $this->marker_image_location = $config['marker_image_location'];
        $this->zoom = $config['zoom'];
        $this->zoom_search = $config['zoom_search'];
        $this->zoom_position = $config['zoom_position'];
        $this->lat_center = $config['lat_center'];
        $this->lng_center = $config['lng_center'];
        $this->map_css = $config['map_css'];
        $this->map_style = $config['map_style'];
        $this->auto_zoom_center = $config['auto_zoom_center'];
        $this->default_radius = $config['default_radius'];
        $this->location = $config['location'];
        $this->location_radius = $config['location_radius'];
        $this->region = $config['region'];
        //$max_execution_time = ini_get('max_execution_time');
    }


    // order/sort stores (name, menuindex, distance, ...)
    public function sortBy($stores, $sortby = 'menuindex', $direction = 'asc')
    {
        if (!is_array($stores)) return false;

        usort($stores, function($a, $b) use ($sortby) {
            return $a[$sortby] - $b[$sortby];
        });

        if ($direction === 'desc'){
            return array_reverse($stores);
        }

        return $stores;
    }

    // get stores
    public function getStores()
    {
        $stores = array();

        // get stores of db
        $c = $this->modx->newQuery('modResource');
        $c->where(array(
            'parent:IN' => $this->parents,
            'deleted' => false,
            'published' => true,
        ));
        $resources = $this->modx->getCollection('modResource', $c);

        foreach($resources as $item) {

            //check if store is in cache
            $storeCache = $this->getCache($item->id);
            if ($storeCache != false) {
                $stores[] = $storeCache;
                continue;
            }

            $page = $this->modx->getObject('modResource', $item->id);
            $fields = $item->toArray();

            $store = array(
                'id' => $item->id,
                'name' => $item->pagetitle,
                'street' => $page->getTVValue($this->tvname_street),
                'housenumber' => $page->getTVValue($this->tvname_housenumber),
                'zipcode' => $page->getTVValue($this->tvname_zipcode),
                'city' => $page->getTVValue($this->tvname_city),
                'state' => $page->getTVValue($this->tvname_state),
                'country' => $page->getTVValue($this->tvname_country),
                'menuindex' => $item->menuindex,
            );

            $store = array_merge($fields, $store);

            // include tvs
            if ($this->include_tvs != false){
                $tvs = explode(",", $this->include_tvs);
                $placeholder = array();
                foreach ($tvs as $tv){
                    $store[$this->tv_prefix.$tv] = $page->getTVValue($tv);
                }
            }

            // add Latitude and Longitude if empty
            if (empty($store['lat']) or empty($store['lng'])) {
                $address = $this->formatAddress($store['street'], $store['housenumber'], $store['zipcode'], $store['city'], $store['state'], $store['country']);
                $latlng = $this->getLngLat($address);
                $store = $stores[$key] = array_merge($store, $latlng);
            }

            if (!empty($store['lat']) and !empty($store['lng'])) {
                $this->addCache($store);
            }

            $stores[] = $store;

        }

        return $stores;
    }


    // adds distance to stores
    public function addDistanceToStores($stores, $lat, $lng, $unit = 'K')
    {
        if (!is_array($stores)) return false;

        foreach ($stores as $key => $store) {
            $distance = NULL;
            if (!empty($store['lat']) and !empty($store['lng'])) {
                $distance = $this->distance($lat, $lng, $store['lat'], $store['lng'], $unit);
            }
            $stores[$key]['distance'] = $distance;
        }

        return $stores;
    }



    // calculates distance between two points
    public function distance($lat1, $lng1, $lat2, $lng2, $unit = 'K')
    {
        $theta = $lng1 - $lng2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        // return distance in km, nautical or miles
        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }


    // formats an Address into a String
    public function formatAddress($street,$housenumber,$zipcode,$city,$state,$country)
    {
        return $street . ' ' . $housenumber . ', ' . $zipcode . ' ' . $city. ', ' . $state . ', ' . $country;
    }

    // get lat lng from Google
    public function getLngLat($address)
    {
        $address = urlencode($address);
        $region = $this->region ? '&region='.$this->region : '';
        $url = 'https://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false&key='.$this->apikeyServer . $region;
        $geojson = file_get_contents($url);
        $geodata = json_decode($geojson);

        if ($geodata->status !== 'OK') {
            // set error
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, '[GoogleStoreLocator] Could not get Geo-Data of Address: ' . $address . ' Errormessage: ' . $geodata->error_message);
        }

        $lat = str_replace(",", ".", $geodata->results[0]->geometry->location->lat);
        $lng = str_replace(",", ".", $geodata->results[0]->geometry->location->lng);

        $data = array(
            'lat' => $lat,
            'lng' => $lng,
            'address' => $geodata->results[0]->formatted_address,
        );

        return $data;
    }

    // set location by user input or property
    public function setLocation($stores)
    {
        $address = urlencode(!empty($_REQUEST['location']) ? $_REQUEST['location'] : $this->location);
        $radius = (int) isset($_REQUEST['radius']) ? $_REQUEST['radius'] : $this->location_radius;

        $locationData = $this->getLngLat($address);

        if (!empty($locationData['lat']) and !empty($locationData['lng'])) {
            if (count($stores) >= 1 and is_array($stores)) {
                // add distance
                $stores = $this->addDistanceToStores($stores, $locationData['lat'], $locationData['lng']);

                // filter stores by radius
                $stores = $this->filterStores($stores, 'distance', $radius);
            }

            $this->lat_center = $locationData['lat'];
            $this->lng_center = $locationData['lng'];
        }else {
            $stores = array();
        }

        if (!empty($_REQUEST['location'])) {
            $message = $this->modx->getChunk($this->tpl_message, array(
                'address' => $locationData['address'],
            ));

            $this->modx->toPlaceholders(array(
                'message' => $message,
            ), 'gsl');
        }

        return $stores;
    }

    // render map
    public function renderMap($stores)
    {
        $mapMarkerOutput = '';

        if (count($stores) >= 1 and is_array($stores)) {
            foreach ($stores as $store) {
                if (!empty($store['lat']) and !empty($store['lng'])) {

                    // Replacing/escaping single quotes for the js-map
                    $jsStorePlaceholder = array();
                    foreach ($store as $key => $value) {
                        $jsStorePlaceholder[$key] = str_replace("'","\\'", $value);
                    }

                    $mapMarkerInfoOutput = $this->modx->getChunk($this->tpl_map_marker_content, $jsStorePlaceholder);

                    $mapMarkerOutput .= $this->modx->getChunk($this->tpl_map_marker, array(
                        'lat' => $store['lat'],
                        'long' => $store['lng'],
                        'content' => $mapMarkerInfoOutput,
                        'markerImage' => $this->marker_image,
                    ));
                }
            }
        }

        $mapOutput = $this->modx->getChunk($this->tpl_map, array(
        	'marker' => preg_replace( "/\r|\n/", "", $mapMarkerOutput),
        	'zoom' => $this->zoom,
        	'latCenter' => $this->lat_center,
        	'longCenter' => $this->lng_center,
        	'mapCSS' => $this->map_css,
        	'apiKey' => $this->apikeyMap,
        	'mapStyle' => $this->map_style,
        	'showLocation' => isset($_REQUEST['location']) ? true : false,
        	'markerImageLocation' => $this->marker_image_location,
        	'autoZoomCenter' => (count($stores) >= 1 and is_array($stores)) ? $this->auto_zoom_center : '',
        ));

        $this->modx->toPlaceholders(array(
            'map' => $mapOutput,
        ), 'gsl');
    }

    // filter stores (storesArray, placeholder/variable name, value, operator)
    public function filterStores($stores, $key, $value, $operator = '<=')
    {
        if (!is_array($stores)) return false;

        $this->key = $key;
        $this->value = $value;
        $this->operator = $operator;

        $stores = array_filter($stores, function ($var) {
            switch ($this->operator) {
                case '==':
                case '=':
                    return ($var[$this->key] == $this->value);
                    break;
                case '>':
                    return ($var[$this->key] > $this->value);
                    break;
                case '>=':
                    return ($var[$this->key] >= $this->value);
                    break;
                case '<':
                    return ($var[$this->key] < $this->value);
                    break;
                case '<=':
                default:
                    return ($var[$this->key] <= $this->value);
                    break;
            }

        });

        return $stores;
    }

    // render form
    public function renderForm()
    {
        $formOutput = $this->modx->getChunk($this->tpl_form, array(
        	'location' => $_REQUEST['location'],
        	'radius' => isset($_REQUEST['radius']) ? $_REQUEST['radius'] : $this->default_radius,
        ));

        $this->modx->toPlaceholders(array(
            'form' => $formOutput,
        ), 'gsl');
    }

    // render stores
    public function renderStores($stores)
    {
        $storesOutput = '';

        if (count($stores) >= 1 and is_array($stores)) {
            foreach ($stores as $store) {
                // let the user see all available placeholders via [[+placeholders]] placeholder
                $store['placeholders'] = print_r($store,true);

                $storesOutput .= $this->modx->getChunk($this->tpl_store, $store);
            }
        } else {
            $storesOutput = $this->modx->getChunk($this->tpl_noresult, array());
        }

        $this->modx->toPlaceholders(array(
            'stores' => $storesOutput,
        ), 'gsl');
    }

    // get the store-data from cache
    public function getCache($id)
    {
        $options = array(
            xPDO::OPT_CACHE_KEY => 'googlestorelocator',
        );
        return  $this->modx->cacheManager->get($id . '.store.gsl', $options);
    }

    // add to cache
    public function addCache($store)
    {
        $options = array(
            xPDO::OPT_CACHE_KEY => 'googlestorelocator',
        );
        $this->modx->cacheManager->delete($store[id] . '.store.gsl', $options);
        $this->modx->cacheManager->set($store[id] . '.store.gsl', $store, 31556926, $options);

    }

    // clear cache
    public function clearCache($id)
    {
        $options = array(
            xPDO::OPT_CACHE_KEY => 'googlestorelocator',
        );
        $this->modx->cacheManager->delete($id . '.store.gsl', $options);
    }
}
