<?php
/**
 * snippets transport file for googlestorelocator extra
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
/* @var xPDOObject[] $snippets */


$snippets = array();

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array (
  'id' => 1,
  'description' => 'A dynamic Store Locator for MODX using the Google-Map-API',
  'name' => 'GoogleStoreLocator',
), '', true, true);
$snippets[1]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/googlestorelocator.snippet.php'));

return $snippets;
