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
 * Form to get the stylist commission from admin
 */
class SMD_Stylisthub_Block_Adminhtml_Managestylist_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * Get the get the stylist commission from admin
     *
     * @return void
     */
    protected function _prepareForm() {
        $stylist_id = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('stylisthub/stylistprofile')
                ->load($stylist_id, 'stylist_id');
        $this->setCollection($collection);
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/savecommission', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
                )
        );
        $fieldset = $form->addFieldset('set_commission', array('legend' => Mage::helper('stylisthub')->__('Stylist Commission')));
        $fieldset->addField('commission', 'text', array(
            'name' => 'commission',
            'title' => Mage::helper('stylisthub')->__('Stylist Commission(%)'),
            'label' => Mage::helper('stylisthub')->__('Stylist Commission(%)'),
            'required' => true,
            'class' => 'validate-number',
            'value' => $collection['commission']
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
