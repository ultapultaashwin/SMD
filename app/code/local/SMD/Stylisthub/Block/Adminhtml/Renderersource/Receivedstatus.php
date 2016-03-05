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
 * Renderer to get the payment received status os stylist
 */
class SMD_Stylisthub_Block_Adminhtml_Renderersource_Receivedstatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Function to render the received status of payment from stylist
     *
     * Return the payment received status
     * @return varchar
     */
    public function render(Varien_Object $row) {
        $id = $row->getData();
        foreach ($id as $_id) {
            $paymentStatus = Mage::getModel('stylisthub/transaction')->getPaymentstatus($_id);
            foreach ($paymentStatus as $_paymentStatus) {
            	if(isset($_paymentStatus['received_status'])){
               		$receivedStatus = $_paymentStatus['received_status'];
            	}
	             if ($receivedStatus == 1) {
	                    return 'Yes';
	                } else {
	                    return 'No';
	                }

            }
        }
    }

}
