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
 * Login page functionality
 */
class SMD_Stylisthub_Block_Form_Login extends Mage_Core_Block_Template {

    protected function _prepareLayout() {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('stylisthub')->__('Stylist Login'));
        return parent::_prepareLayout();
    }

    /**
     * Function to get login data post url
     *
     * Return the login data post url
     * @return string
     */
    public function getPostActionUrl() {
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $secure = strstr($currentUrl, "https");
        if ($secure == true) {
            return $this->getUrl('*/*/loginPost', array('_secure' => true));
        } else {
            return $this->getUrl('*/*/loginPost');
        }
    }

    /**
     * Function to get registration url
     *
     * Retun the registration url
     * @return string
     */
    public function getCreateAccountUrl() {
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $secure = strstr($currentUrl, "https");
        if ($secure == true) {
            return $this->getUrl('*/*/create', array('_secure' => true));
        } else {
            return $this->getUrl('*/*/create');
        }
    }

    /**
     * Function to get forget password url
     *
     * Return forgot password url
     * @return string
     */
    public function getForgotPasswordUrl() {
        return $this->helper('customer')->getForgotPasswordUrl();
    }

}
