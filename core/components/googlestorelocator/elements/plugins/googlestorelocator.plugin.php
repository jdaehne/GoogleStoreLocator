<?php
/**
 * GoogleStoreLocator
 *
 * A dynamic Store Locator Snippet for MODX using the Google-Map-API
 *
 *
 * @event OnDocFormSave
 * @event OnLoadWebDocument
 * @event OnSiteRefresh
 */
 
// load Class
$corePath = $modx->getOption('googlestorelocator.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/googlestorelocator/');
$modelPath = $corePath . 'model/googlestorelocator/';
if (!class_exists('GoogleStoreLocator')) {
    require_once $modelPath . 'googlestorelocator.class.php';
}

$gsl = new GoogleStoreLocator($modx);

switch ($modx->event->name) {
     
    case 'OnDocFormSave':
        /*
         * clear cache if store/resource is updatet
         */
        $id = $resource->get('id');
        
        $gsl->clearCache($id);

        break;
        
    case 'OnLoadWebDocument':
        /*
         * set lat & lng placeholders for store details
         */
        $id = $modx->resource->get('id');

        $storeCache = $gsl->getCache($id);
        
        if (!empty($storeCache['lat']) and !empty($storeCache['lng'])) {
            $modx->toPlaceholders(array(
               'lat' => $storeCache['lat'],
               'lng' => $storeCache['lng'],
            ),'gsl');
        }

        break;
        
    case 'OnSiteRefresh':
            /*
             * clear complete cache
             */
            $modx->getCacheManager()->clean(array(
                xPDO::OPT_CACHE_KEY => 'googlestorelocator'
            ));
            $modx->log(modX::LOG_LEVEL_INFO, '[GoogleStoreLocator] Cleared cache');
            
            break;
        
}

return;