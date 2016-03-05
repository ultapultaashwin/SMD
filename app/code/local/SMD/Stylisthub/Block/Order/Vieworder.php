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
 * View order details page functionality
 */
class SMD_Stylisthub_Block_Order_Vieworder extends Mage_Core_Block_Template {

    /**
     * Function to get particular order information
     *
     * Passed the order id to get that order information
     * @param $orderId
     *
     * Return order details
     * @return array
     */
    function viewOrder($orderId) {
        $order = Mage::getModel('stylisthub/commission')->getCollection();
        $order->addFieldToSelect('*');
        $order->addFieldToFilter('stylist_id', Mage::getSingleton('customer/session')->getCustomer()->getId());
        $order->addFieldToFilter('order_id', $orderId);
        return $order;
    }

}
