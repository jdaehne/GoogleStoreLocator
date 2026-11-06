<?php
/**
 * Stores the API keys entered in the setup options as system settings.
 *
 * This resolver runs before the system setting vehicles are installed,
 * so missing settings are created here. The setting vehicles do not
 * overwrite existing settings.
 *
 * @package googlestorelocator
 * @subpackage build
 *
 * @var xPDOObject $object
 * @var array $options
 */
if (!$object->xpdo) {
    return true;
}
/** @var modX $modx */
$modx =& $object->xpdo;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        foreach (array('apikey_server', 'apikey_map') as $key) {
            if (!isset($options[$key])) {
                continue;
            }
            $settingKey = 'googlestorelocator.' . $key;
            $setting = $modx->getObject('modSystemSetting', array('key' => $settingKey));
            if (!$setting) {
                $setting = $modx->newObject('modSystemSetting');
                $setting->fromArray(array(
                    'key' => $settingKey,
                    'xtype' => 'textfield',
                    'namespace' => 'googlestorelocator',
                    'area' => 'API',
                ), '', true, true);
            }
            $setting->set('value', trim($options[$key]));
            if (!$setting->save()) {
                $modx->log(xPDO::LOG_LEVEL_ERROR, '[GoogleStoreLocator] Could not save setting ' . $settingKey);
            }
        }
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        break;
}
return true;
