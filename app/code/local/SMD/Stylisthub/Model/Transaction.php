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
class SMD_Stylisthub_Model_Transaction extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('stylisthub/transaction');
    }
    /**
     * Function to change the commission status
     *
     * Passed the commission id of the stylist
     * @param int $commissionId
     *
     * @return void
     */
    public function changeStatus($commissionId)
    {
        if($commissionId!=''){
            $now = Mage::getModel('core/date')->date('Y-m-d H:i:s', time());
            $collection = Mage::getModel('stylisthub/transaction')->load($commissionId,'commission_id')
                    ->setReceivedStatus('1')
                    ->setAcknowledgeDate($now);
            $collection->save();
            return true;
        }
    }
    /**
     * Function to get the payment status from stylist
     *
     * Passed the commission id of the stylist
     * @param int $id
     *
     * @return void
     */

    public function getPaymentstatus($id){
        $collection = Mage::getModel('stylisthub/transaction')->load($id,'commission_id');
        return $collection;
    }
    /**
     * Function to get the payment comment from admin
     *
     * Passed the commission id of the stylist
     * @param int $id
     *
     * Return the transaction info of a stylist
     * @return array
     */

    public function getPaymentcomment($_id){
        $collection = Mage::getModel('stylisthub/transaction')->load($_id,'stylist_id');
        return $collection;
    }
}
