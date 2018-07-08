<?php
/**
 * templateVars transport file for googlestorelocator extra
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
/* @var xPDOObject[] $templateVars */


$templateVars = array();

$templateVars[1] = $modx->newObject('modTemplateVar');
$templateVars[1]->fromArray(array (
  'id' => 1,
  'description' => '',
  'caption' => 'City',
  'default_text' => '',
  'name' => 'gslCity',
), '', true, true);
$templateVars[2] = $modx->newObject('modTemplateVar');
$templateVars[2]->fromArray(array (
  'id' => 2,
  'description' => '',
  'caption' => 'Country',
  'default_text' => '',
  'name' => 'gslCountry',
), '', true, true);
$templateVars[3] = $modx->newObject('modTemplateVar');
$templateVars[3]->fromArray(array (
  'id' => 3,
  'description' => '',
  'caption' => 'Housenumber',
  'default_text' => '',
  'name' => 'gslHousenumber',
), '', true, true);
$templateVars[4] = $modx->newObject('modTemplateVar');
$templateVars[4]->fromArray(array (
  'id' => 4,
  'description' => '',
  'caption' => 'State',
  'default_text' => '',
  'name' => 'gslState',
), '', true, true);
$templateVars[5] = $modx->newObject('modTemplateVar');
$templateVars[5]->fromArray(array (
  'id' => 5,
  'description' => '',
  'caption' => 'Street',
  'default_text' => '',
  'name' => 'gslStreet',
), '', true, true);
$templateVars[6] = $modx->newObject('modTemplateVar');
$templateVars[6]->fromArray(array (
  'id' => 6,
  'description' => '',
  'caption' => 'Zipcode',
  'default_text' => '',
  'name' => 'gslZipcode',
), '', true, true);
return $templateVars;
