<?php
/**
 * Setup options for GoogleStoreLocator
 *
 * Asks for the Google API keys during install/upgrade. The values are
 * stored by _build/resolvers/setupoptions.resolver.php.
 *
 * @package googlestorelocator
 * @subpackage build
 *
 * @var modX $modx
 * @var array $options
 */
$values = array(
    'apikey_server' => '',
    'apikey_map' => '',
);
foreach (array_keys($values) as $key) {
    $setting = $modx->getObject('modSystemSetting', array('key' => 'googlestorelocator.' . $key));
    if ($setting) {
        $values[$key] = htmlspecialchars($setting->get('value'), ENT_QUOTES, 'UTF-8');
    }
}

return '<style>.field_desc { color: #A0A0A0; font-size: 11px; font-style: italic; }</style>
<div style="padding-bottom: 1rem;">
    <label for="apikey-server">Google-Maps API-Key Server:</label>
    <input type="text" name="apikey_server" id="apikey-server" value="' . $values['apikey_server'] . '" size="40" maxlength="60" />
</div>
<div>
    <label for="apikey-map">Google-Maps API-Key Frontend:</label>
    <input type="text" name="apikey_map" id="apikey-map" value="' . $values['apikey_map'] . '" size="40" maxlength="60" />
</div>
<div class="field_desc">Setup API-Key: <a href="https://console.developers.google.com/" target="_blank">https://console.developers.google.com/</a></div>';
