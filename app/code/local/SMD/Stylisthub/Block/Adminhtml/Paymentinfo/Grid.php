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
class SMD_Stylisthub_Block_Adminhtml_Paymentinfo_Grid extends Mage_Adminhtml_Block_Widget_Grid {

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
        $this->setId('paymentinfoGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Function to get commission payment collection
     *
     * Return the stylist commission payment information
     * return array
     */
    protected function _prepareCollection() {
        $stylist_id = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('stylisthub/transaction')
                ->getCollection()
                ->addFieldToFilter('stylist_id', $stylist_id)
                ->addFieldToFilter('paid', 1);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Function to display fields with data
     *
     * Display information about payment
     * @return void
     */
    protected function _prepareColumns() {
        $this->addColumn('id', array(
            'header' => Mage::helper('stylisthub')->__('Transaction ID'),
            'width' => '100px',
            'index' => 'id',
        ));
        $this->addColumn('order_id', array(
            'header' => Mage::helper('stylisthub')->__('Order ID'),
            'width' => '100px',
            'index' => 'order_id',
        ));
        $this->addColumn('stylist_commission', array(
            'header' => Mage::helper('stylisthub')->__('Stylist Amount'),
            'width' => '100px',
            'index' => 'stylist_commission',
        ));
        $this->addColumn('admin_commission', array(
            'header' => Mage::helper('stylisthub')->__('Admin Commission'),
            'width' => '100px',
            'index' => 'admin_commission',
        ));
        $this->addColumn('paid_date', array(
            'header' => Mage::helper('customer')->__('Paid On'),
            'width' => '100px',
            'align' => 'center',
            'index' => 'paid_date',
            'gmtoffset' => true
        ));
        $this->addColumn('acknowledge_date', array(
            'header' => Mage::helper('customer')->__('Acknowledge On'),
            'align' => 'center',
            'width' => '100px',
            'index' => 'acknowledge_date',
            'gmtoffset' => true
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
        return parent::_prepareColumns();
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
