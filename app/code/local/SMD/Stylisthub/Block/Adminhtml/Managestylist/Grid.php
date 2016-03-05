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
 * Manage Stylists Grid
 *
 */
class SMD_Stylisthub_Block_Adminhtml_Managestylist_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Construct the inital display of grid information
     * Set the default sort for collection
     * Set the sort order as "DESC"
     *
     * Return array of data to display stylist information
     * @return array
     */
    public function __construct() {
        parent::__construct();
        $this->setId('managestylistGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Function to get stylist collection
     *
     * Return the stylist information
     * return array
     */
    protected function _prepareCollection() {
        $gid = Mage::helper('stylisthub')->getGroupId();
        $collection = Mage::getResourceModel('customer/customer_collection')
                ->addNameToSelect()
                ->addAttributeToSelect('email')
                ->addAttributeToSelect('created_at')
                ->addAttributeToSelect('group_id')
                ->addAttributeToSelect('customerstatus')
                ->addFieldToFilter('group_id', $gid);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Function to display fields with data
     *
     * Display information about Stylist
     * @return void
     */
    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('customer')->__('ID'),
            'width' => '40px',
            'index' => 'entity_id',
        ));
        $this->addColumn('store_title', array(
            'header' => Mage::helper('customer')->__('Store Name'),
            'width' => '150px',
            'index' => 'store_title',
            'filter' => false,
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Storetitle'
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('customer')->__('Name'),
            'width' => '200px',
            'index' => 'name'
        ));
        $this->addColumn('email', array(
            'header' => Mage::helper('customer')->__('Email'),
            'width' => '160px',
            'index' => 'email'
        ));
        $this->addColumn('contact', array(
            'header' => Mage::helper('customer')->__('Contact'),
            'width' => '150px',
            'index' => 'contact',
            'filter' => false,
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Contact'
        ));
        $this->addColumn('customer_since', array(
            'header' => Mage::helper('customer')->__('Customer Since'),
            'type' => 'datetime',
            'width' => '150px',
            'align' => 'center',
            'index' => 'created_at',
            'gmtoffset' => true
        ));
        $this->addColumn('total_products', array(
            'header' => Mage::helper('customer')->__('#Products'),
            'width' => '100px',
            'index' => 'total_products',
            'align' => 'right',
            'filter' => false,
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Totalproducts'
        ));

        $this->addColumn('commission', array(
            'header' => Mage::helper('customer')->__('Commission(%)'),
            'width' => '70px',
            'index' => 'commission',
            'align' => 'right',
            'filter' => false,
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Commissionrate'
        ));
        $this->addColumn('total_sales', array(
            'header' => Mage::helper('customer')->__('Total Sales'),
            'width' => '80px',
            'index' => 'total_sales',
            'align' => 'right',
            'filter' => false,
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Totalsales'
        ));
        $this->addColumn('amount_received', array(
            'header' => Mage::helper('sales')->__('Received'),
            'width' => '80px',
            'align' => 'right',
            'index' => 'entity_id',
            'filter' => false,
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Amountreceived'
        ));
        $this->addColumn('amount_remaining', array(
            'header' => Mage::helper('sales')->__('Remaining'),
            'width' => '80px',
            'align' => 'right',
            'index' => 'entity_id',
            'filter' => false,
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Amountremaining'
        ));
        $this->addColumn('customerstatus', array(
            'header' => Mage::helper('customer')->__('Status'),
            'width' => '150px',
            'type' => 'options',
            'index' => 'customerstatus',
            'options' => Mage::getSingleton('stylisthub/status_status')->getOptionArray()
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('stylisthub')->__('Action'),
            'type' => 'action',
            'width' => '200px',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('stylisthub')->__('Pending'),
                    'url' => array('base' => '*/*/pending/'),
                    'field' => 'id',
                    'title' => Mage::helper('stylisthub')->__('Pending')
                ),
                array(
                    'caption' => Mage::helper('stylisthub')->__('Approve'),
                    'url' => array('base' => '*/*/approve/'),
                    'field' => 'id',
                    'title' => Mage::helper('stylisthub')->__('Approve')
                ),
                array(
                    'caption' => Mage::helper('stylisthub')->__('Disapprove'),
                    'url' => array('base' => "*/*/disapprove"),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('stylisthub')->__('Delete'),
                    'url' => array('base' => "*/*/delete"),
                    'field' => 'id',
                    'confirm' => Mage::helper('stylisthub')->__('Are you sure?')
                )
            ),
            'sortable' => false
        ));
        /**
         * set commission
         */
        $this->addColumn('set_commission', array(
            'header' => Mage::helper('stylisthub')->__('Set Commission'),
            'width' => '80',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('stylisthub')->__('Commission'),
                    'url' => array('base' => '*/*/setcommission/'),
                    'field' => 'id',
                    'title' => Mage::helper('stylisthub')->__('Commission')
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));
        /**
         * Edit Action
         */
        $this->addColumn('order', array(
            'header' => Mage::helper('stylisthub')->__('Order'),
            'width' => '80',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('stylisthub')->__('Order'),
                    'url' => array('base' => 'stylisthubadmin/adminhtml_order/index/'),
                    'field' => 'id'
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
     * Function for Mass edit action(approve,disapprove or delete)
     *
     * Will change the status of the stylist
     * return void
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('stylisthub');
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('stylisthub')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('stylisthub')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('approve', array(
            'label' => Mage::helper('customer')->__('Approve'),
            'url' => $this->getUrl('*/*/massApprove')
        ));
        $this->getMassactionBlock()->addItem('disapprove', array(
            'label' => Mage::helper('customer')->__('Disapprove'),
            'url' => $this->getUrl('*/*/massDisapprove')
        ));
        return $this;
    }

    /**
     * Function for link url
     *
     * Return the stylist profile edit url
     * return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('adminhtml/customer/edit/', array('id' => $row->getId()));
    }

}
