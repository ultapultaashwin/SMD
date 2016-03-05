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
 * This file is used to manage order information
 */
class SMD_Stylisthub_OrderController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }
    /**
     * Load phtml layout file to display order information
     *
     * @return void
     */
    public function indexAction() {
        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Manage orders by stylists
     *
     * @return void
     */
    public function manageAction() {

        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
     /**
      * View full order information by stylist
      *
      * @return void
      */
     function vieworderAction(){

        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
          $this->loadLayout();
          $this->renderLayout();
      }
     /**
      * View full transaction history by stylist
      *
      * @return void
      */
      function viewtransactionAction(){

        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
          $this->loadLayout();
          $this->renderLayout();
      }
       /**
        * Stylist payment acknowledgement
        *
        * @return void
        */
      function acknowledgeAction(){

        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
          $this->loadLayout();
          $this->renderLayout();
          $commissionId = $this->getRequest()->getParam('commissionid');
          if($commissionId!=''){
          $collection = Mage::getModel('stylisthub/transaction')->changeStatus($commissionId);
          if($collection==1){
              Mage::getSingleton('core/session')->addSuccess($this->__("Payment received status has been updated"));
              $this->_redirect('stylisthub/order/viewtransaction');
          } else  {
             Mage::getSingleton('core/session')->addError($this->__('Payment received status was not updated'));
             $this->_redirect('stylisthub/order/viewtransaction');
          }
      }
   }

}
