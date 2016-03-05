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
 * This file contains stylist vacation mode functionality
 */
class SMD_Stylisthub_Block_Vacationmode extends Mage_Core_Block_Template {

    /**
     * Load vacation information by default in vacation form if stylist already stylist submit the vacation form
     *
     * Return the stylist vacation information
     * @return array
     */
    function loadVactionInfo() {
        $stylist = Mage::getSingleton('customer/session')->getCustomer();
        $stylistId = $stylist->getId();
        $loadStylist = Mage::getModel('stylisthub/vacationmode')->load($stylistId, 'stylist_id');
        return $loadStylist;
    }

}
