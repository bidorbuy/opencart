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

class BidorbuyStoreIntegratorForm {
    public $formData = array();
    var $wordings = array();

    private $language = null;
    private $categories = array();

    private $formField = null;

    public function __construct($language, $categories = array(), $settings = array()) {
        $this->categories = $categories;
        $this->language = $language;
        $this->formField = new BidorbuyStoreIntegratorField();

        if (isset($settings[BIDORBUY_SETTINGS_NAME])) {
            bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->unserialize($settings[BIDORBUY_SETTINGS_NAME], true);
        }

        $this->formData = $this->loadFormData();
    }

    /*************************************************************************************/
    public function getFieldset($string = '') {
        $fieldSet = array();

        switch ($string) {
            case 'EXPORT_CONFIGS':
                $fieldSet = array('UserName', 'Password', 'FileName', 'CompressLibrary',
                    'MinQuantity', 'Email', 'CheckboxNotification', 'LoggingLevel');
                break;
            case 'EXPORT_CRITERIA':
                $fieldSet = array('ExportQuantityMoreThan', 'Categories');
                break;
            case '':
                break;
        }

        $fields = array();
        foreach ($fieldSet as $field) {
            $fields[] = call_user_func(array($this, 'get' . $field . 'Field'));
        }

        return $fields;
    }

    /***************************************************************************************/

    public function getUserNameField() {
        $name = bobsi\Settings::nameUsername;
        $text = $this->language->get($name);
        $desc = $text['desc'];
        return $this->formField->getField($this->createInputArray($name), $this->createLabelArray($name), $desc);
    }

    public function getPasswordField() {
        $name = bobsi\Settings::namePassword;
        $text = $this->language->get($name);
        $desc = $text['desc'];
        return $this->formField->getField($this->createInputArray($name, 'password'), $this->createLabelArray($name), $desc);
    }

    public function getFileNameField() {
        $name = bobsi\Settings::nameFilename;
        $text = $this->language->get($name);
        $desc = $text['desc'];
        return $this->formField->getField($this->createInputArray($name), $this->createLabelArray($name), $desc);
    }

    public function getCompressLibraryField() {
        $name = bobsi\Settings::nameCompressLibrary;
        $text = $this->language->get($name);
        $desc = $text['desc'];
        $label = $this->createLabelArray($name);
        $libs = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getCompressLibraryOptions();

        $options = array();
        foreach ($libs as $lib => $info) {
            $options[] = array(
                'option' => array('value' => $lib),
                'childNode' => ucfirst($lib));
            if (bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getCompressLibrary() == $lib) {
                $options[count($options) - 1]['option']['selected'] = 'selected';
            }
        }

        $input = array(
            'select' => array('name' => $name, 'id' => $name, 'class' => 'bobsi-input',),
            'childNode' => $options);
        return $this->formField->getField($input, $label, $desc);
    }

    public function getMinQuantityField() {
        $name = bobsi\Settings::nameDefaultStockQuantity;
        $text = $this->language->get($name);
        $desc = $text['desc'];
        return $this->formField->getField($this->createInputArray($name), $this->createLabelArray($name), $desc);
    }


    public function getEmailField() {
        $name = bobsi\Settings::nameEmailNotificationAddresses;
        $text = $this->language->get($name);
        $desc = $text['desc'];
        return $this->formField->getField($this->createInputArray($name), $this->createLabelArray($name), $desc);
    }

    public function getCheckboxNotificationField() {
        $name = bobsi\Settings::nameEnableEmailNotifications;
        $text = $this->language->get($name);
        $desc = $text['desc'];

        $input = array('input' => array(
            'type' => 'checkbox',
            'name' => $name,
            'id' => $name,
            'value' => 1
        ));

        ($this->formData[$name]) && $input['input']['checked'] = 'checked';

        return $this->formField->getField($input, $this->createLabelArray($name), $desc);
    }

    public function getLoggingLevelField() {
        $name = bobsi\Settings::nameLoggingLevel;
        $text = $this->language->get($name);
        $desc = $text['desc'];
        $logging_levels = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getLoggingLevelOptions();

        $options = array();
        foreach ($logging_levels as $level) {
            $options[] = array(
                'option' => array('value' => $level),
                'childNode' => ucfirst($level));
            if (bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getLoggingLevel() == $level) {
                $options[count($options) - 1]['option']['selected'] = 'selected';
            }
        }

        $input = array(
            'select' => array('name' => $name, 'id' => $name, 'class' => 'bobsi-input'),
            'childNode' => $options);
        return $this->formField->getField($input, $this->createLabelArray($name), $desc);
    }

