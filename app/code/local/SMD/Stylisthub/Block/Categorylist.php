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
 * This file is used for category wise display of stylist products
 */
class SMD_Stylisthub_Block_Categorylist extends Mage_Core_Block_Template {

    /**
     * Collection for category products
     *
     * @return \SMD_Stylisthub_Block_Categorylist
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $stylistCategoryCollection = $this->getStylistcategoryproducts();
        $this->setCollection($stylistCategoryCollection);
        $pager = $this->getLayout()
                ->createBlock('page/html_pager', 'my.pager')
                ->setCollection($stylistCategoryCollection);
        $pager->setAvailableLimit(array(10 => 10, 20 => 20, 30 => 30, 50 => 50));
        $pager->setLimit(10);
        $this->setChild('pager', $pager);
        return $this;
    }

    /**
     * Function to get pagination
     *
     * Return pagination for collection
     * @return array
     */
    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    /**
     * Function to get stylist product using categories
     *
     * Return category products as array
     * @return array
     */
    function getStylistcategoryproducts() {
        $id = $this->getRequest()->getParam('id');
        $catId = $this->getRequest()->getParam('cat');
        $sortProduct = $this->getRequest()->getParam('sorting');
        $catagoryModel = Mage::getModel('catalog/category')->load($catId);
        $collection = Mage::getResourceModel('catalog/product_collection');
        /*
         * category filter
         */
        $collection->addCategoryFilter($catagoryModel);
        /*
         * only enabled product
         */
        $collection->addAttributeToFilter('status', 1);
        /*
         * add product attribute to be fetched
         */
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('stylist_id', $id);
        $collection->addStoreFilter();
        $collection->addAttributeToSort($sortProduct);
        return $collection;
    }

    /**
     * Function to get particular category information
     *
     * Return category information as array
     * @return array
     */
    function getCategoryinfo() {
        $catId = $this->getRequest()->getParam('cat');
        $category = Mage::getModel('catalog/category')->load($catId);
        return $category;
    }

}
