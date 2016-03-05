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
 * This file is used to maintain stylist review details
 */
class SMD_Stylisthub_Adminhtml_StylistreviewController extends Mage_Adminhtml_Controller_action
{

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('stylisthub/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Stylist Review'));
        return $this;
    }
    /**
     * Load phtml file layout
     *
     * @return void
     */
    public function indexAction() {
        $this->_initAction()
        ->renderLayout();
    }
    /**
     * Delete multiple reviews
     *
     * @return void
     */
    public function massDeleteAction() {
        $stylisthubIds = $this->getRequest()->getParam('stylisthub');
        if(!is_array($stylisthubIds)) {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select at least one review'));
        } else {
            try {
                foreach ($stylisthubIds as $stylisthubId) {
                     Mage::helper('stylisthub/stylisthub')->deleteReview($stylisthubId);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($stylisthubIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * Approve customer reviews for stylists
     *
     * @return void
     */
    public function approveAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            $id = $this->getRequest()->getParam('id');
                 try {
                         $model      = $collection = Mage::getModel('stylisthub/stylistreview')->load($this->getRequest()->getParam('id'));
                                        $model->setStatus('1')
                                        ->save();
                     $customeId      = $model->getCustomerId();
                     $stylistId       = $model->getStylistId();
                     /**
                      * send email
                      */
                     $template_id    = (int)Mage::getStoreConfig('stylisthub/admin_approval_stylist_registration/stylist_email_template_selection');
                     $admin_email_id = Mage::getStoreConfig('stylisthub/stylisthub/admin_email_id');
                     $toMailId       = Mage::getStoreConfig("trans_email/ident_$admin_email_id/email");
                     $toName         = Mage::getStoreConfig("trans_email/ident_$admin_email_id/name");
                      if ($template_id) {
                        $emailTemplate = Mage::getModel('core/email_template')->load($template_id);
                      } else {
                        $emailTemplate = Mage::getModel('core/email_template')
                                          ->loadDefault('stylisthub_admin_approval_stylist_registration_stylist_email_template_selection');
                      }
                      /**
                       * Get customer data
                       */
                      $customer         = Mage::getModel('customer/customer')->load($customeId);
                      $recipient        = $customer->getEmail();
                      $cname            = $customer->getName();
                                            $emailTemplate->setSenderName(ucwords($toName));
                                            $emailTemplate->setSenderEmail($toMailId);
                      $emailTemplateVariables = (array('ownername'=>ucwords($toName),'cname'=>ucwords($cname)));
                                            $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                      $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                                            $emailTemplate->send($recipient,ucwords($cname),$emailTemplateVariables);
                      /**
                       * Get Stylist data
                       */
                      $stylist_data       = Mage::getModel('customer/customer')->load($stylistId);
                      $recipient_stylist  = $stylist_data->getEmail();
                      $cname_stylist      = $stylist_data->getName();
                      $emailTemplateVariables = (array('ownername'=>ucwords($toName),'cname'=>ucwords($cname_stylist)));
                      $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                                            $emailTemplate->send($recipient_stylist,ucwords($cname_stylist),$emailTemplateVariables);
                      /**
                       * end email
                       */
                         Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('stylisthub')->__('Review approved successfully.'));
                         $this->_redirect('*/*/');
                 } catch (Exception $e) {
                         Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                         $this->_redirect('*/*/');
                 }
         }
         $this->_redirect('*/*/');
 }
 /**
  * Status as Pending once customer posted the reviews for stylists
  *
  * @return void
  */
  public function pendingAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            $id = $this->getRequest()->getParam('id');
                 try {
                         $model = Mage::getModel('stylisthub/stylistreview')->load($this->getRequest()->getParam('id'));
                         $model->setStatus('0')
                         ->save();
                         Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('stylisthub')->__('Review is Pending.'));
                         $this->_redirect('*/*/');
                 } catch (Exception $e) {
                         Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                         $this->_redirect('*/*/');
                 }
         }
         $this->_redirect('*/*/');
 }
 /**
  * Delete reviews
  *
  * @return void
  */
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
                try {
                        /**
                         *  Reset group id
                         */
                         $model = Mage::getModel('stylisthub/stylistreview');
                         $model->setId($this->getRequest()->getParam('id'))->delete();
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Review successfully deleted'));
                        $this->_redirect('*/*/');
                } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                }
        }
        $this->_redirect('*/*/');
    }
    /**
     * Approve multiple customer reviews for stylists
     *
     * @return void
     */
   public function massApproveAction() {
        $stylisthubIds = $this->getRequest()->getParam('stylisthub');
        if(!is_array($stylisthubIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select at least one review'));
        } else {
            try {
                foreach ($stylisthubIds as $stylisthubId) {
                     $model = Mage::helper('stylisthub/stylisthub')->approveReview($stylisthubId);
                     $customeId = $model->getCustomerId();
                     $stylistId = $model->getStylistId();
                     /**
                      * send email
                      */
                            $template_id    = (int)Mage::getStoreConfig('stylisthub/admin_approval_stylist_registration/stylist_email_template_selection');
                            $admin_email_id = Mage::getStoreConfig('stylisthub/stylisthub/admin_email_id');
                            $toMailId       = Mage::getStoreConfig("trans_email/ident_$admin_email_id/email");
                            $toName         = Mage::getStoreConfig("trans_email/ident_$admin_email_id/name");
                             if ($template_id) {
                               $emailTemplate = Mage::helper('stylisthub/stylisthub')->loadEmailTemplate($template_id);
                             } else {
                               $emailTemplate = Mage::getModel('core/email_template')
                                                 ->loadDefault('stylisthub_admin_approval_stylist_registration_stylist_email_template_selection');
                             }
                            /**
                       * Get customer data
                       */
                             $customer = Mage::helper('stylisthub/stylisthub')->loadCustomerData($customeId);
                             $recipient = $customer->getEmail();
                             $cname = $customer->getName();
                             $emailTemplate->setSenderName(ucwords($toName));
                             $emailTemplate->setSenderEmail($toMailId);
                             $emailTemplateVariables = (array('ownername'=>ucwords($toName),'cname'=>ucwords($cname)));
                             $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                             $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                             $emailTemplate->send($recipient,ucwords($cname),$emailTemplateVariables);

                             /**
                       * Get Stylist data
                       */
                             $stylist_data = Mage::helper('stylisthub/stylisthub')->loadCustomerData($stylistId);
                             $recipient_stylist = $stylist_data->getEmail();
                             $cname_stylist = $stylist_data->getName();
                             $emailTemplateVariables = (array('ownername'=>ucwords($toName),'cname'=>ucwords($cname_stylist)));
                             $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                             $emailTemplate->send($recipient_stylist,ucwords($cname_stylist),$emailTemplateVariables);

                           /**
                       * end email
                       */
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'A total of %d record(s) is successfully approved', count($stylisthubIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * change status to pending for multiple customer reviews
     *
     * @return void
     */
    public function massPendingAction() {
        $stylisthubIds = $this->getRequest()->getParam('stylisthub');
        if(!is_array($stylisthubIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select at least one review'));
        } else {
            try {
                foreach ($stylisthubIds as $stylisthubId) {
                    Mage::helper('stylisthub/stylisthub')->approveReview($stylisthubId);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'A total of %d record(s) is pending', count($stylisthubIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
