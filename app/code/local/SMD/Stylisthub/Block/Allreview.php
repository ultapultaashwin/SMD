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
 * This file is used get stylist reviews
 */
class SMD_Stylisthub_Block_Allreview extends Mage_Core_Block_Template {

    /**
     * Collection for manage reviews
     *
     * @return \SMD_Stylisthub_Block_Allreview
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $id = $this->getRequest()->getParam('id');
        if (is_numeric($id) || $id == '') {
            $reviewCollection = $this->getallreview($id);
            $this->setCollection($reviewCollection);
            $pager = $this->getLayout()
                    ->createBlock('page/html_pager', 'my.pager')
                    ->setCollection($reviewCollection);
            $pager->setAvailableLimit(array(10 => 10, 20 => 20, 30 => 30, 50 => 50));
            $pager->setLimit(10);
            $this->setChild('pager', $pager);
        }
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
     * Function to get all review collection
     *
     * Passed stylist id to get the particular stylist reviews
     * @param int $id
     *
     * Return the review collection
     * @return array
     */
    function getallreview($id) {
        $storeId = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('stylisthub/stylistreview')
                ->getCollection()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('stylist_id', $id);
        return $collection;
    }

    /**
     * Function to get save review url
     *
     * Return the save review action url
     * @return string
     */
    function saveReviewUrl() {
        return Mage::getUrl('stylisthub/stylist/savereview');
    }

}