    /*
     * Functions for "Export criteria" fieldset
     */

    public function getExportQuantityMoreThanField() {
        $name = bobsi\Settings::nameExportQuantityMoreThan;
        $text = $this->language->get($name);
        $desc = $text['desc'];
        return $this->formField->getField($this->createInputArray($name), $this->createLabelArray($name), $desc);
    }

    public function getCategoriesField() {
        $field = new BidorbuyStoreIntegratorField();
        $export_categories = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getExcludeCategories();
        if (!isset($export_categories)) $export_categories = array();
        $included_categories = '<select id="bobsi-inc-categories" class="bobsi-categories-select"
                                name="bobsi_inc_categories[]" multiple="multiple" size="9">';
        $excluded_categories = '<select id="bobsi-exc-categories" class="bobsi-categories-select"
                                                name=" ' . bobsi\Settings::nameExcludeCategories . '[]"'
            . ' multiple="multiple" size="9">';

        foreach ($this->categories as $category) {
            $id = ($category['category_id']);
            $t = '<option value="' . $id . '">' . ($category['name']) . '</option>';
            if (in_array($id, $export_categories)) {
                $excluded_categories .= $t;
            } else {
                $included_categories .= $t;
            }
        }
        $included_categories .= '</select>';
        $excluded_categories .= '</select>';


        $html[] = '<table><tr><td><label for="bobsi-inc-categories">Included Categories</label></td>
                    <td></td><td><label for="bobsi-exc-categories">Excluded Categories</label></td></tr>';
        $html[] = '<tr><td>' . $included_categories . '</td>';
        $html[] = '<td>
                    <p class="submit"><a name="include" id="include" class="button" type="button">< Include</a></p>
                    <p class="submit"><a name="exclude" id="exclude" class="button" type="button">> Exclude</a></p>
                   </td>';
        $html[] = '<td>' . $excluded_categories . '</td></tr></table>';

        $field->input = implode($html);
        return $field;
    }


    protected function loadFormData() {
        $data = (array)bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings();
        $data = array_shift($data);
        return $data;
    }

    private function  createLabelArray($name, $tip = true) {
        $tip = version_compare(VERSION, '2.0') > 0 ? false : $tip;

        $text = $this->language->get($name);
        $label = array('label' => array('id' => $name . '-lbl', 'for' => $name),
            'childNode' => $text['title']);
//            'childNode' => $this->language['export_configs_username_title']);
//            'childNode' => $this->wordings[$name][bobsi\Settings::nameWordingsTitle]);
        if ($tip) {
            $label['label']['class'] = 'hasTip';
            $label['label']['title'] = $text['title'] .
                '::' . $text['desc'];
        }

        return $label;
    }

    private function createInputArray($name, $type = 'text') {
        $input = array('input' => array(
            'type' => $type,
            'name' => $name,
            'id' => $name,
            'class' => 'bobsi-input',
            'value' => $this->formData[$name]
        ));
        return $input;
    }
}


class BidorbuyStoreIntegratorField {
    public $label;
    public $input;
    public $desc;
    private $tag = '';

    public function getField($input, $label = array(), $desc = '') {
        $field = new self;
        $field->input = $input ? $field->createHtmlNode($input) : '';
        $field->label = $label ? $field->createHtmlNode($label) : '';
        $field->desc = $desc;

        return $field;
    }

    private function createHtmlNode($nodes = array(), $is_tag_name = true, &$html_string = '', &$tags = array(), $iterationIndex = 0) {
        if ($is_tag_name) {
            $keys = array_keys($nodes);
            $this->tag = $keys[0];
            $tags[] = $keys[0];
            $html_string .= '<' . $this->tag . ' ';
        }

        foreach ($nodes as $node => $attr) {
            if ($node == 'childNode') {

                if (is_array($attr)) {
                    foreach ($attr as $child) {
                        $this->createHtmlNode($child, true, $html_string, $tags, ++$iterationIndex);
                    }
                    $html_string .= '</' . $tags[0] . '>';
                } else {
                    $html_string .= $attr;
                    $html_string .= '</' . $tags[$iterationIndex] . '>';
                }
//                    $html_string .= '</' . $this->tags[0] . '>';
            } elseif (is_array($attr)) {
                $this->createHtmlNode($attr, false, $html_string, $tags);
            } elseif (next($nodes) === false) {
                $html_string .= ' ' . $node . '="' . $attr . '"';
                $html_string .= '>';
            } else {
                $html_string .= ' ' . $node . '="' . $attr . '"';
            }
        }
        return $html_string;
    }
}

?>
