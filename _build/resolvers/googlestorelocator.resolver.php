<?php
/**
 * Resolver for googlestorelocator extra
 *
 * Copyright 2018 by Quadro - Jan Dähne <https://www.quadro-system.de>
 * Created on 07-06-2018
 *
 * googlestorelocator is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * googlestorelocator is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * googlestorelocator; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 * @package googlestorelocator
 * @subpackage build
 */

/* @var $object xPDOObject */
/* @var $modx modX */

/* @var array $options */

if ($object->xpdo) {
    $modx =& $object->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:

            $settings = array(
                'apikey_server',
                'apikey_map',
            );
            foreach ($settings as $key) {
                if (isset($options[$key])) {
                    $setting = $object->xpdo->getObject('modSystemSetting',array('key' => 'googlestorelocator.'.$key));
                    if ($setting != null) {
                        $setting->set('value',$options[$key]);
                        $setting->save();
                    } else {
                        $object->xpdo->log(xPDO::LOG_LEVEL_ERROR,'[GoogleStoreLocator] googlestorelocator.'.$key.' setting could not be found, so the setting could not be changed.');
                    }
                }
            }

            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}

return true;
