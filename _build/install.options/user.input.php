<?php

/**
 * Script to interact with user during googlestorelocator package install
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
 *
 * @package googlestorelocator
 */

/**
 * Description: Script to interact with user during googlestorelocator package install
 * @package googlestorelocator
 * @subpackage build
 */

/* The return value from this script should be an HTML form (minus the
 * <form> tags and submit button) in a single string.
 *
 * The form will be shown to the user during install
 *
 * This example presents an HTML form to the user with two input fields
 * (you can have as many as you like).
 *
 * The user's entries in the form's input field(s) will be available
 * in any php resolvers with $modx->getOption('field_name', $options, 'default_value').
 *
 * You can use the value(s) to set system settings, snippet properties,
 * chunk content, etc. based on the user's preferences.
 *
 * One common use is to use a checkbox and ask the
 * user if they would like to install a resource for your
 * component (usually used only on install, not upgrade).
 */

/* This is an example. Modify it to meet your needs.
 * The user's input would be available in a resolver like this:
 *
 * $changeSiteName = (! empty($modx->getOption('change_sitename', $options, ''));
 * $siteName = $modx->getOption('sitename', $options, '').
 *
 * */

$setting = $modx->getObject('modSystemSetting',array('key' => 'googlestorelocator.apikey_server'));
if ($setting != null) { $values['apikey_server'] = $setting->get('value'); }
unset($setting);

$setting = $modx->getObject('modSystemSetting',array('key' => 'googlestorelocator.apikey_map'));
if ($setting != null) { $values['apikey_map'] = $setting->get('value'); }
unset($setting);


 $output = '<style>.field_desc { color: #A0A0A0; font-size: 11px; font-style: italic; }</style>
 <div style="padding-bottom: 1rem;">
    <label for="apikey-server">Google-Maps API-Key Server:</label>
    <input type="text" name="apikey_server" id="apikey-server" value="'.$values['apikey_server'].'" align="left" size="40" maxlength="60" />
 </div>
 <div>
     <label for="apikey-map">Google-Maps API-Key Frontend:</label>
     <input type="text" name="apikey_map" id="apikey-map" value="'.$values['apikey_map'].'" align="left" size="40" maxlength="60" />
     </div>
 <div class="field_desc">Setup API-Key: <a href="https://console.developers.google.com/" target="_blank">https://console.developers.google.com/</a></div>';



return $output;
