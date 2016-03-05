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
 * This file is used to get the customer review list functionality
 */
class SMD_Stylisthub_Block_Customerreview extends Mage_Core_Block_Template
{
    /**
     * Function to get all review collection with pagination
     *
     * @return SMD_Stylisthub_Block_Customerreview
     */
       protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $reviewCollection = $this->getCustomer();
        $this->setCollection($reviewCollection);
        $pager = $this->getLayout()
                ->createBlock('page/html_pager', 'my.pager')
                ->setCollection($reviewCollection);
        $pager->setAvailableLimit(array(10 => 10,20 => 20,30=>30,50=>50));
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
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    /**
     * Function to get all review collection
     *
     * Return customer review collection as array
     * @return array
     */
    function getCustomer()
    {
       if (Mage::getSingleton('customer/session')->isLoggedIn()) {
           $customer    = Mage::getSingleton('customer/session')->getCustomer();
           $id          = $customer->getId();
           $storeId    = Mage::app()->getStore()->getId();
           $collection  = Mage::getModel('stylisthub/stylistreview')
                            ->getCollection()
                            ->addFieldToFilter('status',1)
                             ->addFieldToFilter('store_id',$storeId)
                            ->addFieldToFilter('customer_id',$id);
       }
       return $collection;
   }
}
