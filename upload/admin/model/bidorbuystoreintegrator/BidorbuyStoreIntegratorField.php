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

/**
 * Class BidorbuyStoreIntegratorField.
 */
class BidorbuyStoreIntegratorField {
    public $label;
    public $input;
    public $desc;
    private $tag = '';

    /**
     * Get Field.
     *
     * @param array $input input
     * @param array $label label
     * @param string $desc description
     *
     * @return BidorbuyStoreIntegratorField
     */
    public function getField($input, $label = array(), $desc = '') {
        $field = new self;
        $field->input = $input ? $field->createHtmlNode($input) : '';
        $field->label = $label ? $field->createHtmlNode($label) : '';
        $field->desc = $desc;

        return $field;
    }

    /**
     * Create HTML Node
     *
     * @param array $nodes nodes
     * @param bool $is_tag_name flag
     * @param string $html_string html
     * @param array $tags tags
     * @param int $iterationIndex index
     *
     * @return string
     */
    private function createHtmlNode(
        $nodes = array(),
        $is_tag_name = TRUE,
        &$html_string = '',
        &$tags = array(),
        $iterationIndex = 0
    ) {
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
                        $this->createHtmlNode($child, TRUE, $html_string, $tags, ++$iterationIndex);
                    }
                    $html_string .= '</' . $tags[0] . '>';
                } else {
                    $html_string .= $attr;
                    $html_string .= '</' . $tags[$iterationIndex] . '>';
                }
//                    $html_string .= '</' . $this->tags[0] . '>';
            } elseif (is_array($attr)) {
                $this->createHtmlNode($attr, FALSE, $html_string, $tags);
            } elseif (next($nodes) === FALSE) {
                $html_string .= ' ' . $node . '="' . $attr . '"';
                $html_string .= '>';
            } else {
                $html_string .= ' ' . $node . '="' . $attr . '"';
            }
        }
        return $html_string;
    }
}
