<?php

/**
 * Copyright (c) 2014, 2015, 2016 Bidorbuy http://www.bidorbuy.co.za
 * This software is the proprietary information of Bidorbuy.
 *
 * All Rights Reserved.
 * Modification, redistribution and use in source and binary forms, with or without modification
 * are not permitted without prior written approval by the copyright holder.
 *
 * Vendor: EXTREME IDEA LLC http://www.extreme-idea.com
 */

use com\extremeidea\bidorbuy\storeintegrator\core as bobsi;

require_once(DIR_SYSTEM . '/../bidorbuystoreintegrator/factory.php');

// Heading Goes here:
$_['heading_title'] = bobsi\Version::$name;

// Extension controls
$_['button_save'] = 'Save';
$_['button_export'] = 'Export';
$_['button_download'] = 'Download';
$_['button_reset_tokens'] = 'Reset tokens';

// Export Configurations
$_['export_configs_title'] = 'Export Configurations';
$_['export_configs_desc'] = '';

$_[bobsi\Settings::nameUsername]['title'] = 'Username';
$_[bobsi\Settings::nameUsername]['desc'] = 'Please specify the username if your platform is protected by ' .
    '<a href=\'http://en.wikipedia.org/wiki/Basic_access_authentication\' ' .
    'target=\'_blank\'>Basic Access Authentication</a>';

$_[bobsi\Settings::namePassword]['title'] = 'Password';
$_[bobsi\Settings::namePassword]['desc'] = 'Please specify the password if your platform is protected by ' .
    '<a href=\'http://en.wikipedia.org/wiki/Basic_access_authentication\' ' .
    'target=\'_blank\'>Basic Access Authentication</a>';

$_[bobsi\Settings::nameFilename]['title'] = 'Export Filename';
$_[bobsi\Settings::nameFilename]['desc'] = '16 characters max. Must start with a letter.<br>' .
    'Can contain letters, digits, `-` and `_`';

$_[bobsi\Settings::nameCompressLibrary]['title'] = 'Compress Tradefeed XML';
$_[bobsi\Settings::nameCompressLibrary]['desc'] = 'Choose a Compress Library to compress destination Tradefeed XML';

$_[bobsi\Settings::nameDefaultStockQuantity]['title'] = 'Min quantity in stock';
$_[bobsi\Settings::nameDefaultStockQuantity]['desc'] = 'Set minimum quantity if quantity management is turned OFF';

$_[bobsi\Settings::nameEmailNotificationAddresses]['title'] = 'Send logs to email addresses';
$_[bobsi\Settings::nameEmailNotificationAddresses]['desc'] = 'Specify email address(es) separated by comma to send ' .
    'the log entries to';

$_[bobsi\Settings::nameEnableEmailNotifications]['title'] = 'Turn on/off email notifications';
$_[bobsi\Settings::nameEnableEmailNotifications]['desc'] = '';

$_[bobsi\Settings::nameLoggingLevel]['title'] = 'Logging Level';
$_[bobsi\Settings::nameLoggingLevel]['desc'] = 'A level describes the severity of a logging message. There are six ' .
    'levels, show here in descending order of severity';

// Export Criteria
$_['export_criteria_title'] = 'Export Criteria';
$_['export_criteria_desc'] = '';

$_[bobsi\Settings::nameExportQuantityMoreThan]['title'] = 'Export products with available quantity more than';
$_[bobsi\Settings::nameExportQuantityMoreThan]['desc'] = 'Products with stock quantities lower than this value will ' .
    'be excluded from the XML feed';

$_['export_criteria_activeonly_title'] = 'Export only published products';
$_['export_criteria_activeonly_desc'] = '';

$_['export_criteria_includedcats_title'] = 'Included Categories';
$_['export_criteria_excludedcats_desc'] = '';
$_['export_criteria_excludedcats_title'] = 'Excluded Categories';
$_['export_criteria_excludedcats_desc'] = '';

// Links
$_['export_links_title'] = 'Export Links';
$_['export_url'] = 'Export';
$_['download_url'] = 'Download';
$_['link_tooltip'] = 'Click to select';
$_['launch'] = 'Launch';
$_['copy'] = 'Copy';
$_['reset_tokens'] = 'Reset tokens';

// Other
$_['text_feed'] = 'Product Feeds';
$_['text_success'] = 'Success: You have modified bidorbuy Store Integrator feed!';
$_['text_reset_tokens'] = 'Success: The tokens updated.';
$_['entry_status'] = 'Status';
$_['text_disabled'] = 'Disabled';
$_['text_enabled'] = 'Enabled';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify bidorbuy Store Integrator!';
$_['error_filename'] = 'Warning: Incorrect Export Filename. <br> 16 characters max. Must start with a letter. ' .
    'Can contain letters, digits, `-` and `_`';
