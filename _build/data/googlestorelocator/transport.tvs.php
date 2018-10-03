<?php
/**
 * templateVars transport file for googlestorelocator extra
 *
 * Copyright 2018 by Quadro - Jan Dähne <https://www.quadro-system.de>
 * Created on 10-03-2018
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
  'property_preprocess' => false,
  'type' => 'text',
  'name' => 'gslStreet',
  'caption' => 'Street',
  'description' => '',
  'elements' => '',
  'rank' => 10,
  'display' => 'default',
  'default_text' => '',
  'properties' => 
  array (
  ),
  'input_properties' => 
  array (
  ),
  'output_properties' => 
  array (
  ),
), '', true, true);
$templateVars[2] = $modx->newObject('modTemplateVar');
$templateVars[2]->fromArray(array (
  'id' => 2,
  'property_preprocess' => false,
  'type' => 'text',
  'name' => 'gslZipcode',
  'caption' => 'Zipcode',
  'description' => '',
  'elements' => '',
  'rank' => 30,
  'display' => 'default',
  'default_text' => '',
  'properties' => 
  array (
  ),
  'input_properties' => 
  array (
  ),
  'output_properties' => 
  array (
  ),
), '', true, true);
$templateVars[3] = $modx->newObject('modTemplateVar');
$templateVars[3]->fromArray(array (
  'id' => 3,
  'property_preprocess' => false,
  'type' => 'text',
  'name' => 'gslHousenumber',
  'caption' => 'Housenumber',
  'description' => '',
  'elements' => '',
  'rank' => 20,
  'display' => 'default',
  'default_text' => '',
  'properties' => 
  array (
  ),
  'input_properties' => 
  array (
  ),
  'output_properties' => 
  array (
  ),
), '', true, true);
$templateVars[4] = $modx->newObject('modTemplateVar');
$templateVars[4]->fromArray(array (
  'id' => 4,
  'property_preprocess' => false,
  'type' => 'text',
  'name' => 'gslState',
  'caption' => 'State',
  'description' => '',
  'elements' => '',
  'rank' => 50,
  'display' => 'default',
  'default_text' => '',
  'properties' => 
  array (
  ),
  'input_properties' => 
  array (
  ),
  'output_properties' => 
  array (
  ),
), '', true, true);
$templateVars[5] = $modx->newObject('modTemplateVar');
$templateVars[5]->fromArray(array (
  'id' => 5,
  'property_preprocess' => false,
  'type' => 'text',
  'name' => 'gslCity',
  'caption' => 'City',
  'description' => '',
  'elements' => '',
  'rank' => 40,
  'display' => 'default',
  'default_text' => '',
  'properties' => 
  array (
  ),
  'input_properties' => 
  array (
    'allowBlank' => 'true',
    'minLength' => '',
    'maxLength' => '',
    'regex' => '',
    'regexText' => '',
  ),
  'output_properties' => 
  array (
  ),
), '', true, true);
$templateVars[6] = $modx->newObject('modTemplateVar');
$templateVars[6]->fromArray(array (
  'id' => 6,
  'property_preprocess' => false,
  'type' => 'text',
  'name' => 'gslCountry',
  'caption' => 'Country',
  'description' => '',
  'elements' => '',
  'rank' => 60,
  'display' => 'default',
  'default_text' => '',
  'properties' => 
  array (
  ),
  'input_properties' => 
  array (
  ),
  'output_properties' => 
  array (
  ),
), '', true, true);
$templateVars[7] = $modx->newObject('modTemplateVar');
$templateVars[7]->fromArray(array (
  'id' => 7,
  'property_preprocess' => false,
  'type' => 'text',
  'name' => 'sortierer',
  'caption' => 'Sortierer',
  'description' => '',
  'elements' => '',
  'rank' => 100,
  'display' => 'default',
  'default_text' => '',
  'properties' => 
  array (
  ),
  'input_properties' => 
  array (
    'allowBlank' => 'true',
    'minLength' => '',
    'maxLength' => '',
    'regex' => '',
    'regexText' => '',
  ),
  'output_properties' => 
  array (
  ),
), '', true, true);
return $templateVars;
