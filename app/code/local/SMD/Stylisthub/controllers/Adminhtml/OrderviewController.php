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
class SMD_Stylisthub_Adminhtml_OrderviewController extends Mage_Adminhtml_Controller_action {
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
     * Credit amount to stylist account
     *
     * @return void
     */
    public function creditAction() {
        $id = $this->getRequest()->getParam('id');
        if ($id > 0) {
            try {
                $model               = Mage::getModel('stylisthub/commission')->load($id);
                                       $model->setCredited('1')->save();
                $stylist_id           = $model->getStylistId();
                $admin_commission    = $model->getCommissionFee();
                $stylist_commission   = $model->getStylistAmount();
                $order_id            = $model->getOrderId();
                /**
				 * transaction collection
				 */
                $transaction         = Mage::getModel('stylisthub/transaction')->load($id, 'commission_id');
                $transaction_id      = $transaction->getId();
                if (empty($transaction_id)) {
                    $Data = array('commission_id' => $id, 'stylist_id' => $stylist_id, 'stylist_commission' => $stylist_commission, 'admin_commission' => $admin_commission, 'order_id' => $order_id);
                    Mage::getModel('stylisthub/transaction')->setData($Data)->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('stylisthub')->__('Amount was successfully credited'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }
    /**
     * Credit amount to multiple stylist account
     *
     * @return void
     */
    public function masscreditAction() {
        $stylisthub = $this->getRequest()->getPost('stylisthub');
        foreach ($stylisthub as $value) {
            Mage::helper('stylisthub/stylisthub')->updateCredit($value);
			 $model               = Mage::getModel('stylisthub/commission')->load($value);
            $stylist_id         = $model->getStylistId();
            $admin_commission  = $model->getCommissionFee();
            $stylist_commission = $model->getStylistAmount();
            $order_id          = $model->getOrderId();
            /**
			 * transaction collection
			 */
           $transaction        = Mage::helper('stylisthub/stylisthub')->getTransactionInfo($value);
            $transaction_id    = $transaction->getId();
            if (empty($transaction_id)) {
                $Data = array('commission_id' => $value, 'stylist_id' => $stylist_id, 'stylist_commission' => $stylist_commission, 'admin_commission' => $admin_commission, 'order_id' => $order_id);
                 Mage::helper('stylisthub/stylisthub')->saveTransactionData($Data);
            }
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('stylisthub')->__('Amount was successfully credited'));
        $this->_redirect('*/*/');
    }
}
