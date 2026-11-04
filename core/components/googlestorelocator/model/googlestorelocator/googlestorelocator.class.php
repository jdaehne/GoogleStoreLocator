<?php

class GoogleStoreLocator {

    /** @var modX */
    public $modx;

    public $apikeyServer;
    public $apikeyMap;
    public $parents;
    public $tvname_lat;
    public $tvname_lng;
    public $tvname_street;
    public $tvname_housenumber;
    public $tvname_zipcode;
    public $tvname_city;
    public $tvname_state;
    public $tvname_country;
    public $include_tvs;
    public $tv_prefix;
    public $unit;
    public $tpl_map;
    public $tpl_map_marker;
    public $tpl_map_marker_content;
    public $tpl_form;
    public $tpl_store;
    public $tpl_noresult;
    public $tpl_message;
    public $marker_image;
    public $marker_image_location;
    public $zoom;
    public $zoom_search;
    public $zoom_position;
    public $lat_center;
    public $lng_center;
    public $map_css;
    public $map_style;
    public $auto_zoom_center;
    public $default_radius;
    public $location;
    public $location_radius;
    public $region;


    /**
     * GoogleStoreLocator constructor
     *
     * @param modX $modx A reference to the MODX instance.
     * @param array $config An array of options. Optional (e.g. the plugin only needs the cache methods).
     */
    public function __construct(modX $modx, array $config = array())
    {
        // init modx
        $this->modx = $modx;

        // config
        $this->apikeyServer = $this->modx->getOption('googlestorelocator.apikey_server');
        $this->apikeyMap = $this->modx->getOption('googlestorelocator.apikey_map');
        $this->parents = $config['parents'] ?? array();
        $this->tvname_lat = $config['tvname_lat'] ?? 'gslLat';
        $this->tvname_lng = $config['tvname_lng'] ?? 'gslLng';
        $this->tvname_street = $config['tvname_street'] ?? 'gslStreet';
        $this->tvname_housenumber = $config['tvname_housenumber'] ?? 'gslHousenumber';
        $this->tvname_zipcode = $config['tvname_zipcode'] ?? 'gslZipcode';
        $this->tvname_city = $config['tvname_city'] ?? 'gslCity';
        $this->tvname_state = $config['tvname_state'] ?? 'gslState';
        $this->tvname_country = $config['tvname_country'] ?? 'gslCountry';
        $this->include_tvs = $config['include_tvs'] ?? false;
        $this->tv_prefix = $config['tv_prefix'] ?? 'tv.';
        $this->unit = $config['unit'] ?? 'K';
        $this->tpl_map = $config['tpl_map'] ?? 'gslMapTpl';
        $this->tpl_map_marker = $config['tpl_map_marker'] ?? 'gslMapMarkerTpl';
        $this->tpl_map_marker_content = $config['tpl_map_marker_content'] ?? 'gslMapMarkerContentTpl';
        $this->tpl_form = $config['tpl_form'] ?? 'gslFormTpl';
        $this->tpl_store = $config['tpl_store'] ?? 'gslStoreTpl';
        $this->tpl_noresult = $config['tpl_noresult'] ?? 'gslNoResultTpl';
        $this->tpl_message = $config['tpl_message'] ?? 'gslMessageTpl';
        $this->marker_image = $config['marker_image'] ?? '';
        $this->marker_image_location = $config['marker_image_location'] ?? 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png';
        $this->zoom = $config['zoom'] ?? 8;
        $this->zoom_search = $config['zoom_search'] ?? 11;
        $this->zoom_position = $config['zoom_position'] ?? 11;
        $this->lat_center = $config['lat_center'] ?? '49.14721';
        $this->lng_center = $config['lng_center'] ?? '8.2202';
        $this->map_css = $config['map_css'] ?? 'height: 400px; margin: 30px 0;';
        $this->map_style = $config['map_style'] ?? '';
        $this->auto_zoom_center = $config['auto_zoom_center'] ?? '';
        $this->default_radius = $config['default_radius'] ?? 20;
        $this->location = $config['location'] ?? '';
        $this->location_radius = $config['location_radius'] ?? 20;
        $this->region = $config['region'] ?? '';
    }


