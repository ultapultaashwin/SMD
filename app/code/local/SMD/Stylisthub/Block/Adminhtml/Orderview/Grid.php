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
 */

/**
 * View order information
 */
class SMD_Stylisthub_Block_Adminhtml_Orderview_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Construct the inital display of grid information
     * Set the default sort for collection
     * Set the sort order as "DESC"
     *
     * Return array of data to view order information
     * @return array
     */
    public function __construct() {
        parent::__construct();
        $this->setId('orderviewGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Function to get order collection
     *
     * Return the stylist product's order information
     * return array
     */
    protected function _prepareCollection() {
        $orders = Mage::getModel('stylisthub/commission')->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('order_status', array('eq' => 'complete'))
                ->addFieldToFilter('status', array('eq' => 1))
                ->setOrder('order_id', 'desc');
        $this->setCollection($orders);
        return parent::_prepareCollection();
    }

    /**
     * Function to display fields with data
     *
     * Display information about orders
     * @return void
     */
    protected function _prepareColumns() {
        $this->addColumn('stylistemail', array(
            'header' => Mage::helper('sales')->__('Stylist detail'),
            'width' => '150px',
            'index' => 'stylist_id',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Orderstylistdetails'
        ));
        $this->addColumn('increment_id', array(
            'header' => Mage::helper('sales')->__('Order #'),
            'width' => '100px',
            'index' => 'increment_id'
        ));
        $this->addColumn('productdetail', array(
            'header' => Mage::helper('stylisthub')->__('Product details'),
            'width' => '150px',
            'index' => 'id',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_OrderProductdetails'
        ));
        $this->addColumn('product_amt', array(
            'header' => Mage::helper('sales')->__('Product Price'),
            'align' => 'right',
            'index' => 'product_amt',
            'width' => '80px',
            'type' => 'currency',
            'currency' => 'order_currency_code',
        ));
        $this->addColumn('stylist_amount', array(
            'header' => Mage::helper('sales')->__('Stylist\'s Earned Amount'),
            'align' => 'right',
            'width' => '80px',
            'index' => 'stylist_amount',
            'type' => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('commission_fee', array(
            'header' => Mage::helper('sales')->__('Commission Fee'),
            'align' => 'right',
            'index' => 'commission_fee',
            'type' => 'currency',
            'width' => '80px',
            'currency' => 'order_currency_code',
        ));
        $this->addColumn('order_created_at', array(
            'header' => Mage::helper('stylisthub')->__('Order At'),
            'align' => 'center',
            'width' => '200px',
            'index' => 'order_id',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Orderdate'
        ));
        /**
         * Credit Action
         */
        $this->addColumn('action', array(
            'header' => Mage::helper('stylisthub')->__('Actions'),
            'align' => 'center',
            'width' => '100px',
            'index' => 'id',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Ordercredit'
        ));
        /**
         * Payment status
         */
        $this->addColumn('payment_status', array(
            'header' => Mage::helper('stylisthub')->__('Ack Status'),
            'align' => 'center',
            'width' => '100px',
            'index' => 'payment_status',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Receivedstatus'
        ));
        /**
         * Acknowledge Date
         */
        $this->addColumn('acknowledge_date', array(
            'header' => Mage::helper('stylisthub')->__('Ack On'),
            'align' => 'center',
            'width' => '100px',
            'index' => 'acknowledge_date',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Acknowledgedate'
        ));
        /**
         * View Action
         */
        $this->addColumn('view', array(
            'header' => Mage::helper('stylisthub')->__('View'),
            'type' => 'action',
            'getter' => 'getOrderId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('stylisthub')->__('View'),
                    'url' => array('base' => 'adminhtml/sales_order/view/'),
                    'field' => 'order_id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));
        return parent::_prepareColumns();
    }

    /**
     * Function for Mass action(credit payment to stylist)
     *
     * Will change the credit order status of the stylist
     * return void
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('stylisthub');
        $this->getMassactionBlock()->addItem('credit', array(
            'label' => Mage::helper('stylisthub')->__('Credit'),
            'url' => $this->getUrl('*/*/masscredit'),
        ));
        return $this;
    }

    /**
     * Function for link url
     *
     * Not redirected to any page
     * return void
     */
    public function getRowUrl($row) {
        return false;
    }

}
