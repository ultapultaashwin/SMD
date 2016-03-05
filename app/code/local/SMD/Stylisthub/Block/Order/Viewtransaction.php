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
 * View transaction details page functionality
 */
class SMD_Stylisthub_Block_Order_Viewtransaction extends Mage_Core_Block_Template {

    protected function _prepareLayout() {
        parent::_prepareLayout();
        $collection = $this->getTransactionhistory();
        $this->setCollection($collection);
        $pager = $this->getLayout()
                ->createBlock('page/html_pager', 'my.pager')
                ->setCollection($collection);
        $pager->setAvailableLimit(array(10 => 10, 20 => 20, 30 => 30, 50 => 50));
        $pager->setLimit(10);
        $this->setChild('pager', $pager);
        return $this;
    }

    /**
     * Function to get the Pagination
     *
     * Return the collection for pagination
     * @return array
     */
    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    /**
     * Get transaction details of particular stylist
     *
     * Return the transaction details
     * @return array
     */
    public function getTransactionhistory() {
        $customer = Mage::getSingleton("customer/session")->getCustomer();
        $customerId = $customer->getId();
        $collection = Mage::getModel('stylisthub/transaction')->getCollection()->addFieldToFilter('stylist_id', $customerId)->addFieldToFilter('paid', 1)->setOrder('id', 'DESC');
        return $collection;
    }

    /**
     * To get the acknowlege url
     *
     * Passed the commission id to update the acknowledge date
     * @param int $commission_id
     *
     * Return the acknowledge action url
     * @return string
     */
    public function getAcknowledge($commissionId) {
        return Mage::getUrl('stylisthub/order/acknowledge', array('commissionid' => $commissionId));
    }

}
