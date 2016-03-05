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
class SMD_Stylisthub_Model_Stylistprofile extends Mage_Core_Model_Abstract
{
    public function _construct() {
        parent::_construct();
        $this->_init('stylisthub/stylistprofile');
    }

    /**
     * Function to approve or disapprove stylist
     *
     * Passed the customer id of the stylist
     * @param int $customerId
     *
     * @return void
     */
    function adminApproval($customerId) {
        $templateId    = (int) Mage::getStoreConfig('stylisthub/admin_approval_stylist_registration/email_template_selection');
        $adminEmailId = Mage::getStoreConfig('stylisthub/stylisthub/admin_email_id');
        $toMailId       = Mage::getStoreConfig("trans_email/ident_$adminEmailId/email");
        $toName         = Mage::getStoreConfig("trans_email/ident_$adminEmailId/name");
        if ($templateId) {
            $emailTemplate = Mage::getModel('core/email_template')->load($templateId);
        } else {
            $emailTemplate = Mage::getModel('core/email_template')
                              ->loadDefault('stylisthub_admin_approval_stylist_registration_email_template_selection');
        }
        $adminurl               = Mage::helper('adminhtml')->getUrl('stylisthubadmin/adminhtml_managestylist/index');
        $customer               = Mage::getModel('customer/customer')->load($customerId);
        $cemail                 = $customer->getEmail();
        $recipient              = $admin_email_id;
        $cname                  = $customer->getName();
                                $emailTemplate->setSenderName(ucwords($cname));
                                $emailTemplate->setSenderEmail($cemail);
        $emailTemplateVariables = (array('ownername' => ucwords($toName), 'cname' => ucwords($cname), 'cemail' => $cemail, 'adminurl' => $adminurl));
                                $emailTemplate->setDesignConfig(array('area' => 'frontend'));
        $processedTemplate      = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                                $emailTemplate->send($toMailId, ucwords($toName), $emailTemplateVariables);
    }
    /**
     * function to approve or disapprove stylist
     *
     * Passed the customer id of the stylist
     * @param int $customerId
     *
     * @return void
     */

    function newStylist($customerId) {
        $templateId    = (int) Mage::getStoreConfig('stylisthub/admin_approval_stylist_registration/new_stylist_template');
        $adminEmailId = Mage::getStoreConfig('stylisthub/stylisthub/admin_email_id');
        $toMailId       = Mage::getStoreConfig("trans_email/ident_$adminEmailId/email");
        $toName         = Mage::getStoreConfig("trans_email/ident_$adminEmailId/name");
        if ($templateId) {
            $emailTemplate = Mage::getModel('core/email_template')->load($templateId);
        } else {
            $emailTemplate = Mage::getModel('core/email_template')
                              ->loadDefault('stylisthub_admin_approval_stylist_registration_new_stylist_template');
        }
        $adminurl               = Mage::helper('adminhtml')->getUrl('stylisthubadmin/adminhtml_managestylist/index');
        $customer               = Mage::getModel('customer/customer')->load($customerId);
        $cemail                 = $customer->getEmail();
        $recipient              = $adminEmailId;
        $cname                  = $customer->getName();
                                $emailTemplate->setSenderName(ucwords($cname));
                                $emailTemplate->setSenderEmail($cemail);
        $emailTemplateVariables = (array('ownername' => ucwords($toName), 'cname' => ucwords($cname), 'cemail' => $cemail, 'adminurl' => $adminurl));
                                $emailTemplate->setDesignConfig(array('area' => 'frontend'));
        $processedTemplate      = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                                $emailTemplate->send($toMailId, ucwords($toName), $emailTemplateVariables);
    }
    /**
     * Function to get stylist profile info
     *
     * Passed the customer id of the stylist
     * @param int $id
     *
     * Return stylist information as array
     * @return array
     */

    function collectprofile($id) {
        $collection = Mage::getModel('stylisthub/stylistprofile')->load($id, 'stylist_id');
        return $collection;
    }
    /**
     * Function to display new products
     *
     * Passed the stylist id of the stylist
     * @param int $stylistid
     *
     * Return new products as array
     * @return array
     */

