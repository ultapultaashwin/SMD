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
class SMD_Stylisthub_Block_Linkstylist extends Mage_Core_Block_Template
{
	/*
	 * Function to get the stylist profile data
	 *
	 * Passed the stylist id as $stylistId to get particular stylist info 
     * @param int $stylistId
     *
     * Return store title of the stylist as $StoreTitle
     * @return varchar
	 */
    function stylistdisplay($stylistId)
    {
       $collection   = Mage::getModel('stylisthub/stylistprofile')->load($stylistId,'stylist_id');
       return  $collection;
    }
    /*
     * Function to get show profile information
    *
    * Passed the stylist id as $stylistId to get particular stylist info
    * @param int $stylistId
    *
    * Return store profile of the stylist as $StoreProfile
    * @return int
    */

    function stylistprofiledisplay($stylistId)
    {
       $collection    = Mage::getModel('stylisthub/stylistprofile')->load($stylistId,'stylist_id');
       $StoreProfile = $collection->getShowProfile();
       return  $StoreProfile;
    }
}
