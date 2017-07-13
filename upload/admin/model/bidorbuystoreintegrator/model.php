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

require_once(DIR_SYSTEM . '/../bidorbuystoreintegrator/factory.php');
require_once(dirname(__FILE__) . '/form.php');

class ModelBidorbuyStoreIntegratorModel extends Model {
    private $form;

    public function __construct() {
    }

    public function getForm($language, $categories = array(), $settings = array()) {
        //Add Uncategorized
        $categories[] = array(
            'category_id' => '0',
            'name' => 'Uncategorized',
            'parent_id' => '0',
            'sort_order' => '0'
        );

        $this->form = new BidorbuyStoreIntegratorForm($language, $categories, $settings);
        return $this->form;
    }
}