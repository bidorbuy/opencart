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


class ControllerFeedBidorbuyStoreIntegrator extends Controller {
    private $error = array();
    private $settings;

    public function __construct($registry) {
        parent::__construct($registry);
        $bobsi = new BobsiInit();
        $bobsi->init($this->config);
    }

    public function index() {
        $this->load->language(EXTENSION_PATH . 'feed/bidorbuystoreintegrator');
        $this->load->model('bidorbuystoreintegrator/model');
        $this->load->model('setting/setting');
        $this->load->model('catalog/category');

        if (bidorbuystoreintegrator_is_upgrade_required($this->model_setting_setting)) {
            bidorbuystoreintegrator_upgrade_202($this->model_setting_setting);
        }

        $this->settings = $this->model_setting_setting->getSetting(bobsi\Version::$id);
        if (isset($this->settings[BIDORBUY_SETTINGS_NAME])) {
            bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->unserialize($this->settings[BIDORBUY_SETTINGS_NAME], true);
        }
        $isHttps = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->isHTTPS();

        // Reset Token action
        if (isset($this->request->post[bobsi\Settings::nameActionReset]) && $this->validate()) {

            bobsi\StaticHolder::getBidorbuyStoreIntegrator()->processAction(bobsi\Settings::nameActionReset);
            $settingsGroup = array(BIDORBUY_SETTINGS_NAME => bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->serialize(true));
            $this->saveSettings($settingsGroup);
            
            $this->session->data['bobsi-success'][] = $this->language->get('text_reset_tokens');

            // Version >= 2.0
            if (version_compare(VERSION, '2.0') >= 0) {
                $this->response->redirect($this->url->link(EXTENSION_PATH . 'feed/bidorbuystoreintegrator', 'token=' . $this->session->data['token'], $isHttps));
            } else {
                $this->redirect($this->url->link('feed/bidorbuystoreintegrator', 'token=' . $this->session->data['token'], $isHttps ? 'SSL' : 'NONSSL'));
            }
        }

        // Log files actions: Download, Remove
        if (isset($this->request->post[bobsi\Settings::nameLoggingFormAction]) && $this->validate()) {
            $data = array(
                bobsi\Settings::nameLoggingFormFilename =>
                    (isset($this->request->post[bobsi\Settings::nameLoggingFormFilename]))
                        ? $this->request->post[bobsi\Settings::nameLoggingFormFilename]
                        : '');
            $result = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->processAction($this->request->post[bobsi\Settings::nameLoggingFormAction], $data);
            foreach ($result as $item) {
                $this->session->data['bobsi-success'][] = $item;
            }

            // Version >= 2.0
            if (version_compare(VERSION, '2.0') >= 0) {
                $this->response->redirect($this->url->link(EXTENSION_PATH . 'feed/bidorbuystoreintegrator', 'token=' . $this->session->data['token'], $isHttps));
            } else {
                $this->redirect($this->url->link('feed/bidorbuystoreintegrator', 'token=' . $this->session->data['token'], $isHttps ? 'SSL' : 'NONSSL'));
            }
        }

        //Save the settings if the user has submitted the admin form (ie if someone has pressed save).
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate() && empty($this->request->post[bobsi\Settings::nameLoggingFormAction])) {
            bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->unserialize(serialize($this->request->post));
            $data = $this->request->post;
            $wordings = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getDefaultWordings();
            $presaved_settings = array();
            $prevent_saving = false;

            $settings_checklist = array(
                bobsi\Settings::nameUsername => 'strval',
                bobsi\Settings::namePassword => 'strval',
                bobsi\Settings::nameFilename => 'strval',
                bobsi\Settings::nameCompressLibrary => 'strval',
                bobsi\Settings::nameDefaultStockQuantity => 'intval',
                bobsi\Settings::nameEmailNotificationAddresses => 'strval',
                bobsi\Settings::nameEnableEmailNotifications => 'bool',
                bobsi\Settings::nameLoggingLevel => 'strval',
                bobsi\Settings::nameExportQuantityMoreThan => 'intval',
                bobsi\Settings::nameExcludeCategories => 'categories',
            );

            foreach ($settings_checklist as $setting => $prevalidation) {
                switch ($prevalidation) {
                case ('strval'):
                    $presaved_settings[$setting] = isset($data[$setting]) ? 
                        strval($data[$setting]) : '';
                    break;
                case ('intval'):
                    $presaved_settings[$setting] = isset($data[$setting]) ?
                        $data[$setting] : 0;
                    break;
                case ('bool'):
                    $presaved_settings[$setting] = isset($data[$setting]) ?
                        (bool)($data[$setting]) : false;
                    break;
                case ('categories'):
                    $presaved_settings[$setting] = isset($data[$setting]) ?
                        (array)$data[$setting] : array();
                }

                if (!call_user_func($wordings[$setting][bobsi\Settings::nameWordingsValidator], $presaved_settings[$setting])) {
                    $field = $wordings[$setting]['title'];
                    
                    $this->session->data['bobsi-warning'][] = (
                        "Invalid value: 
                        \"$presaved_settings[$setting]\"in the field: $field"
                    );
                    
                    $prevent_saving = true;
                }
            }

            if (!$prevent_saving) {
                //Saving tokens
                $presaved_settings[bobsi\Settings::nameTokenExport] =
                    bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()
                        ->getTokenExport();

                $presaved_settings[bobsi\Settings::nameTokenDownload] =
                    bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()
                        ->getTokenExport();
                bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()
                    ->unserialize(serialize($presaved_settings));

                $newSettings = bobsi\StaticHolder::getBidorbuyStoreIntegrator()
                    ->getSettings()->serialize(true);

                $settingsGroup = array(BIDORBUY_SETTINGS_NAME => $newSettings);

                $this->saveSettings($settingsGroup);
            }

            // Version >= 2.0
            if (version_compare(VERSION, '2.0') >= 0) {
                $this->response->redirect($this->url->link(EXTENSION_PATH . 'feed/bidorbuystoreintegrator', 'token=' . $this->session->data['token'], $isHttps));
            } else {
                $this->redirect($this->url->link('feed/bidorbuystoreintegrator', 'token=' . $this->session->data['token'], $isHttps ? 'SSL' : 'NONSSL'));
            }
        }

