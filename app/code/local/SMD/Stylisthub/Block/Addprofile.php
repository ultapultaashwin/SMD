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
 * This file contains add stylist profile page functionality
 */
class SMD_Stylisthub_Block_Addprofile extends Mage_Core_Block_Template {

    /**
     * Function to get save profile url
     *
     * Return the save profile action url
     * @return string
     */
    function addprofile() {
        return Mage::getUrl('stylisthub/stylist/saveprofile');
    }

    /**
     * Function to display the profile info for edit option
     *
     * Passed stylist id to get stylist information
     * @param int @stylistId
     *
     * Return stylist data as array
     * @return array
     */
    function editprofile($stylistId) {
        $collection = Mage::getModel('stylisthub/stylistprofile')->getCollection()
                ->addFieldToFilter('stylist_id', $stylistId);
        foreach ($collection as $data) {
            return $data;
        }
    }

}
