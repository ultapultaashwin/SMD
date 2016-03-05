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
 * Form to get the comments from admin after transfer the payment to stylist
 */
class SMD_Stylisthub_Block_Adminhtml_Commission_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Get the comments from admin after transfer the payment to stylist
     *
     * @return void
     */
 protected function _prepareForm(){
    $stylist_id  = $this->getRequest()->getParam('id');
    $collection = Mage::getModel('stylisthub/transaction')            
                  ->load($stylist_id,'stylist_id');
    $this->setCollection($collection);
    $form       = new Varien_Data_Form(array(
                  'id'      => 'edit_form',
                  'action'  => $this->getUrl('*/*/pay', array('id' => $this->getRequest()->getParam('id'))),
                  'method'  => 'post',
                  'enctype' => 'multipart/form-data'
        )
     );
     $fieldset = $form->addFieldset('add_comment', array('legend' => Mage::helper('stylisthub')->__('Comments')));
     $fieldset->addField('detail', 'textarea', array(
            'name'      => 'detail',
            'title'     => Mage::helper('stylisthub')->__('Comments'),
            'label'     => Mage::helper('stylisthub')->__('Comments'),
            'style'     => 'height: 200px;',
            'required'  => true,
            'value'     => $collection['comment']
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
