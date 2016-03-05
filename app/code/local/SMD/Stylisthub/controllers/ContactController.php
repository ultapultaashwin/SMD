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
 * This file is used for contact admin functionality
 */
class SMD_Stylisthub_ContactController extends Mage_Core_Controller_Front_Action {
   /**
     * Load phtml file layout
     *
     * @return void
     */
    public function indexAction() {

        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
            $this->_redirect('*/*/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * Load contact admin form
     *
     * @return void
     */
    public function formAction() {
        /**
         *  Initilize customer and stylist group id
         */
        $customerGroupId = $stylistGroupId = $customerStatus = '';
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $stylistGroupId = Mage::helper('stylisthub')->getGroupId();
        $customerStatus = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();
        if (!$this->_getSession()->isLoggedIn() && $customerGroupId != $stylistGroupId) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        /**
         * Checking whether customer approved or not
         */
        if ($customerStatus != 1) {
            Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirms your Stylist Account'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     *  Send email to admin
     *
     *  @return void
     */
    public function postAction() {
        /**
         *  Initilize customer and stylist group id
         */
         $customerGroupId = $stylistGroupId = $customerStatus = '';
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $stylistGroupId = Mage::helper('stylisthub')->getGroupId();
        $customerStatus = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();
        if (!$this->_getSession()->isLoggedIn() && $customerGroupId != $stylistGroupId) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        /**
         *  Checking whether customer approved or not
         */
        if ($customerStatus != 1) {
            Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirms your Stylist Account'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        if (Mage::getStoreConfig('stylisthub/admin_approval_stylist_registration/contact_admin') == 1) {
            $subject = $message = '';
            $subject = $this->getRequest()->getPost('subject');
            $message = $this->getRequest()->getPost('message');
            if (!empty($subject) && !empty($message)) {
                /**
                 *  Sending email to admin
                 */
                try {
                    $templateId = (int) Mage::getStoreConfig('stylisthub/admin_approval_stylist_registration/contact_email_template_selection');
                    $adminEmailId = Mage::getStoreConfig('stylisthub/stylisthub/admin_email_id');
                    $toMailId = Mage::getStoreConfig("trans_email/ident_$adminEmailId/email");
                    $toName = Mage::getStoreConfig("trans_email/ident_$adminEmailId/name");
                    /**
                     *  Selecting template id
                     */
                    if ($templateId) {
                        $emailTemplate = Mage::getModel('core/email_template')->load($templateId);
                    } else {
                        $emailTemplate = Mage::getModel('core/email_template')
                                ->loadDefault('stylisthub_admin_approval_stylist_registration_contact_email_template_selection');
                    }
                    $stylistId = Mage::getSingleton('customer/session')->getId();
                    $customer = Mage::getModel('customer/customer')->load($stylistId);
                    $stylist_info = Mage::getModel('stylisthub/stylistprofile')->load($stylistId,'stylist_id');
                    $stylistemail = $customer->getEmail();
                    $recipient = $toMailId;
                    $stylistname = $customer->getName();
                    $contactno = $stylist_info['contact'];
                    $emailTemplate->setSenderName($stylistname);
                    $emailTemplate->setSenderEmail($stylistemail);
                    $emailTemplateVariables = (array('ownername' => $toName, 'stylistname' => $stylistname, 'stylistemail' => $stylistemail, 'subject' => $subject, 'message' => $message, 'contactno' => $contactno ));
                    $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                    $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                    $emailTemplate->send($recipient, $stylistname, $emailTemplateVariables);
                    Mage::getSingleton('core/session')->addSuccess($this->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                    $this->_redirect('*/*/form');
                } catch (Mage_Core_Exception $e) {
                    /**
                     *  Error message redirect to create new product page
                     */
                    Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                    $this->_redirect('*/*/form');;
                } catch (Exception $e) {
                    /**
                     * Error message redirect to create new product page
                     */
                    Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                     $this->_redirect('*/*/form');
                }
            } else {
                Mage::getSingleton('core/session')->addError($this->__('Please enter all required fields'));
                 $this->_redirect('*/*/form');
            }
        }
    }

    /**
     * Getting customer
     *
     * @return string
     */

    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }
}
