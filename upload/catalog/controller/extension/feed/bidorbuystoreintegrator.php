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

require_once(DIR_SYSTEM . '/../bidorbuystoreintegrator/factory.php');

/**
 * Class ControllerFeedBidorbuyStoreIntegrator.
 *
 * @codingStandardsIgnoreStart
 */
class ControllerFeedBidorbuyStoreIntegrator extends Controller {
    // @codingStandardsIgnoreEnd
    private $shipMethods = array();
    private $modelExtension = NULL;

    /**
     * ControllerFeedBidorbuyStoreIntegrator constructor.
     *
     * @param mixed $registry registry
     *
     * @return void.
     */
    public function __construct($registry) {
        parent::__construct($registry);

        $bobsi = new BobsiInit();
        $bobsi->init($this->config);

        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('setting/setting');

        // Version >= 2.0
        if (version_compare(VERSION, '2.0') >= 0) {
            $this->load->model('extension/extension');
            $this->modelExtension = $this->model_extension_extension;
        } else {
            $this->load->model('setting/extension');
            $this->modelExtension = $this->model_setting_extension;
        }

        //Get shipment classes enabled in store
        foreach ($this->modelExtension->getExtensions('shipping') as $shipMethod) {
            $extData = $this->model_setting_setting->getSetting($shipMethod['code']);
            //Get only active extensions
            if (isset($extData[$shipMethod['code'] . '_status'])
                && $extData[$shipMethod['code'] . '_status'] == '1') {
                //Get full name from shipping language file
                $this->shipMethods[] = ($this->language->load(EXTENSION_PATH . 'shipping/' . $shipMethod['code'])) ?
                    $this->language->get('text_title')
                    : ucfirst($shipMethod['code']);
            }
        }
    }

    /**
     * Get Breadcrumb
     *
     * @param mixed $categoryId category id
     *
     * @return string
     */
    public function getBreadcrumb($categoryId) {
        $category = $this->model_catalog_category->getCategory($categoryId);
        $parents = (intval($categoryId) > 0
            && is_array($category)
            && isset($category['category_id'])
            && is_numeric($category['category_id'])) ? $this->getParentList($category['category_id']) : array();

        $names = array();
        foreach ($parents as $c) {
            array_unshift($names, $c['name']);
        }
        return implode(' > ', $names);
    }

    /**
     * Parent list
     *
     * @param mixed $parentId parent id
     * @param array $parentList list
     *
     * @return array
     */
    public function getParentList($parentId, &$parentList = array()) {
        $parent = $this->model_catalog_category->getCategory($parentId);

        $parentList[] = $parent;
        if (isset($parent['parent_id']) && is_numeric($parent['parent_id']) && intval($parent['parent_id']) > 0) {
            $this->getParentList($parent['parent_id'], $parentList);
        }
        return $parentList;
    }

    /**
     * Get products
     *
     * @param array $exportConfiguration configuration.
     *
     * @return array
     */
    public function &getProducts(&$exportConfiguration = array()) {
        $itemsPerIteration = intval($exportConfiguration[bobsi\Settings::paramItemsPerIteration]);
        $iteration = intval($exportConfiguration[bobsi\Settings::paramIteration]);
        $categoryId = $exportConfiguration[bobsi\Settings::paramCategoryId];

        $products = array();
        if ($categoryId == '0') {

            $uncat_q = $this->db->query(
                'SELECT  `product_id` FROM ' . DB_PREFIX . 'product WHERE product_id NOT IN (SELECT `product_id` FROM '
                . DB_PREFIX . 'product_to_category)'
            );
            foreach ($uncat_q->rows as $pid) {
                $products[$pid['product_id']] = $this->model_catalog_product->getProduct($pid['product_id']);
            }

        } else {
            $products = $this->model_catalog_product->getProducts(array('filter_category_id' => $categoryId));
        }

        $products_slice = array_slice($products, $itemsPerIteration * $iteration, $itemsPerIteration);
        return $products_slice;
    }

    /**
     * Get all products.
     *
     * @return array
     */
    public function &getAllProducts() {
        $productsIds = array();
        $r = $this->db->query('SELECT `product_id` FROM ' . DB_PREFIX . 'product WHERE `status` = 1');

        if (isset($r->rows) and !empty($r->rows)) {
            foreach ($r->rows as $id) {
                $productsIds[] = $id['product_id'];
            }
        }

        return $productsIds;
    }

