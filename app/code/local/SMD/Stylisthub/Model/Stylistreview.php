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
class SMD_Stylisthub_Model_Stylistreview extends Mage_Core_Model_Abstract
{
    public function _construct() {
        parent::_construct();
        $this->_init('stylisthub/stylistreview');
    }
    /**
     * Function to save a stylist review
     *
     * @return void
     */

    function saveReview($data) {
       $needAdmin    =  Mage::getStoreConfig('stylisthub/stylist_review/need_approval');
        if($data){
         $storeId    = Mage::app()->getStore()->getId();
         $collection = Mage::getModel('stylisthub/stylistreview');
                        $collection->setStylistId($data['stylist_id']);
                        $collection->setProductId($data['product_id']);
                        $collection->setCustomerId($data['customer_id']);
                        $collection->setRating($data['rating']);
                        $collection->setReview($data['feedback']);
                        $collection->setStoreId($storeId);
          if($needAdmin == 1){
             $collection->setStatus(0);
          } else {
             $collection->setStatus(1);
          }
        $collection->save();
        $templateId     = (int) Mage::getStoreConfig('stylisthub/stylist_review/admin_notify_review');
        $adminEmailId   = Mage::getStoreConfig('stylisthub/stylisthub/admin_email_id');
        $toMailId       = Mage::getStoreConfig("trans_email/ident_$adminEmailId/email");
        $toName         = Mage::getStoreConfig("trans_email/ident_$adminEmailId/name");
        if ($templateId) {
            $emailTemplate = Mage::getModel('core/email_template')->load($templateId);
        } else {
            $emailTemplate = Mage::getModel('core/email_template')
                            ->loadDefault('stylisthub_stylist_review_admin_notify_review');
        }
        $adminurl       = Mage::helper('adminhtml')->getUrl('stylisthubadmin/adminhtml_stylistreview/index');
        $customer       = Mage::getModel('customer/customer')->load($data['customer_id']);
        $cemail         = $customer->getEmail();
        $recipient      = $adminEmailId;
        $cname          = $customer->getName();
                            $emailTemplate->setSenderName(ucwords($cname));
                            $emailTemplate->setSenderEmail($cemail);
        $emailTemplateVariables = (array('ownername' => ucwords($toName), 'cname' => ucwords($cname), 'cemail' => $cemail, 'adminurl' => $adminurl));
                                    $emailTemplate->setDesignConfig(array('area' => 'frontend'));
        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                             $emailTemplate->send($toMailId, ucwords($toName), $emailTemplateVariables);
        return true;
        } else {
            return false;
        }
    }
    /**
     * Function to check customer already review for this product or not
     *
     * Passed the customer id as $customerId to get particular stylist reviews
     * @param int $customerId
     *
     * Passed the stylist id as $id to get particular stylist reviews
     * @param int $id
     *
     * Passed the product id as $productId to get particular stylist reviews
     * @param int $productId
     *
     * Return count of total reviews
     * @return int
     *
     */

    function checkReview($customerId,$id,$productId){
        $storeId      = Mage::app()->getStore()->getId();
        $coreResource = Mage::getSingleton('core/resource');
        $conn         = $coreResource->getConnection('core_read');
        $table        = $coreResource->getTableName('stylisthub/stylistreview');
	    $select       = $conn->select()
                        ->from(array('p' => $table ), new Zend_Db_Expr('stylist_review_id'))
                        ->where('stylist_id = ?',$id)
                        ->where('customer_id = ?', $customerId)
                        ->where('product_id = ?', $productId)
                        ->where('status = ?', 1)
                        ->where('store_id = ?', $storeId);
        $count = $conn->fetchOne($select);
        return $count;
    }
    /**
     * Function to display stylist recent review
     *
     * Passed the stylist id as $id to get particular stylist reviews
     * @param int $id
     *
     * Return  reviews collection as array
     * @return array
     */

    function displayReview($id){
        $storeId   = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('stylisthub/stylistreview')
                    ->getCollection()
                    ->addFieldToFilter('status',1)
                     ->addFieldToFilter('store_id',$storeId)
                    ->addFieldToFilter('stylist_id',$id)
                    ->setOrder('created_at','DESC')
                    ->setPageSize(5);

         return $collection;
    }
    /**
     * Function to get stylist store name
     *
     * Passed the stylist id as $id to get particular stylist information
     * @param int $id
     *
     * Return  stylist store name
     * @return string
     */

    function getStylistInfo($id){
        $collection   = Mage::getModel('stylisthub/stylistprofile')->getCollection()
                        ->addFieldToFilter('stylist_id',$id);
        foreach($collection as $data){
           return $data['store_title'];
        }
    }
    /**
     * Function to get stylist profile url of particular stylist
     *
     * Passed the stylist id as $id to get particular stylist profile url
     * @param int $id
     *
     * Return  stylist store url
     * @return string
     */

    function backUrl($id){
        $stylistData = Mage::getModel('stylisthub/stylistreview')->getStylistInfo($id);
        if ($stylistData) {
            $targetPath        = 'stylisthub/stylist/displaystylist/id/' . $id;
            $mainUrlRewrite     = Mage::getModel('core/url_rewrite')->load($targetPath, 'target_path');
            $getRequestPath     = $mainUrlRewrite->getRequestPath();
            $_getRequestPath    = Mage::getUrl($getRequestPath);
            return $_getRequestPath;
        }
    }
}
