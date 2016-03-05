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
 * This file is used get all stylist information
 */
class SMD_Stylisthub_Block_Allstylist extends Mage_Core_Block_Template {

    /**
     * Function to get all stylist collection
     *
     * @return \SMD_Stylisthub_Block_Allreview
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $stylist_collection = $this->getallStylist();
        $this->setCollection($stylist_collection);
        $pager = $this->getLayout()
                ->createBlock('page/html_pager', 'my.pager')
                ->setCollection($stylist_collection);
        $this->setChild('pager', $pager);
        $pager->setAvailableLimit(array(10 => 10, 20 => 20, 30 => 30, 50 => 50));
        $pager->setLimit(10);
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
     * Function to get all stylist collection
     *
     * Return all stylist data as array
     * @return array
     */
    function getallStylist() {
        $tableName = Mage::getSingleton("core/resource")->getTableName('stylisthub_stylistprofile');
        $model = Mage::getModel('customer/customer')->getCollection()->addAttributeToFilter('customerstatus', 1);
        $model->getSelect()->join(array('t2' => $tableName), 'e.entity_id = t2.stylist_id', array('store_logo' => 't2.store_logo', 'store_title' => 't2.store_title'));
        return $model;
    }

}
