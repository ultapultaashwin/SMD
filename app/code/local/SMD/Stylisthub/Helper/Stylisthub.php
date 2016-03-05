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
 * Function written in this file are globally accessed
 */
class SMD_Stylisthub_Helper_Stylisthub extends Mage_Core_Helper_Abstract {

    /**
     * Function to get new product url
     *
     * This Function will return the redirect url of new product form
     * @return string
     */
    public function getNewProductUrl() {
        return Mage::getUrl('stylisthub/product/new');
    }

    /**
     * Functionto get the manage product url
     *
     * This Function will return the redirect url of manage products
     * @return string
     */
    public function getManageProductUrl() {
        return Mage::getUrl('stylisthub/product/manage');
    }

    /**
     * Function to get the manage order url
     *
     * This Function will return the redirect url of manage orders
     * @return string
     */
    public function getManageOrderUrl() {
        return Mage::getUrl('stylisthub/order/manage');
    }

    /**
     * Function to get the add profile url
     *
     * This Function will return the redirect url of add profile
     * @return string
     */
    public function addprofileUrl() {
        return Mage::getUrl('stylisthub/stylist/addprofile');
    }

    /**
     * Function to get the become a merchant url
     *
     * This Function will return the redirect url to become a merchant
     * @return string
     */
    public function becomemerchantUrl() {
        return Mage::getUrl('stylisthub/stylist/changebuyer');
    }

    /**
     * Function to get link profile url
     *
     * Passed the stylist id in url to get the stylist store name
     * @param int $stylistId
     *
     * This Function will return the redirect url link to stylist profile
     * @return string
     */
    public function linkprofileUrl($stylistId) {
        return Mage::getUrl('stylisthub/stylist/displaystylist', array('id' => $stylistId));
    }

    /**
     * Function to get link product url
     *
     * Passed the stylist id in url to get the stylist store name
     * @param int $stylistId
     *
     * This Function will return the redirect url
     * @return string
     */
    public function linkproductUrl($stylistId) {
        return Mage::getUrl('stylisthub/stylist/stylistproduct', array('id' => $stylistId));
    }

    /**
     * Function to get stylist registration url
     *
     * This Function will return the redirect url to stylist registration
     * @return string
     */
    public function getregisterUrl() {
        return Mage::getUrl('stylisthub/stylist/create');
    }

    /**
     * Function to get stylist registration url and login url
     *
     * This Function will return the redirect url stylist registration and login
     * @return string
     */
    public function getregister() {
        return Mage::getUrl('stylisthub/stylist/login');
    }

    /**
     * Function to get the dashboard url
     *
     * This Function will return the redirect url to dashboard
     * @return string
     */
    public function dashboardUrl() {
        return Mage::getUrl('stylisthub/stylist/dashboard');
    }

    /**
     * Function to get all stylist information
     *
     * This Function will return the redirect url to view all stylist page
     * @return string
     */
    public function getviewallstylistUrl() {
        return Mage::getUrl('stylisthub/stylist/allstylist');
    }

    /**
     * Function to get view order url
     *
     * Passed the order id in url to get the order details
     * @param int $getOrderId
     *
     * Passed the product id in url to get the product details
     * @param int $getProductId
     *
     * This Function will return the redirect url to view order details
     * @return string
     */
    public function getVieworder($getOrderId,$getProductId) {
        return Mage::getUrl('stylisthub/order/vieworder', array('orderid' => $getOrderId,'productid' => $getProductId));
    }

    /**
     * Function to get view transaction url
     *
     * This Function will return the redirect url to view transaction
     * @return string
     */
    public function getViewtransaction() {
        return Mage::getUrl('stylisthub/order/viewtransaction');
    }

    /**
     * Function to get the received amount of stylist
     *
     * This funtion will return the Total amount received by the stylist from admin
     * @return int
     */
    public function getAmountReceived() {
        $return = '';
        $stylistId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $_collection = Mage::getModel('stylisthub/transaction')->getCollection()
                ->addFieldToSelect('stylist_commission')
                ->addFieldToFilter('stylist_id', $stylistId)
                ->addFieldToFilter('paid', 1);
        $_collection->getSelect()
                ->columns('SUM(	stylist_commission) AS stylist_commission')
                ->group('stylist_id');
        foreach ($_collection as $amount) {
            $return = $amount->getStylistCommission();
        }
        return Mage::helper('core')->currency($return, true, false);
    }