    /**
     * Export products
     *
     * @param mixed $productsIds product ids
     * @param array $exportConfiguration config
     *
     * @return array
     */
    public function exportProducts($productsIds, $exportConfiguration) {

        $exportQuantityMoreThan = bobsi\StaticHolder::getBidorbuyStoreIntegrator()
            ->getSettings()->getExportQuantityMoreThan();

        $defaultStockQuantity = bobsi\StaticHolder::getBidorbuyStoreIntegrator()
            ->getSettings()->getDefaultStockQuantity();
        
        $exportProducts = array();
        foreach ($productsIds as $productId) {
            $product = $this->model_catalog_product->getProduct($productId);

            if (!$product) {
                continue;
            }

            $product['categories'] = array();

            foreach ($this->model_catalog_product->getCategories($product['product_id']) as $category) {
                $product['categories'][] = $category['category_id'];
            }

            $product['categories'] = (empty($product['categories'])) ? array('0') : $product['categories'];

            $categoriesMatching = array_intersect(
                $exportConfiguration[bobsi\Settings::paramCategories], $product['categories']
            );

            if (empty($categoriesMatching)) {
                continue;
            }

            $variations = $this->buildProductVariations($product);

            if ($this->calcProductQuantity($product, $defaultStockQuantity) > $exportQuantityMoreThan) {
                //If variation available - process it as independent product
                if (!empty($variations)) {
                    foreach ($variations as $variation) {
                        //Get Q-ty from variation
                        foreach ($variation as $key => $val) {
                            $variationQty = $val['quantity'];
                        }

                        $p = $this->buildExportProduct($product, $variation);
                        //Check if PRICE > 0 AND variation quantity > saved in settings number
                        if (intval($p[bobsi\Tradefeed::nameProductPrice]) > 0
                            && $variationQty > $exportQuantityMoreThan
                        ) {
                            $exportProducts[] = $p;
                        } else {
                            bobsi\StaticHolder::getBidorbuyStoreIntegrator()->logInfo(
                                'Product price <= 0 or quantity <=0, skipping, product id: ' . $product['product_id']
                            );
                        }
                    }
                } else {
                    $p = $this->buildExportProduct($product);
                    (intval($p[bobsi\Tradefeed::nameProductPrice]) > 0) ? $exportProducts[] = $p 
                        : bobsi\StaticHolder::getBidorbuyStoreIntegrator()
                        ->logInfo('Product price <= 0, skipping, product id: ' . $product['product_id']);
                }
            } else {
                bobsi\StaticHolder::getBidorbuyStoreIntegrator()
                    ->logInfo('QTY is not enough to export product id: ' . $product['product_id']);
            }

        }

        return $exportProducts;
    }

    /**
     * Build Variations
     * 
     * @param object $product product
     *
     * @return array
     */
    protected function buildProductVariations($product) {
        $optionValueElementName = (version_compare(VERSION, '2.0') >= 0) ? 'product_option_value' : 'option_value';
        $optionsArray = $this->model_catalog_product->getProductOptions($product['product_id']);
        $variations = array();
        $sortVariations = array();
        if (!empty($optionsArray)) {
            foreach ($optionsArray as $options) {
                $temp = array();
                $currentTitle = $options['name'];

                $index = 0;
                if (is_array($options[$optionValueElementName])) {
                    foreach ($options[$optionValueElementName] as $option) {
                        $temp[] = array(
                            'id' => $index++,
                            'name' => $option['name'],
                            'operand' => $option['price_prefix'],
                            'adjustment' => floatval($option['price']),
                            'weight' => floatval($option['weight']),
                            'weight_operand' => $option['weight_prefix'],
                            'quantity' => intval($option['quantity'])
                        );
                    }
                } else {
                    $temp[] = array(
                        'id' => $index++,
                        'name' => $options[$optionValueElementName],
                        'operand' => '+',
                        'adjustment' => floatval(0),
                        'weight' => floatval(0),
                        'weight_operand' => '+',
                        'quantity' => intval(0)
                    );
                }

                $sortVariations[$currentTitle] = $temp;
            }
            $variations = $this->arrayCartesian($sortVariations);
        }
        
        return $variations;
    }