        // Set the title from the language file $_['heading_title'] string
        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->addScript('../bidorbuystoreintegrator/vendor/com.extremeidea.bidorbuy/storeintegrator-core/assets/js/admin.js');
        $this->document->addScript('view/javascript/bidorbuystoreintegrator/admin_aux.js');
        $this->document->addStyle('view/stylesheet/bidorbuystoreintegrator/bidorbuystoreintegrator.css');

        $data = $this->getTemplateVariables();

        // Version >= 2.0
        if (version_compare(VERSION, '2.0') >= 0) {
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view(EXTENSION_PATH . 'feed/bidorbuystoreintegrator_v2.tpl', $data));
        } else {
            $this->data = $data;

            $this->template = 'feed/bidorbuystoreintegrator.tpl';
            $this->children = array(
                'common/header',
                'common/footer',
            );

            $this->response->setOutput($this->render());
        }
    }

    /*
      * This function is called to ensure that the settings chosen by the admin user are allowed/valid.
      * You can add checks in here of your own.
      */
    protected function validate() {
        if (!$this->user->hasPermission('modify', EXTENSION_PATH . 'feed/bidorbuystoreintegrator')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error ? true : false;
    }

    public function install() {
        $this->load->model('setting/setting');

        $settingsGroup = array();
        $settingsGroup[BIDORBUY_SETTINGS_NAME] = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->serialize(true);

        $this->saveSettings($settingsGroup);
    }

    public function uninstall() {
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting(bobsi\Version::$id);
    }

    protected function getTemplateVariables() {
        $data = array();

        // Language
        $text_strings = array(
            'heading_title',
            'export_configs_title',
            'button_save',
            'button_cancel',
            'button_export',
            'button_download',
            'button_reset_tokens',
            'export_criteria_title',
            'export_criteria_includedcats_title',
            'export_criteria_excludedcats_title',
            'export_links_title',
            'export_url',
            'download_url',
            'link_tooltip',
            'reset_tokens',
            'launch',
            'copy',
            'entry_status',
            'text_disabled',
            'text_enabled'
        );

        foreach ($text_strings as $text) {
            $data[$text] = $this->language->get($text);
        }

        $data['success'] = array();
        
        $data['warning'] = array_merge(
            bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getWarnings(),
            bobsi\StaticHolder::getWarnings()->getBusinessWarnings()
        );

        if (isset($this->session->data['bobsi-success'])) {
            $data['success'] = $this->session->data['bobsi-success'];
            unset($this->session->data['bobsi-success']);
        }

        if (isset($this->session->data['bobsi-warning'])) {
            $data['warning'] = array_merge($data['warning'], $this->session->data['bobsi-warning']);
            unset($this->session->data['bobsi-warning']);
        }

        if (isset($this->error['warning'])) {
            $data['warning'][] = $this->error['warning'];
        }

        //Getting categories
        $categories_params = array(
            'start' => 0,
            'limit' => $this->model_catalog_category->getTotalCategories());

        $categories = $this->model_catalog_category->getCategories($categories_params);
		$data['cancel'] = $this->url->link('extension/feed', 'token=' . $this->session->data['token'], true);
        $data['token_download'] = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getTokenDownload();
        $data['token_export'] = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getTokenExport();
        $data['download_link'] = HTTP_CATALOG . 'index.php?route='. EXTENSION_PATH . 'feed/bidorbuystoreintegrator/download&t=' . $data['token_download'];
        $data['export_link'] = HTTP_CATALOG . 'index.php?route='. EXTENSION_PATH .'feed/bidorbuystoreintegrator/export&t=' . $data['token_export'];
        $data['phpInfo_link'] = HTTP_CATALOG . 'index.php?route='. EXTENSION_PATH . 'feed/bidorbuystoreintegrator/version&t=' . $data['token_download'] . '&phpinfo=y';
        $data['logsHtml'] = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getLogsHtml();

        if (isset($this->request->post['bobsi_status'])) {
            $data['bobsi_status'] = $this->request->post['bobsi_status'];
        } else {
            $data['bobsi_status'] = $this->config->get($this->statusName);
        }

        $form = $this->model_bidorbuystoreintegrator_model->getForm($this->language, $categories, $this->settings);
        //Create form fields with titles and descriptions using the specified language.
        $data['exporConfigstFieldSet'] = $form->getFieldset('EXPORT_CONFIGS');
        $data['exportCriteriaFieldSet'] = $form->getFieldset('EXPORT_CRITERIA');

        //SET UP BREADCRUMB TRAIL. YOU WILL NOT NEED TO MODIFY THIS UNLESS YOU CHANGE YOUR MODULE NAME.
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_feed'),
            'href' => $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link(EXTENSION_PATH . 'feed/bidorbuystoreintegrator', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link(EXTENSION_PATH . 'feed/bidorbuystoreintegrator', 'token=' . $this->session->data['token'], 'SSL');
        $data['reset'] = $this->url->link(EXTENSION_PATH . 'feed/bidorbuystoreintegrator', 'token=' . $this->session->data['token'], 'SSL');

        $data['baa'] = $this->showBAA();
        $formdata = (array)bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings();
        $data['formdata'] = array_shift($formdata);

        return $data;
    }

    protected function saveSettings($settingsGroup = array()) {
        $settingsGroup[bobsi\Version::$id . '_status'] = 1;
        $this->model_setting_setting->editSetting(bobsi\Version::$id, $settingsGroup);
    }

    protected function showBAA() {
        return isset($this->request->get['baa']) && $this->request->get['baa'] == 1;
    }

}

class ControllerExtensionFeedBidorbuyStoreIntegrator extends ControllerFeedBidorbuyStoreIntegrator {

}