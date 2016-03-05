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
 * Renderer to get the payment mode details of stylist
 */
class SMD_Stylisthub_Block_Adminhtml_Renderersource_Paymentmode extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Function to render payment mode details of stylist
     *
     * Return the stylist payment mode
     * @return varchar
     */
    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        $collection = Mage::getModel('stylisthub/stylistprofile')->getCollection()
                ->addFieldToFilter('stylist_id', $value)
                ->addFieldToSelect('bank_payment')
                ->addFieldToSelect('paypal_id');
        foreach ($collection as $_collection) {
            $bankPayment = $_collection->getBankPayment();
            $paypalId = $_collection->getPaypalId();
            if ($bankPayment && $paypalId) {
                $return = 'Bank Payment:' . $bankPayment . 'Paypal Id:' . $paypalId;
                return $return;
            } elseif ($paypalId) {
                $return = 'Paypal Id:' . $paypalId;
                return $return;
            } elseif ($bankPayment) {
                $return = 'Bank Payment:' . $bankPayment;
                return $return;
            }
        }
    }

}
