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
 * Renderer to change the payment status
 */
class SMD_Stylisthub_Block_Adminhtml_Renderersource_Payment extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Function to pay amount from admin to stylist and changed the payment status
     *
     * Return the payment status as 'Paid' or 'Pay' or 'NIL'
     * @return string
     */
    public function render(Varien_Object $row) {
        $id = $row->getData();
        foreach ($id as $_id) {
            $collection = Mage::getModel('stylisthub/transaction')->getCollection()
                    ->addFieldToSelect('stylist_commission')
                    ->addFieldToFilter('stylist_id', $_id)
                    ->addFieldToFilter('paid', 0);
            $collection->getSelect()
                    ->columns('SUM(	stylist_commission) AS stylist_commission')
                    ->group('stylist_id');
            foreach ($collection as $amount) {
                $total = $amount->getStylistCommission();
            }
            $collectionPaid = Mage::getModel('stylisthub/transaction')->getCollection()
                    ->addFieldToSelect('stylist_commission')
                    ->addFieldToFilter('stylist_id', $id)
                    ->addFieldToFilter('paid', 1);
            $collectionPaid->getSelect()
                    ->columns('SUM(	stylist_commission) AS stylist_commission')
                    ->group('stylist_id');
            foreach ($collectionPaid as $amountPaid) {
                $totalPaid = $amountPaid->getStylistCommission();
            }
            if (empty($total) && !empty($totalPaid)) {
                $result = Mage::helper('stylisthub')->__('Paid');
            } elseif (empty($total) && empty($totalPaid)) {
                $result = Mage::helper('stylisthub')->__('NIL');
            } else {
                $result = "<a href='" . $this->getUrl('*/*/addcomment/', array('id' => $_id)) . "' title='" . Mage::helper('stylisthub')->__('click to Pay') . "'>" . Mage::helper('stylisthub')->__('Pay') . "</a>";
            }
            return $result;
        }
    }

}
