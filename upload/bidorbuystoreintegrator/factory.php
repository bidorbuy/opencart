<?php

/**
 * Copyright (c) 2014, 2015, 2016 Bidorbuy http://www.bidorbuy.co.za
 * This software is the proprietary information of Bidorbuy.
 *
 * All Rights Reserved.
 * Modification, redistribution and use in source and binary forms, with or without
 * modification are not permitted without prior written approval by the copyright
 * holder.
 *
 * Vendor: EXTREME IDEA LLC http://www.extreme-idea.com
 */

use com\extremeidea\bidorbuy\storeintegrator\core as bobsi;

require_once(dirname(__FILE__) . '/vendor/autoload.php');
define('BIDORBUY_SETTINGS_NAME', bobsi\Version::$id . '_' . bobsi\Settings::name);

version_compare(VERSION, '2.3') >= 0 ? define('EXTENSION_PATH', 'extension/') : define('EXTENSION_PATH', '');

/**
 * Class BobsiInit.
 */
class BobsiInit {

    /**
     * Init.
     *
     * @param object $config config
     *
     * @return void
     */
    public function init($config) {
        $bobsiConfig = !is_null($config->get(BIDORBUY_SETTINGS_NAME)) ?
            $config->get(BIDORBUY_SETTINGS_NAME) : $config->get(bobsi\Settings::name);

        bobsi\StaticHolder::getBidorbuyStoreIntegrator()->init(
            $config->get("config_name"),
            $config->get("config_email"),
            'OpenCart ' . VERSION,
            $bobsiConfig
        );
    }
}

/**
 * Update bobsi
 *
 * @param mixed $settingsModel settings model
 *
 * @return bool
 */
function bidorbuystoreintegrator_upgrade_202($settingsModel = NULL) {

    $oldSettings = $settingsModel->getSetting(bobsi\Settings::name);
    $statusName = bobsi\Version::$id . '_status';

    $settingsGroup[$statusName] = isset($oldSettings[$statusName]) ? $oldSettings[$statusName] : '';
    $settingsGroup[BIDORBUY_SETTINGS_NAME] = $oldSettings[bobsi\Settings::name];

    if (property_exists($settingsModel, 'editSetting') || method_exists($settingsModel, 'editSetting')) {
        $settingsModel->editSetting(bobsi\Version::$id, $settingsGroup);
        $settingsModel->deleteSetting(bobsi\Settings::name);
    }

    return TRUE;
}

/**
 * Check update.
 * 
 * @param mixed $settingsModel settings model
 *
 * @return bool
 */
function bidorbuystoreintegrator_is_upgrade_required($settingsModel = NULL) {

    $oldSettings = $settingsModel->getSetting(bobsi\Settings::name);
    $newSettings = $settingsModel->getSetting(bobsi\Version::$id);

    if (is_array($oldSettings)
        && !empty($oldSettings)
        && isset($oldSettings[bobsi\Settings::name])
        && empty($newSettings)
    ) {
        return TRUE;
    }

    return FALSE;
}