    // order/sort stores (name, menuindex, distance, ...)
    public function sortBy($stores, $sortby = 'menuindex', $direction = 'asc')
    {
        if (!is_array($stores)) return false;

        usort($stores, function($a, $b) use ($sortby) {
            $a = $a[$sortby] ?? '';
            $b = $b[$sortby] ?? '';
            if ($sortby === 'distance') {
                $a = floatval(str_replace(',', '.', (string) $a));
                $b = floatval(str_replace(',', '.', (string) $b));
                return $a <=> $b;
            }
            return strcmp((string) $a, (string) $b);
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
            $storeCache = $this->getCache($item->get('id'));
            if (!empty($storeCache)) {
                $stores[] = $storeCache;
                continue;
            }

            $fields = $item->toArray();

            $store = array(
                'id' => $item->get('id'),
                'name' => $item->get('pagetitle'),
                'lat' => $item->getTVValue($this->tvname_lat),
                'lng' => $item->getTVValue($this->tvname_lng),
                'street' => $item->getTVValue($this->tvname_street),
                'housenumber' => $item->getTVValue($this->tvname_housenumber),
                'zipcode' => $item->getTVValue($this->tvname_zipcode),
                'city' => $item->getTVValue($this->tvname_city),
                'state' => $item->getTVValue($this->tvname_state),
                'country' => $item->getTVValue($this->tvname_country),
                'menuindex' => $item->get('menuindex'),
            );

            $store = array_merge($fields, $store);

            // include tvs
            if (!empty($this->include_tvs)){
                $tvs = array_map('trim', explode(',', $this->include_tvs));
                foreach ($tvs as $tv){
                    $store[$this->tv_prefix . $tv] = $item->getTVValue($tv);
                }
            }

            // add Latitude and Longitude if empty
            if (empty($store['lat']) || empty($store['lng'])) {
                $address = $this->formatAddress($store['street'], $store['housenumber'], $store['zipcode'], $store['city'], $store['state'], $store['country']);
                $latlng = $this->getLngLat($address);
                $store = array_merge($store, $latlng);
            }

            if (!empty($store['lat']) && !empty($store['lng'])) {
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
            if (!empty($store['lat']) && !empty($store['lng'])) {
                $distance = $this->distance($lat, $lng, $store['lat'], $store['lng'], $unit);
            }
            $stores[$key]['distance'] = $distance;
        }

        return $stores;
    }



    // calculates distance between two points
    public function distance($lat1, $lng1, $lat2, $lng2, $unit = 'K')
    {
        $lat1 = (float) $lat1;
        $lng1 = (float) $lng1;
        $lat2 = (float) $lat2;
        $lng2 = (float) $lng2;

        $theta = $lng1 - $lng2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        // rounding errors can push the value slightly above 1, which makes acos() return NAN
        $dist = acos(max(-1.0, min(1.0, $dist)));
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper((string) $unit);

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
        $data = array(
            'lat' => '',
            'lng' => '',
            'address' => '',
        );

        $region = $this->region ? '&region=' . urlencode($this->region) : '';
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode((string) $address) . '&key=' . urlencode((string) $this->apikeyServer) . $region;
        $geojson = @file_get_contents($url);
        $geodata = $geojson !== false ? json_decode($geojson) : null;

        if (!is_object($geodata) || ($geodata->status ?? '') !== 'OK' || empty($geodata->results[0])) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, '[GoogleStoreLocator] Could not get Geo-Data of Address: ' . $address . ' Errormessage: ' . (is_object($geodata) ? ($geodata->error_message ?? $geodata->status ?? '') : 'No response from Google'));
            return $data;
        }

        $result = $geodata->results[0];
        $data['lat'] = str_replace(',', '.', (string) $result->geometry->location->lat);
        $data['lng'] = str_replace(',', '.', (string) $result->geometry->location->lng);
        $data['address'] = $result->formatted_address ?? '';

        return $data;
    }