    /**
     * Function to get the remaining amount of stylist
     *
     * This funtion will return the Total remaining amount by admin to stylist
     * @return int
     */
    public function getAmountRemaining() {
        $return = '';
        $stylistId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $_collection = Mage::getModel('stylisthub/transaction')->getCollection()
                ->addFieldToSelect('stylist_commission')
                ->addFieldToFilter('stylist_id', $stylistId)
                ->addFieldToFilter('paid', 0);
        $_collection->getSelect()
                ->columns('SUM(	stylist_commission) AS stylist_commission')
                ->group('stylist_id');
        foreach ($_collection as $amount) {
            $return = $amount->getStylistCommission();
        }
        return Mage::helper('core')->currency($return, true, false);
    }

    /**
     * Function to get customer review url
     *
     * This Function will return the redirect url to customer review
     * @return string
     */
    public function customerreviewUrl() {
        return Mage::getUrl('stylisthub/stylist/customerreview');
    }

    /**
     * Function to get all review data
     *
     * Passed the stylist id in url to get all review
     * @param int $id
     *
     * This Function will return all reviews as array format for a particular stylist
     * @return array
     */
    function getallreviewdata($id) {
        $storeId = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('stylisthub/stylistreview')
                ->getCollection()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('stylist_id', $id);
        return $collection;
    }

    /**
     * Function to get order collection
     *
     * Filter the order collection by customer id
     * @param int $customerId
     *
     * This function will return only the order details of particular customer
     * @return array
     */
    function allowReview($customerId) {
        $orders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', $customerId)
                ->addAttributeToSort('created_at', 'DESC')
                ->setPageSize(5);
        return $orders;
    }

    /**
     * Function to get contact form url
     *
     * This function will return the contact form url
     * @return string
     */
    public function getContactFormUrl() {
        return Mage::getUrl('stylisthub/contact/form');
    }

    /**
     * Function to get the stylist rewrite url
     *
     * Passed the stylist id to rewrite the particular stylist url
     * @param int $stylistId
     *
     * This function will return the rewrited url for a particular stylist
     * @return string
     */
    public function getStylistRewriteUrl($stylistId) {
        $targetPath = 'stylisthub/stylist/displaystylist/id/' . $stylistId;
        $mainUrlRewrite = Mage::getModel('core/url_rewrite')->load($targetPath, 'target_path');
        $getRequestPath = $mainUrlRewrite->getRequestPath();
        $NewGetRequestPath = Mage::getUrl($getRequestPath);
        return $NewGetRequestPath;
    }

    /**
     * Function to load particular stylist information
     *
     * In this function stylist id is passed to get particular stylist data
     * @param int $_id
     *
     * This function will return the particular stylist information as array
     * @return array
     */
    public function getStylistCollection($_id) {
        $collection = Mage::getModel('stylisthub/stylistprofile')->load($_id, 'stylist_id');
        return $collection;
    }

    /**
     * Function to load particular category information
     *
     * Passed Category Id to get the category information
     * @param int $catId
     *
     * This function will return the Category information as array
     * @return array
     */
    public function getCategoryData($catId) {
        $collection = Mage::getModel('catalog/category')->load($catId);
        return $collection;
    }

    /**
     * Function to delete product
     *
     * Product entity id are passed to delete the product
     * @param int $entityIds
     *
     * This function will return true or false
     * @return bool
     */
    public function deleteProduct($entityIds) {
        Mage::getModel('catalog/product')->setId($entityIds)->delete();
        return true;
    }

    /**
     * Function to get product collection
     *
     * Product id is passed to get the particular product information
     * @param int $getProductId
     *
     * This function will display the particular product information as array
     * @return array
     */
    public function getProductInfo($getProductId) {
        $collection = Mage::getModel('catalog/product')->load($getProductId);
        return $collection;
    }

    /**
     * Function to get Commission data
     *
     * Commission Id is passed to get the particular commission id's data
     * @param int $commissionId
     *
     * This function will return the commission information as array
     * @return array
     */
    public function getCommissionInfo($commissionId) {
        $collection = Mage::getModel('stylisthub/commission')->load($commissionId, 'id');
        return $collection;
    }

    /**
     * Function to get Transaction data
     *
     * Commission id is passed to get the transaction details
     * @param int $commissionId
     *
     * This function will return the transaction details as array
     * @return array
     */
    public function getTransactionInfo($commissionId) {
        $collection = Mage::getModel('stylisthub/transaction')->load($commissionId, 'commission_id');
        return $collection;
    }

    /**
     * Function to save transaction data
     *
     * Transaction data is passed as array
     * @param array $data
     *
     * This function will return true or false
     * @return bool
     */
    public function saveTransactionData($data) {
        Mage::getModel('stylisthub/transaction')->setData($data)->save();
        return true;
    }

