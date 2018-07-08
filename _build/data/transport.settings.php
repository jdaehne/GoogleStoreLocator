<?php
/**
 * systemSettings transport file for googlestorelocator extra
 *
 * Copyright 2018 by Quadro - Jan Dähne <https://www.quadro-system.de>
 * Created on 07-08-2018
 *
 * @package googlestorelocator
 * @subpackage build
 */

if (! function_exists('stripPhpTags')) {
    function stripPhpTags($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<' . '?' . 'php', '', $o);
        $o = str_replace('?>', '', $o);
        $o = trim($o);
        return $o;
    }
}
/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $systemSettings */


$systemSettings = array();

$systemSettings[1] = $modx->newObject('modSystemSetting');
$systemSettings[1]->fromArray(array (
  'key' => 'googlestorelocator.apikey_map',
  'name' => 'API-Key Frontent',
  'description' => 'This API-Key is used for displaying the map in the fronend.',
  'namespace' => 'googlestorelocator',
  'xtype' => 'textfield',
  'value' => '',
  'area' => 'API',
), '', true, true);
$systemSettings[2] = $modx->newObject('modSystemSetting');
$systemSettings[2]->fromArray(array (
  'key' => 'googlestorelocator.apikey_server',
  'name' => 'API-Key Server',
  'description' => 'This API-Key is used for fetching the data from the Google-API-Server.',
  'namespace' => 'googlestorelocator',
  'xtype' => 'textfield',
  'value' => '',
  'area' => 'API',
), '', true, true);
return $systemSettings;
