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
class SMD_Stylisthub_Block_Adminhtml_Paymentinfo extends Mage_Adminhtml_Block_Widget_Grid_Container {

    /**
     * Construct the inital display of grid information
     * Setting the Block files group for this grid
     * Setting the Header text to display
     * Setting the Controller file for this grid
     *
     * Return payment info as array
     * @return array
     */
    public function __construct() {
        $stylist_id = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('stylisthub/stylistprofile')->load($stylist_id, 'stylist_id');
        $stylist_title = $collection['store_title'];
        $this->_controller = 'adminhtml_paymentinfo';
        $this->_blockGroup = 'stylisthub';
        $this->_headerText = Mage::helper('stylisthub')->__('Transaction History of ' . $stylist_title);
        $this->_addButton('button1', array(
            'label' => Mage::helper('stylisthub')->__('Back'),
            'onclick' => 'setLocation(\'' . $this->getUrl('stylisthubadmin/adminhtml_commission/index') . '\')',
            'class' => 'back',
        ));
        parent::__construct();
        $this->_removeButton('add');
    }

}
