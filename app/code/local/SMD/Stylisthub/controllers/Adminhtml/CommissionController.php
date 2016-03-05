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
 * This file is used for commission functionality from admin panel
 */
class SMD_Stylisthub_Adminhtml_CommissionController extends Mage_Adminhtml_Controller_action
{
    protected function _initAction() {
        $this->loadLayout()
        ->_setActiveMenu('stylisthub/items')
        ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
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
     * Load phtml edit action layout file
     *
     * @return void
     */
    public function editAction() {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('stylisthub/adminhtml_commission_edit'));
        $this->renderLayout();
    }
    /**
     * Paying stylist earned amount from a order
     *
     * @return void
     */
    public function payAction() {
        $id         = $this->getRequest()->getParam('id');
        $comment    = $this->getRequest()->getPost('detail');
        if ( $id > 0) {
            try {
                $transactions = Mage::getModel('stylisthub/transaction')->getCollection()
                                    ->addFieldToFilter('stylist_id', $id)
                                    ->addFieldToSelect('id')
                                    ->addFieldToFilter('paid',0);
                            foreach ($transactions as $transaction) {
                                $transactionId = $transaction->getId();
                                if (!empty($transactionId)) {
                                    Mage::helper('stylisthub/stylisthub')->updateComment($comment,$transactionId);
                                }
                            }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('stylisthub')->__('Payment successful'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
	}
	/**
	 * Load a phtml file for adding comments while paying money to stylist
	 *
	 * @return void
	 */
        public function addcommentAction() {
           $this->_initAction()
           ->renderLayout();
        }
}
