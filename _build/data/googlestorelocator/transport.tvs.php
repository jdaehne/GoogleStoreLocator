<?php
/**
 * templateVars transport file for GoogleStoreLocator extra
 *
 * Copyright 2015 by Quadro - Jan Dähne info@quadro-system.de
 * Created on 11-27-2015
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
    'allowBlank' => 'true',
    'maxLength' => '',
    'minLength' => '',
    'regex' => '',
    'regexText' => '',
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
  'name' => 'gslCountry',
  'caption' => 'Country',
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
    'allowBlank' => 'true',
    'maxLength' => '',
    'minLength' => '',
    'regex' => '',
    'regexText' => '',
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
  'name' => 'gslCity',
  'caption' => 'City',
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
    'allowBlank' => 'true',
    'maxLength' => '',
    'minLength' => '',
    'regex' => '',
    'regexText' => '',
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
  'rank' => 40,
  'display' => 'default',
  'default_text' => '',
  'properties' => 
  array (
  ),
  'input_properties' => 
  array (
    'allowBlank' => 'true',
    'maxLength' => '',
    'minLength' => '',
    'regex' => '',
    'regexText' => '',
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
  'name' => 'gslZip',
  'caption' => 'Zip',
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
    'allowBlank' => 'true',
    'maxLength' => '',
    'minLength' => '',
    'regex' => '',
    'regexText' => '',
  ),
  'output_properties' => 
  array (
  ),
), '', true, true);
return $templateVars;
