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
 * This file is used to compare stylist product price with others stylist products
 */
class SMD_Stylisthub_Block_Allcompareprice extends Mage_Core_Block_Template {

    protected function _prepareLayout() {
        parent::_prepareLayout();
        $productId = $this->getRequest()->getParam('id');
        if (is_numeric($productId) || $productId == '') {
            $collection = $this->getComparePrice($productId);
            $this->setCollection($collection);
            $pager = $this->getLayout()
                    ->createBlock('page/html_pager', 'my.pager')
                    ->setCollection($collection);
            $pager->setAvailableLimit(array(10 => 10, 20 => 20, 30 => 30, 50 => 50));
            $pager->setLimit(10);
            $this->setChild('pager', $pager);
        }
        return $this;
    }

    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    /**
     * Get Product Collection with 'compare_product_id' attribute filter
     *
     * Passed the product id for which we need to compare price
     * @param int $productId
     *
     * Return the product collection as array
     * @return array
     */
    public function getComparePrice($productId) {
        $products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('compare_product_id', array('like' => '%' . $productId . '%'))
                ->setOrder('price', 'asc');
        return $products;
    }

    /**
     * Get Review Collection of the particular stylist
     *
     * Passed the stylist id to get the review collection
     * @param int $stylistId
     *
     * Return the stylist reviews count
     * @return int
     */
    public function getReviewsCount($stylistId) {
        $storeId = Mage::app()->getStore()->getId();
        $reviewsCollection = Mage::getModel('stylisthub/stylistreview')->getCollection()
                ->addFieldToFilter('stylist_id', $stylistId)
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('store_id', $storeId);
        $reviewsCount = $reviewsCollection->getSize();
        return $reviewsCount;
    }

    /**
     * Calculating average rating for each stylist
     *
     * Passed the stylist id to get the review collection
     * @param int $stylistId
     *
     * Return the average ratings
     * @return int
     */
    public function averageRatings($stylistId) {
        /**
         *  Review Collection to retrive the ratings of the stylist
         */
        $storeId = Mage::app()->getStore()->getId();
        $reviews = Mage::getModel('stylisthub/stylistreview')->getCollection()
                ->addFieldToFilter('stylist_id', $stylistId)
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('store_id', $storeId);
        /**
         *  Calculate average ratings
         */
        $ratings = array();
        $avg = 0;
        if (count($reviews) > 0) {
            foreach ($reviews as $review) {
                $ratings[] = $review->getRating();
            }
            $count = count($ratings);
            $avg = array_sum($ratings) / $count;
        }
        return round($avg, 1);
    }

}