    /**
     * Function to save transaction data
     *
     * Transaction Id is passed to update the transaction information
     * @param int $transactionId
     *
     * Update the database table and will return void
     * @return void
     */
    public function updateTransactionData($transactionId) {
        $now = Mage::getModel('core/date')->date('Y-m-d H:i:s', time());
        if (!empty($transactionId)) {
            Mage::getModel('stylisthub/transaction')
                    ->setPaid(1)
                    ->setPaidDate($now)
                    ->setComment('Paypal Adaptive Payment')
                    ->setId($transactionId)->save();
        }
    }

    /**
     * Function to update commission data
     *
     * Passed the order status to update in database table
     * @param int $statusOrder
     *
     * Passed the commission id to update the data in database
     * @param int $commissionId
     *
     * This function will return true or false
     * @return bool
     */
    public function updateCommissionData($statusOrder, $commissionId) {
        Mage::getModel('stylisthub/commission')->setOrderStatus($statusOrder)->setId($commissionId)->save();
        return true;
    }

    /**
     * Function to save commission data
     *
     * Passed the commission data as array
     * @param array $data
     *
     * Passed the commission id to save the commission data
     * @param int $commissionId
     *
     * This function will return true or false
     * @return bool
     */
    public function saveCommissionData($data, $commissionId) {
        Mage::getModel('stylisthub/commission')->setData($data)->setId($commissionId)->save();
        return true;
    }

    /**
     * Function to load email template
     *
     * Passed the template id to load the email template
     * @param int $templateId
     *
     * This function will return the email template
     * @return string
     */
    public function loadEmailTemplate($templateId) {
        $emailTemplate = Mage::getModel('core/email_template')->load($templateId);
        return $emailTemplate;
    }

    /**
     * Function to load customer data
     *
     * Passed the selle id to load a particular stylist details
     * @param int $stylistId
     *
     * This function will return the stylist details as array
     * @return array
     */
    public function loadCustomerData($stylistId) {
        $customer = Mage::getModel('customer/customer')->load($stylistId);
        return $customer;
    }

    /**
     * Function to update comment from admin
     *
     * Passed the comment provided by admin before pay amount to stylist
     * @param int $comment
     *
     * Passed the transaction id to update the comment for that particular transaction
     * @param int $transactionId
     *
     * This function will return true or false
     * @return bool
     */
    public function updateComment($comment, $transactionId) {
        $now = Mage::getModel('core/date')->date('Y-m-d H:i:s', time());
        if (!empty($transactionId)) {
            Mage::getModel('stylisthub/transaction')
                    ->setPaid(1)
                    ->setPaidDate($now)
                    ->setComment($comment)
                    ->setId($transactionId)->save();
            return true;
        }
    }

    /**
     * Function to credit amount to stylist
     *
     * Passed the Commission Id to update the amount credited details
     * @param int $commissionId
     *
     * This function will return true or false
     * @return bool
     */
    public function updateCredit($commissionId) {
        $collection = Mage::getModel('stylisthub/commission')->load($commissionId, 'id');
        $collection->setCredited('1')->save();
        return true;
    }

    /**
     * Function to delete a stylist review
     *
     * stylist id is passed to delete the stylist review
     * @param int $stylisthubId
     *
     * This function will return true or false
     * @return bool
     */
    public function deleteReview($stylisthubId) {
        $model = Mage::getModel('stylisthub/stylistreview');
        $model->setId($stylisthubId)->delete();
        return true;
    }

    /**
     * Function to approve review
     *
     * stylist id is passed to approve the stylist review
     * @param int $stylisthubId
     *
     * This function will return true or false
     * @return bool
     */
    public function approveReview($stylisthubId) {
        $model = Mage::getModel('stylisthub/stylistreview')->load($stylisthubId);
        $model->setStatus('1')->save();
        return true;
    }

    /**
     * Function to delete a stylist account
     *
     * stylist id is passed to delete the stylist
     * @param int $stylisthubId
     *
     * This function will return true or false
     * @return bool
     */
    public function deleteStylist($stylisthubId) {
        $stylisthub = Mage::getModel('customer/customer')->load($stylisthubId);
        $stylisthub->delete();
        return true;
    }

    /**
     * Function to update approve stylist status
     *
     * Stylist id is passed to approve the stylist
     * @param int $stylisthubId
     *
     * This function will return true or false
     * @return bool
     */
    public function approveStylistStatus($stylisthubId) {
        $model = Mage::getModel('customer/customer')->load($stylisthubId);
        $model->setCustomerstatus('1')
                ->save();
        return true;
    }

