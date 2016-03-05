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
 * Display stylist commission in admin grid
 */
class SMD_Stylisthub_Block_Adminhtml_Commission_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Construct the inital display of grid information
     * Set the default sort for collection
     * Set the sort order as "DESC"
     *
     * Return array of data to with stylist commission information
     * @return array
     */
    public function __construct() {
        parent::__construct();
        $this->setId('commissionGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Get collection from commission table
     *
     * Return array of data to with stylist commission information
     * @return array
     */
    protected function _prepareCollection() {
        $gid = Mage::helper('stylisthub')->getGroupId();
        $collection = Mage::getResourceModel('customer/customer_collection')
                ->addNameToSelect()
                ->addAttributeToSelect('email')
                ->addAttributeToSelect('created_at')
                ->addAttributeToSelect('group_id')
                ->addFieldToFilter('group_id', $gid);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Display the Stylist Commission in grid
     *
     * Display information about Stylist Commission
     * @return void
     */
    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('stylisthub')->__('Stylist ID'),
            'width' => '40px',
            'index' => 'entity_id',
            'type' => 'number',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('customer')->__('Name'),
            'width' => '200px',
            'index' => 'name'
        ));
        $this->addColumn('email', array(
            'header' => Mage::helper('customer')->__('Email'),
            'width' => '200px',
            'index' => 'entity_id',
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Orderstylistdetails'
        ));
        $this->addColumn('amount_received', array(
            'header' => Mage::helper('sales')->__('Amount Received'),
            'align' => 'right',
            'index' => 'entity_id',
            'width' => '150px',
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Amountreceived'
        ));
        $this->addColumn('amount_remaining', array(
            'header' => Mage::helper('sales')->__('Amount Remaining'),
            'align' => 'right',
            'index' => 'entity_id',
            'width' => '150px',
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Amountremaining'
        ));
        $this->addColumn('payment_mode', array(
            'header' => Mage::helper('sales')->__('Payment Mode'),
            'align' => 'left',
            'index' => 'entity_id',
            'filter' => false,
            'width' => '200px',
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Paymentmode'
        ));
        /**
         * Pay action
         */
        $this->addColumn('action', array(
            'header' => Mage::helper('stylisthub')->__('Actions'),
            'align' => 'center',
            'width' => '100',
            'index' => 'id',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Payment'
        ));
        $this->addColumn('payment_comment', array(
            'header' => Mage::helper('sales')->__('Comments'),
            'align' => 'right',
            'index' => 'entity_id',
            'filter' => false,
            'width' => '200px',
            'renderer' => 'SMD_Stylisthub_Block_Adminhtml_Renderersource_Paymentcomment'
        ));
        $this->addColumn('customer_since', array(
            'header' => Mage::helper('customer')->__('Customer Since'),
            'type' => 'datetime',
            'align' => 'center',
            'index' => 'created_at',
            'filter' => false,
            'sortable' => false,
            'gmtoffset' => true
        ));
        return parent::_prepareColumns();
    }

    /**
     * Provide the link url of the data displayed
     *
     * Link url is set to payment grid
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('stylisthubadmin/adminhtml_paymentinfo/index/', array('id' => $row->getId()));
    }

}
