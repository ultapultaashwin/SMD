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
 * Display order information
 */
class SMD_Stylisthub_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Construct the inital display of grid information
     * Set the default sort for collection
     * Set the sort order as "DESC"
     *
     * Return array of data to display order information
     * @return array
     */
    public function __construct() {
        parent::__construct();
        $this->setId('orderGrid');
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
        $stylistId   = Mage::app()->getRequest()->getParam('id');
        $orders     = Mage::getModel('stylisthub/commission')->getCollection()
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('order_status', array('neq' => 'closed'))
                        ->addFieldToFilter('status', array('eq' => 1))
                        ->addFieldToFilter('stylist_id', $stylistId)
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
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('sales')->__('Order #'),
            'width'     => '100px',
            'index'     => 'increment_id'
        ));
        $this->addColumn('productdetail', array(
            'header'    => Mage::helper('stylisthub')->__('Product details'),
            'width'     => '300px',
            'index'     => 'id',
            'renderer'  => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_OrderProductdetails'
        ));
        $this->addColumn('product_amt', array(
            'header'    => Mage::helper('sales')->__('Product Price'),
            'align'     => 'right',
            'index'     => 'product_amt',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
        ));
        $this->addColumn('stylist_amount', array(
            'header'    => Mage::helper('sales')->__('Stylist\'s Earned Amount'),
            'align'     => 'right',
            'index'     => 'stylist_amount',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
        ));
        $this->addColumn('commission_fee', array(
            'header'    => Mage::helper('sales')->__('Commission Fee'),
            'align'     => 'right',
            'index'     => 'commission_fee',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
        ));
        $this->addColumn('order_status', array(
            'header'    => Mage::helper('stylisthub')->__('Status'),
            'align'     => 'center',
            'width'     => '80px',
            'index'     => 'order_status',
        ));
        $this->addColumn('order_created_at', array(
            'header'    => Mage::helper('stylisthub')->__('Order At'),
            'align'     => 'center',
            'index'     => 'order_id',
            'renderer'  => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Orderdate'
        ));
        /**
         * Credit Action
         */
        $this->addColumn('action', array(
            'header'    => Mage::helper('stylisthub')->__('Actions'),
            'align'     => 'center',
            'width'     => '100',
            'index'     => 'id',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Ordercredit'
        ));
       /**
        * View order
        */
        $this->addColumn('view', array(
            'header'    => Mage::helper('stylisthub')->__('View'),
            'width'     => '80',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('stylisthub')->__('View'),
                    'url'       => array('base' => 'adminhtml/sales_order/view/'),
                    'field'     => 'order_id'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        ));
        /**
         * Export data
         */

        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
        return parent::_prepareColumns();
    }
    /**
     * Function for Mass edit action(credit payment to stylist)
     *
     * Will change the credit order status of the stylist
     * return void
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('stylisthub');
        $this->getMassactionBlock()->addItem('credit', array(
                'label' => Mage::helper('stylisthub')->__('Credit'),
                'url'   => $this->getUrl('*/*/masscredit'),
        ));
        return $this;
    }
    /**
     * Function for link url
     *
     * Not redirect to any page
     * return void
     */
    public function getRowUrl($row) {
        return false;
    }
}