    /**
     * Get Export Categories Ids
     *
     * @param array $ids ids
     * @param array $result_ids result ids
     * @param array $cats cats
     *
     * @return array|bool
     */
    protected function getExportCategoriesIds($ids = array(), &$result_ids = array(), $cats = array(0)) {
        $categories = array();
        foreach ($cats as $c) {
            $categories = array_merge($categories, $this->model_catalog_category->getCategories($c));
        }
        $categories_ids = array();
        if (!empty($categories)) {
            foreach ($categories as $cat) {
                $categories_ids[] = $cat['category_id'];
            }
        } else {
            return TRUE;
        }

        $result_ids = array_merge($result_ids, $categories_ids);
        $this->getExportCategoriesIds($ids, $result_ids, $categories_ids);

        //Add Uncategorized
        if (!in_array('0', $result_ids)) {
            $result_ids[] = '0';
        }

        return array_values(array_diff($result_ids, $ids));
    }

    /**
     * Build product for export.
     *
     * @param object $product product
     * @param array $variations variations
     *
     * @return array
     */
    protected function &buildExportProduct(&$product, &$variations = array()) {
        $exportedProduct = array();

        $sku = $product['product_id'];
        $attrs[] = array('name' => 'Brand', 'value' => strval($product['manufacturer']));

        //Calculate price appendix, SKU code, Attributes
        $varPrices = 0;
        $varWeight = 0;
        if (!empty($variations)) {
            $sku .= '-';
            foreach ($variations as $titleVariation => $var) {
                $varPrices += $this->getVariationValue($var['operand'], $var['adjustment']);
                $varWeight += $this->getVariationValue($var['weight_operand'], $var['weight']);
                $attrs[] = array('name' => $titleVariation, 'value' => $var['name']);
                $sku .= $var['id'];
                $product['quantity'] = $var['quantity'];
            }
        }
        $sku .= !empty($product['sku']) ? '-' . $product['sku'] : '';

        $exportedProduct[bobsi\Tradefeed::nameProductId] = $product['product_id'];
        $exportedProduct[bobsi\Tradefeed::nameProductName] = $product['name'];
        $exportedProduct[bobsi\Tradefeed::nameProductCode] = $sku;

        $categories = array();
        foreach ($product['categories'] as $category) {
            $categories[] = $this->getBreadcrumb($category);
        }

        $exportedProduct[bobsi\Tradefeed::nameProductCategory] = join(
            bobsi\Tradefeed::categoryNameDelimiter, $categories
        );

        $priceWithoutReduct = $this->calcPriceWithTax($product['price'] + $varPrices, $product);
        $priceFinal = ($product['special'] != NULL) ?
            $this->calcPriceWithTax($product['special'] + $varPrices, $product) :
            $priceWithoutReduct;

        if ((floatval($priceFinal) < floatval($priceWithoutReduct))) {
            $exportedProduct[bobsi\Tradefeed::nameProductPrice] = $priceFinal;
            $exportedProduct[bobsi\Tradefeed::nameProductMarketPrice] = $priceWithoutReduct;
        } else {
            $exportedProduct[bobsi\Tradefeed::nameProductPrice] = $priceWithoutReduct;
            $exportedProduct[bobsi\Tradefeed::nameProductMarketPrice] = '';
        }

        $exportedProduct[bobsi\Tradefeed::nameProductShippingClass] = implode(', ', $this->shipMethods);

        $description = html_entity_decode($product['description']);
        $exportedProduct[bobsi\Tradefeed::nameProductDescription] = $description;
        $exportedProduct[bobsi\Tradefeed::nameProductCondition] = bobsi\Tradefeed::conditionNew;

        $this->buildExportProductAttributes($product, $varWeight, $attrs);

        $exportedProduct[bobsi\Tradefeed::nameProductAttributes] = $attrs;
        $exportedProduct[bobsi\Tradefeed::nameProductAvailableQty] = $this->calcProductQuantity(
            $product, bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getDefaultStockQuantity()
        );

        $images = $this->getImages($product['image'], $this->model_catalog_product->getProductImages(
            $product['product_id'])
        );

        $exportedProduct[bobsi\Tradefeed::nameProductImages] = $images;

        if (!empty($images)) {
            $exportedProduct[bobsi\Tradefeed::nameProductImageURL] = reset($images);
        }

        return $exportedProduct;
    }

