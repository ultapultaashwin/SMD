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
 * Manage stylist reviews and ratings
 */
class SMD_Stylisthub_Block_Adminhtml_Stylistreview_Grid extends Mage_Adminhtml_Block_Widget_Grid {

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
        $this->setId('stylistreviewGrid');
        $this->setDefaultSort('stylist_review_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Function to get review collection
     *
     * Return the stylist review information
     * return array
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('stylisthub/stylistreview')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Function to display fields with data
     *
     * Display information about orders
     * @return void
     */
    protected function _prepareColumns() {
        $this->addColumn('stylist_review_id', array(
            'header' => Mage::helper('customer')->__('Review ID'),
            'width' => '40px',
            'index' => 'stylist_review_id',
        ));
        $this->addColumn('customer_at', array(
            'header' => Mage::helper('customer')->__('Reviewed On'),
            'type' => 'datetime',
            'align' => 'center',
            'index' => 'created_at',
            'gmtoffset' => true
        ));
        $this->addColumn('review', array(
            'header' => Mage::helper('customer')->__('Review'),
            'type' => 'text',
            'align' => 'left',
            'index' => 'review',
            'width' => '250px',
        ));
        $this->addColumn('rating', array(
            'header' => Mage::helper('customer')->__('Rating'),
            'type' => 'text',
            'align' => 'center',
            'index' => 'rating',
        ));
        $this->addColumn('customer_id', array(
            'header' => Mage::helper('customer')->__('Customer ID'),
            'type' => 'text',
            'align' => 'center',
            'index' => 'customer_id',
        ));
        $this->addColumn('product_id', array(
            'header' => Mage::helper('customer')->__('Product ID'),
            'type' => 'text',
            'align' => 'center',
            'index' => 'product_id',
        ));
        $this->addColumn('stylist_id', array(
            'header' => Mage::helper('customer')->__('Stylist ID'),
            'type' => 'text',
            'align' => 'center',
            'index' => 'stylist_id',
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('stylisthub')->__('Status'),
            'width' => '150px',
            'type' => 'options',
            'index' => 'status',
            'options' => Mage::getSingleton('stylisthub/status_reviewstatus')->getOptionArray()
        ));
        /**
         * Action to change the review status
         */
        $this->addColumn('action', array(
            'header' => Mage::helper('stylisthub')->__('Action'),
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('stylisthub')->__('Pending'),
                    'url' => array('base' => '*/*/pending'),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('stylisthub')->__('Approve'),
                    'url' => array('base' => '*/*/approve'),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('stylisthub')->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false
        ));
        return parent::_prepareColumns();
    }

    /**
     * Function for Mass action(approve or delete)
     *
     * Will change the review status of the stylist
     * return void
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('stylisthub');
        $this->getMassactionBlock()->addItem('disapprove', array(
            'label' => Mage::helper('customer')->__('Pending'),
            'url' => $this->getUrl('*/*/massPending')
        ));
        $this->getMassactionBlock()->addItem('approve', array(
            'label' => Mage::helper('stylisthub')->__('Approve'),
            'url' => $this->getUrl('*/*/massApprove')
        ));
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('stylisthub')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('stylisthub')->__('Are you sure?')
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