    /**
     * Function to update disapprove stylist status
     *
     * Stylist id is passed to disapprove the stylist
     * @param int $stylisthubId
     *
     * This function will return true or false
     * @return bool
     */
    public function disapproveStylistStatus($stylisthubId) {
        $model = Mage::getModel('customer/customer')->load($stylisthubId);
        $model->setCustomerstatus('2')
                ->save();
        return true;
    }

    /**
     * Function to upload product image
     * @param int $filename
     * @param array $filesDataArray
     * @return string
     */
    public function uploadImage($filename, $filesDataArray) {
        $imagesPath = array();
        $uploader = new Varien_File_Uploader($filename);
        $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
        $uploader->addValidateCallback('catalog_product_image', Mage::helper('catalog/image'), 'validateUploadFile');
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);
        $path = Mage::getBaseDir('media') . DS . 'tmp' . DS . 'catalog' . DS . 'product' . DS;
        $uploader->save($path, $filesDataArray[$filename]['name']);
        $imagesPath = $path . $uploader->getUploadedFileName();

        return $imagesPath;
    }

    /**
     * Function to save image in media gallery
     *
     * Product images are passed as array
     * @param array $product
     *
     * This function will return true or false
     * @return bool
     */
    public function mediaGallery($product) {
        $product->save();
        return true;
    }

    /**
     * Function to disallow php files
     *
     * Uploaded file information are passed as array
     * @param array $uploader
     *
     * Temporary storage path is passed to store the uploaded file
     * @param string $tmpPath
     *
     * This function will return true or false
     * @return bool
     */
    public function disAllowUpload($uploader, $tmpPath) {
        $uploader->save($tmpPath);
        return true;
    }

    /**
     * Function to delete existing product custom option
     *
     * Custom option details will be send as array
     * @param array $opt
     *
     * This function will return true or false
     * @return bool
     */
    public function deleteCustomOption($opt) {
        $opt->delete();
        return true;
    }

    /**
     * Function to storing downloadable product link data
     *
     * Downloadable file data are passed as array
     * @param array $linkModel
     *
     * This function will return true or false
     * @return bool
     */
    public function saveDownLoadLink($linkModel) {
        $linkModel->save();
        return true;
    }

    /**
     * Function to set product instock
     *
     * Passed the Product is instock or not value
     * @param int $isInStock
     *
     * This function will return 0 or 1
     * @return bool
     */
    public function productInStock($isInStock) {
        if (isset($isInStock)) {
            return $stock_data['is_in_stock'] = $isInStock;
        } else {
            return $stock_data['is_in_stock'] = 1;
        }
    }

    /**
     * Function to get vacation mode url
     *
     * This Function will return the redirect url of vacation mode form
     * @return string
     */
    public function getVacationModeUrl(){
        return Mage::getUrl('stylisthub/stylist/vacationmode');
    }
     /**
     * Function to get vacation mode savae url
     *
     * This Function will return the redirect url of vacation mode save action
     * @return string
     */
    public function getVacationModeSaveUrl(){
        return Mage::getUrl('stylisthub/stylist/vacationmodesave');
    }

    /**
     * Function to get invoice order url
     *
     * Passed the order id in url to get the order details
     * @param int $orderId
     *
     * Passed the product id in url to get the product details
     * @param int $productId
     *
     * This Function will return the redirect url to view order details
     * @return string
     */
    public function getInvoiceUrl($orderId,$productId) {
        return Mage::getUrl('stylisthub/order/invoice', array('orderid' => $orderId,'productid' => $productId));
    }
     /**
     * Function to get manage deals url
     *
     * This Function will return the redirect url to view deals
     * @return string
     */
    public function getManageDealsUrl(){
        return Mage::getUrl('stylisthub/product/managedeals');
    }

    /**
     * Function to delete deal price and date for products
     *
     * Passed the entity id in url to get the product details
     * @param int $entityIds
     *
     * This Function will delete deal details
     * @return bool
     */
    public function deleteDeal($entityIds){
        Mage::getModel('catalog/product')->load($entityIds)->setSpecialFromDate('')->setSpecialToDate('')->setSpecialPrice('')->save();
        return true ;
    }
    /**
     * Function to get view all compare price products url
     *
     * This Function will return the redirect url of view all compare price products
     * @return string
     */
    public function getComparePriceUrl($productId) {
        return Mage::getUrl('stylisthub/product/comparestylistprice',array('id'=>$productId));
    }
    /**
     * Retrieve attribute id for stylist shipping
     *
     * This function will return the stylist shipping id
     * @return int
     */
	public  function getStylistShipping()
	{
            return Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'stylist_shipping_option');
	}

}