    /**
     * Build arrtibutes
     *
     * @param object $product product
     * @param mixed $varWeight weight
     * @param array $attrs attributes
     *
     * @return array
     */
    protected function buildExportProductAttributes($product, $varWeight, &$attrs) {
        $attributesGroups = $this->model_catalog_product->getProductAttributes($product['product_id']) ?: array();
        foreach ($attributesGroups as $attributesGroup) {
            foreach ($attributesGroup['attribute'] as $attribute) {
                $attrs[] = array('name' => $attribute['name'], 'value' => $attribute['text']);
            }
        }

        if ($product['height'] + 0) {
            $attrs[] = array(
                'name' => bobsi\Tradefeed::nameProductAttrHeight,
                'value' => number_format($product['height'] + 0, 2, '.', '')
            );
        }

        if ($product['width'] + 0) {
            $attrs[] = array(
                'name' => bobsi\Tradefeed::nameProductAttrWidth,
                'value' => number_format($product['width'] + 0, 2, '.', '')
            );
        }

        if ($product['length'] + 0) {
            $attrs[] = array(
                'name' => bobsi\Tradefeed::nameProductAttrLength,
                'value' => number_format($product['length'] + 0, 2, '.', '')
            );
        }

        $weightClass = (version_compare(VERSION, '2.2') >= 0) ?
            new \Cart\Weight($this->registry) : new Weight($this->registry);
        if (($product['weight'] + $varWeight) > 0) {
            $attrs[] = array(
                'name' => bobsi\Tradefeed::nameProductAttrShippingWeight,
                'value' => number_format($product['weight'] + $varWeight, 2, '.', '')
                    . $weightClass->getUnit($product['weight_class_id']));
        }
    }

    /**
     * Export
     *
     * @return void
     */
    public function export() {
        $token = $this->getToken();
        $products = isset($this->request->request[bobsi\Settings::paramIds]) ?
            $this->request->request[bobsi\Settings::paramIds] : FALSE;

        $exportConfiguration = array(
            bobsi\Settings::paramIds => $products,
            bobsi\Tradefeed::settingsNameExcludedAttributes => array('Width', 'Height', 'Length'),
            bobsi\Settings::paramCallbackGetProducts => array($this, 'getAllProducts'),
            bobsi\Settings::paramCallbackGetBreadcrumb => array($this, 'getBreadcrumb'),
            bobsi\Settings::paramCallbackExportProducts => array($this, 'exportProducts'),
            bobsi\Settings::paramCategories => $this->getExportCategoriesIds(
                bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getExcludeCategories()
            ),
            bobsi\Settings::paramExtensions => $this->getInstalledExtensions()
        );

        bobsi\StaticHolder::getBidorbuyStoreIntegrator()->export($token, $exportConfiguration);
    }

    /**
     * Download xml
     *
     * @return void
     */
    public function download() {
        $token = $this->getToken();
        bobsi\StaticHolder::getBidorbuyStoreIntegrator()->download($token);
    }

    /**
     * Download logs
     *
     * @return void
     */
    public function downloadl() {
        $token = $this->getToken();
        bobsi\StaticHolder::getBidorbuyStoreIntegrator()->downloadl($token);
    }

    /**
     * Server info - phpInfo()
     *
     * @return void
     */
    public function version() {
        $token = $this->getToken();
        $phpinfo = isset($this->request->request['phpinfo']) ? $this->request->request['phpinfo'] : 'n';
        bobsi\StaticHolder::getBidorbuyStoreIntegrator()->showVersion($token, 'y' == $phpinfo);
    }

    /**
     * Get token
     *
     * @return mixed
     */
    protected function getToken() {
        return isset($this->request->request[bobsi\Settings::paramToken]) ?
            $this->request->request[bobsi\Settings::paramToken] : FALSE;
    }

