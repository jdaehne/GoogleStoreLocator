<?php
/**
 * systemSettings transport file for GoogleStoreLocator extra
 *
 * Copyright 2015 by Quadro - Jan Dähne info@quadro-system.de
 * Created on 12-04-2015
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
  'key' => 'googlestorelocator_googleapikey',
  'value' => '',
  'xtype' => 'textfield',
  'namespace' => 'googlestorelocator',
  'area' => 'googlestorelocator',
  'name' => 'Google API-Key',
  'description' => 'Optional Google API-Key. It is not required.',
), '', true, true);
return $systemSettings;