    function newproduct($stylistid) {
        $storeId    = Mage::app()->getStore()->getId();
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $collection = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeId)
                    ->addAttributeToSelect('*')
                    ->addFieldToFilter('stylist_id', $stylistid)
                    ->addFieldToFilter('status', 1)
                    ->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $todayDate))
                    ->addAttributeToFilter('news_to_date', array('or' => array(
                            0 => array('date' => true, 'from' => $todayDate),
                            1 => array('is' => new Zend_Db_Expr('null')))
                            ), 'left')
                    ->addAttributeToSort('entity_id', 'DESC')
                    ->setPage(1, 5);
        return $collection;
    }
    /**
     * Function to display popular products
     *
     * Passed the stylist id of the stylist
     * @param int $stylistid
     *
     * Return popular products as array
     * @return array
     */

    function popularproduct($stylistid) {
        $productCollection = Mage::getResourceModel('reports/product_collection')
                            ->addOrderedQty()
                            ->addFieldToFilter('stylist_id', $stylistid)
                            ->addAttributeToSelect('*')
                            ->addAttributeToFilter('status', array('eq' => 1))
                            ->setPage(1, 5);
        return $productCollection;
    }
    /**
     * Function for url management
     *
     * Passed the stylist id of the stylist
     * @param int $stylistId
     *
     * Passed the stylist store name to create url
     * @param int $storeName
     *
     * Return popular products as array
     * @return array
     */

    function addRewriteUrl($storeName, $stylistId) {
        $trimStr            = trim(preg_replace('/[^a-z0-9-]+/', '-', strtolower($storeName)), '-');
        $mainUrlRewrite     = Mage::getModel('core/url_rewrite')->load($trimStr, 'request_path');
        $getUrlRewriteId    = $mainUrlRewrite->getUrlRewriteId();
        if ($getUrlRewriteId) {
            $requestPath = $trimStr . '-' . $stylistId;
        } else {
            $requestPath = $trimStr;
        }
        Mage::getModel('core/url_rewrite')
                ->setIsSystem(0)
                ->setIdPath('stylist/' . $stylistId)
                ->setTargetPath('stylisthub/stylist/displaystylist/id/' . $stylistId)
                ->setRequestPath($requestPath)
                ->save();
    }
    /**
     * Function to get stylist product info
     *
     * Passed the stylist id
     * @param int $id
     *
     * Return stylist product information as array
     * @return array
     */

   function stylistProduct($id){
        $collection = Mage::getModel('stylisthub/commission')->getCollection()
                        ->addFieldToFilter('order_status','complete')
                        ->addFieldToFilter('stylist_id',$id);
         return $collection;
    }
    /**
     * Function to get stylist product order info
     *
     * Passed the stylist id
     * @param int $id
     *
     * Return stylist product order information as array
     * @return array
     */

    function getdataProduct($orderIds){
       $items = Mage::getModel("sales/order_item")->getCollection()
               ->addFieldToSelect('product_id')
               ->addFieldToSelect('order_id')
               ->addFieldToSelect('name')
               ->addFieldToSelect('qty_invoiced')
               ->addFieldToSelect('base_price')
               ->addAttributeToSort('order_id', 'DESC')
                ->addFieldToFilter("order_id", array("in" => $orderIds));
       $items->getSelect()->join( array('t2'=> Mage::getConfig()->getTablePrefix().'sales_flat_order'), 'main_table.order_id = t2.entity_id', array('increment_id' => 't2.increment_id'));
       $items->getSelect()->limit(5);
       return $items;
    }
    /**
     * Function to display top stylist
     *
     * Passed the stylist id
     * @param int $id
     *
     * Return stylist information as array
     * @return array
     */

    function topStylist($id){
        $collection = Mage::getModel('stylisthub/stylistprofile')->getCollection()->addFieldToFilter('stylist_id',array($id));                          
        return $collection;
    }
}