    /**
     * Calc Product Quantity
     *
     * @param object $product product
     * @param int $default default
     *
     * @return int
     */
    protected function calcProductQuantity($product, $default = 0) {
        $qty = intval($product['quantity']);
        return $qty ? $qty : (($product['stock_status'] == 'In Stock') ? $default : 0);
    }

    /**
     * Calc Price With Tax
     *
     * @param float $value value
     * @param object $product product
     *
     * @return mixed
     */
    protected function calcPriceWithTax($value, $product) {
        if ($this->config->get('config_tax')) {
            return $this->tax->calculate($value, $product['tax_class_id']);
        } else {
            return $value;
        }
    }

    /**
     * Get Variation Value
     *
     * @param string $operand operand
     * @param mixed $adjustment adjustment
     *
     * @return float|int
     */
    public function getVariationValue($operand, $adjustment) {
        if ($operand == '+' or $operand == '=') {
            return (float)$adjustment;
        } elseif ($operand == '-') {
            return -(float)$adjustment;
        }
        return 0;
    }

    /**
     * Get Installed Extensions
     *
     * @return array
     */
    protected function getInstalledExtensions() {
        $types = scandir(DIR_APPLICATION . '/controller');
        $extensions = array();
        $extensionsSortedByTypes = array();

        foreach ($types as $type) {
            $extensionsSortedByTypes[] = $this->modelExtension->getExtensions($type);
        }

        foreach ($extensionsSortedByTypes as $extensionType) {
            if (is_array($extensionType) && !empty($extensionType)) {
                foreach ($extensionType as $extension) {
                    $extensions[] = (ucfirst($extension['code']));
                }
            }
        }

        return $extensions;
    }

    /**
     * Get images
     *
     * @param mixed $main_image main image
     * @param mixed $extra_images extra image
     *
     * @return array
     */
    protected function getImages($main_image, $extra_images) {
        $images = array();

        // Add main image
        if (isset($main_image) && !empty($main_image)) {
            $images[] = $main_image;
        }

        // Add the rest of the product images
        foreach ($extra_images as $img) {
            $images[] = $img['image'];
        }

        // Make full URLs
        foreach ($images as $key => $image) {
            $images[$key] = ((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'image/' . $image;
        }

        return $images;
    }

    /**
     * Array Cartesian
     *
     * @param array $input input
     *
     * @return array
     */
    protected function arrayCartesian($input) {
        $result = array();

        while (list($key, $values) = each($input)) {
            // If a sub-array is empty, it doesn't affect the cartesian product
            if (empty($values)) {
                continue;
            }

            // Special case: seeding the product array with the values from the first sub-array
            if (empty($result)) {
                foreach ($values as $value) {
                    $result[] = array($key => $value);
                }
            } else {
                // Second and subsequent input sub-arrays work like this:
                //   1. In each existing array inside $product, add an item with
                //      key == $key and value == first item in input sub-array
                //   2. Then, for each remaining item in current input sub-array,
                //      add a copy of each existing array inside $product with
                //      key == $key and value == first item in current input sub-array

                // Store all items to be added to $product here; adding them on the spot
                // inside the foreach will result in an infinite loop
                $append = array();
                foreach ($result as &$product) {
                    // Do step 1 above. array_shift is not the most efficient, but it
                    // allows us to iterate over the rest of the items with a simple
                    // foreach, making the code short and familiar.
                    $product[$key] = array_shift($values);

                    // $product is by reference (that's why the key we added above
                    // will appear in the end result), so make a copy of it here
                    $copy = $product;

                    // Do step 2 above.
                    foreach ($values as $item) {
                        $copy[$key] = $item;
                        $append[] = $copy;
                    }

                    // Undo the side effecst of array_shift
                    array_unshift($values, $product[$key]);
                }

                // Out of the foreach, we can add to $results now
                $result = array_merge($result, $append);
            }
        }

        return $result;
    }
}

/**
 * Class ControllerExtensionFeedBidorbuyStoreIntegrator.
 *
 * @codingStandardsIgnoreStart
 */
class ControllerExtensionFeedBidorbuyStoreIntegrator extends ControllerFeedBidorbuyStoreIntegrator {

}
// @codingStandardsIgnoreEnd
