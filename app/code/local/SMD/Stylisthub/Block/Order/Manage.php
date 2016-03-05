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
 * Manage order information
 */
class SMD_Stylisthub_Block_Order_Manage extends Mage_Core_Block_Template {

    /**
     * Collection for manage orders
     *
     * @return \SMD_Stylisthub_Block_Order_Manage
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $manageCollection = $this->getstylistOrders();
        $this->setCollection($manageCollection);
        $pager = $this->getLayout()
                ->createBlock('page/html_pager', 'my.pager')
                ->setCollection($manageCollection);
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
     * Function to get stylist order details
     *
     * Return stylist orders information
     * @return array
     */
    public function getstylistOrders() {
        $data = $status = $selectFilter = $from = $to = '';
        $data = $this->getRequest()->getPost();
        if (isset($data['status'])) {
            $status = $data['status'];
        }
        if (isset($data['select_filter'])) {
            $selectFilter = $data['select_filter'];
        }
        if (!empty($selectFilter)) {
            switch ($selectFilter) {
                case "today":
                    /**
                     * today interval
                     */
                    $startDay = strtotime("-1 today midnight");
                    $endDay = strtotime("-1 tomorrow midnight");
                    $from = date("Y-m-d", $startDay);
                    $to = date("Y-m-d", $endDay);
                    $fromDisplay = date("Y-m-d", $startDay);
                    $toDisplay = date("Y-m-d", $startDay);
                    break;
                case "yesterday":
                    /**
                     *  yesterday interval
                     */
                    $startDay = strtotime("-1 yesterday midnight");
                    $endDay = strtotime("-1 today midnight");
                    $from = date("Y-m-d", $startDay);
                    $to = date("Y-m-d", $endDay);
                    $fromDisplay = date("Y-m-d", $startDay);
                    $toDisplay = date("Y-m-d", $startDay);
                    break;
                case "lastweek":
                    /**
                     *  last week interval
                     */
                    $to = date('d-m-Y');
                    $toDay = date('l', strtotime($to));
                    /**
                     *  if today is monday, take last monday
                     */
                    if ($toDay == 'Monday') {
                        $startDay = strtotime("-1 monday midnight");
                        $endDay = strtotime("yesterday");
                    } else {
                        $startDay = strtotime("-2 monday midnight");
                        $endDay = strtotime("-1 sunday midnight");
                    }
                    $from = date("Y-m-d", $startDay);
                    $to = date("Y-m-d", $endDay);
                    $to = date('Y-m-d', strtotime($to . ' + 1 day'));
                    $fromDisplay = $from;
                    $toDisplay = date("Y-m-d", $endDay);
                    break;
                case "lastmonth":
                    /**
                     *  last month interval
                     */
                    $from = date('Y-m-01', strtotime('last month'));
                    $to = date('Y-m-t', strtotime('last month'));
                    $to = date('Y-m-d', strtotime($to . ' + 1 day'));
                    $fromDisplay = $from;
                    $toDisplay = date('Y-m-t', strtotime('last month'));
                    break;
                case "custom":
                    /**
                     *  last custom interval
                     */
                    $from = date('Y-m-d', strtotime($data['date_from']));
                    $to = date('Y-m-d', strtotime($data['date_to'] . ' + 1 day'));
                    $fromDisplay = $from;
                    $toDisplay = date('Y-m-d', strtotime($data['date_to']));
                    break;
            }
        }
        /**
         *  Convert local date to magento db date.
         */
        $dbFrom = Mage::getModel('core/date')->gmtDate(null, strtotime($from));
        $dbTo = Mage::getModel('core/date')->gmtDate(null, strtotime($to));
        $orders = Mage::getModel('stylisthub/commission')->getCollection();
        $orders->addFieldToSelect('*');
        $orders->addFieldToFilter('stylist_id', Mage::getSingleton('customer/session')->getCustomer()->getId());
        if ($status != '') {
            $orders->addFieldToFilter('order_status', array('in' => array($status)));
        }
        if ($selectFilter != '') {
            $orders->addFieldToFilter('created_at', array('from' => $dbFrom, 'to' => $dbTo));
        }
        $orders->setOrder('order_id', 'desc');
        return $orders;
    }

}
