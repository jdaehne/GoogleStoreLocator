<?php
/**
 * snippets transport file for googlestorelocator extra
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
/* @var xPDOObject[] $snippets */


$snippets = array();

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array (
  'id' => 1,
  'property_preprocess' => false,
  'name' => 'GoogleStoreLocator',
  'description' => 'A dynamic Store Locator for MODX using the Google-Map-API',
  'properties' => 
  array (
  ),
), '', true, true);
$snippets[1]->setContent(file_get_contents(MODX_BASE_PATH . 'assets/mycomponents/googlestorelocator/core/components/googlestorelocator/elements/snippets/googlestorelocator.snippet.php'));

return $snippets;
