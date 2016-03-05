<?php

/**
 * SMD
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magemobiledesign.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * SMD does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * SMD does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    SMD
 * @package     SMD_Stylisthub
 * @version     1.2.3
 * @author      SMD Team <support@magemobiledesign.com>
 * @copyright   Copyright (c) 2014 SMD. (http://www.magemobiledesign.com)
 * @license     http://www.magemobiledesign.com/LICENSE.txt
 *
 */

/**
 * This file is used display the banner slider in home page
 */
class SMD_Stylisthub_Block_Bannerslide extends Mage_Core_Block_Template {

    /**
     * Get Product Collection
     *
     * Return the product collection as array
     * @return array
     */
    public function getProductCollections() {
        $getProductCollection = Mage::getModel('stylisthub/stylisthub')->getProductCollection();
        return $getProductCollection;
    }

    /**
     * Function to get most popular products
     *
     * Return the most popular product collection as array
     * @return array
     */
    public function getMostpopular() {
        $productCollection = Mage::getResourceModel('reports/product_collection')
                ->addOrderedQty()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', array('eq' => 1));
        return $productCollection;
    }

    /**
     * Function to get the new products
     *
     * Return New product collection as array
     * @return array
     */
    public function getNewproduct() {
        $storeId = Mage::app()->getStore()->getId();
        $todayDate = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $collection = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeId)
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $todayDate))
                ->addAttributeToFilter('news_to_date', array('or' => array(
                        0 => array('date' => true, 'from' => $todayDate),
                        1 => array('is' => new Zend_Db_Expr('null')))
                        ), 'left')
                ->addAttributeToSort('entity_id', 'desc')
                ->addAttributeToFilter('status', array('eq' => 1));
        return $collection;
    }

}