    // set location by user input or property
    public function setLocation($stores)
    {
        $address = !empty($_REQUEST['location']) ? (string) $_REQUEST['location'] : (string) $this->location;
        $radius = (int) (isset($_REQUEST['radius']) ? $_REQUEST['radius'] : $this->location_radius);

        $locationData = $this->getLngLat($address);

        if (!empty($locationData['lat']) && !empty($locationData['lng'])) {
            if (is_array($stores) && count($stores) >= 1) {
                // add distance
                $stores = $this->addDistanceToStores($stores, $locationData['lat'], $locationData['lng'], $this->unit);

                // filter stores by radius
                $stores = $this->filterStores($stores, 'distance', $radius);

                // sort stores
                if (!empty($_REQUEST['location'])) {
                    $stores = $this->sortBy($stores, 'distance');
                }
            }

            $this->lat_center = $locationData['lat'];
            $this->lng_center = $locationData['lng'];
        }else {
            $stores = array();
        }

        if (!empty($_REQUEST['location'])) {
            $message = $this->modx->getChunk($this->tpl_message, array(
                'address' => htmlspecialchars($locationData['address'], ENT_QUOTES, 'UTF-8'),
                'radius' => $radius,
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

        if (is_array($stores) && count($stores) >= 1) {
            foreach ($stores as $store) {
                if (!empty($store['lat']) && !empty($store['lng'])) {

                    // Replacing/escaping single quotes for the js-map
                    $jsStorePlaceholder = array();
                    foreach ($store as $key => $value) {
                        $jsStorePlaceholder[$key] = is_scalar($value) ? str_replace("'", "\\'", (string) $value) : $value;
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
        	'marker' => preg_replace( "/\r|\n/", "", (string) $mapMarkerOutput),
        	'zoom' => $this->zoom,
        	'latCenter' => $this->lat_center,
        	'longCenter' => $this->lng_center,
        	'mapCSS' => $this->map_css,
        	'apiKey' => $this->apikeyMap,
        	'mapStyle' => $this->map_style,
        	'showLocation' => isset($_REQUEST['location']) ? true : false,
        	'markerImageLocation' => $this->marker_image_location,
        	'autoZoomCenter' => (is_array($stores) && count($stores) >= 1) ? $this->auto_zoom_center : '',
        ));

        $this->modx->toPlaceholders(array(
            'map' => $mapOutput,
        ), 'gsl');
    }

    // filter stores (storesArray, placeholder/variable name, value, operator)
    public function filterStores($stores, $key, $value, $operator = '<=')
    {
        if (!is_array($stores)) return false;

        $stores = array_filter($stores, function ($var) use ($key, $value, $operator) {
            $field = $var[$key] ?? null;
            switch ($operator) {
                case 'contains':
                    return strpos((string) $field, (string) $value) !== false;
                case '==':
                case '=':
                    return ($field == $value);
                case '>':
                    return ($field > $value);
                case '>=':
                    return ($field >= $value);
                case '<':
                    return ($field < $value);
                case '<=':
                default:
                    return ($field <= $value);
            }
        });

        return $stores;
    }

    // render form
    public function renderForm()
    {
        $formOutput = $this->modx->getChunk($this->tpl_form, array(
        	'location' => htmlspecialchars((string) ($_REQUEST['location'] ?? ''), ENT_QUOTES, 'UTF-8'),
        	'radius' => (int) (isset($_REQUEST['radius']) ? $_REQUEST['radius'] : $this->default_radius),
        ));

        $this->modx->toPlaceholders(array(
            'form' => $formOutput,
        ), 'gsl');
    }

    // render stores
    public function renderStores($stores)
    {
        $storesOutput = '';

        if (is_array($stores) && count($stores) >= 1) {
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
        $this->modx->cacheManager->delete($store['id'] . '.store.gsl', $options);
        $this->modx->cacheManager->set($store['id'] . '.store.gsl', $store, 31556926, $options);

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
