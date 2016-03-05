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
 * Event Observer
 */
class SMD_Stylisthub_Model_Observer {

    /**
     * Order saved successfully then commisssion information will be saved in database and email notification
     * will be sent to stylist
     *
     * Order information will be get from the $observer parameter
     * @param array $observer
     *
     * @return void
     */
    public function successAfter($observer) {
        $orderIds = $observer->getEvent()->getOrderIds();
        $order = Mage::getModel('sales/order')->load($orderIds[0]);
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $getCustomerId = $customer->getId();
        $grandTotal = $order->getGrandTotal();
        $status = $order->getStatus();
        /**
         *  getting Product information to update commission details
         */
        $items = $order->getAllItems();
        $orderEmailData = array();
        foreach ($items as $item) {
            $getProductId = $item->getProductId();
            $createdAt = $item->getCreatedAt();
            $paymentMethodCode = $order->getPayment()->getMethodInstance()->getCode();
            $products = Mage::helper('stylisthub/stylisthub')->getProductInfo($getProductId);
            $stylistId = $products->getStylistId();
            $stylistShippingEnabled = Mage::getStoreConfig('carriers/smd/active');
            if($stylistShippingEnabled == 1){
            	$nationalShippingPrice = $products->getNationalShippingPrice();
            	$internationalShippingPrice = $products->getInternationalShippingPrice();
            	$stylistDefaultCountry = $products->getDefaultCountry();
            	$shippingCountryId = $order->getShippingAddress()->getCountry();
            }
            if ($stylistId) {
                if ($paymentMethodCode == 'paypaladaptive') {
                    $credited = 1;
                    $orderPrice = $item->getPrice() * $item->getQtyOrdered();
                    $productAmt = $item->getPrice();
                    $productQty = $item->getQtyOrdered();
                 if($stylistDefaultCountry == $shippingCountryId){
                    	$shippingPrice = $orderPrice + ($nationalShippingPrice * $productQty);
                    } else {
                    	$shippingPrice = $orderPrice + ($internationalShippingPrice* $productQty);
                    }
                } else {
                    $credited = 0;
                   $orderPrice = $item->getPrice() * $item->getQtyOrdered();
                   $productAmt = $item->getPrice();
                   $productQty = $item->getQtyOrdered();
                    if($stylistDefaultCountry == $shippingCountryId){
                    	$shippingPrice = $orderPrice + ($nationalShippingPrice * $productQty);
                    } else {
                    	$shippingPrice = $orderPrice + ($internationalShippingPrice* $productQty);
                    }
                }
                /**
                 * Getting stylist commission percent
                 */
                $stylistCollection = Mage::helper('stylisthub/stylisthub')->getStylistCollection($stylistId);
                $percentperproduct = $stylistCollection['commission'];
                $commissionFee = $orderPrice * ($percentperproduct / 100);
                $stylistAmount = $shippingPrice - $commissionFee;

                /**
                 *  Storing commission information in database table
                 */
                $commissionData = array('stylist_id' => $stylistId, 'product_id' => $getProductId, 'product_qty' => $productQty, 'product_amt' => $productAmt, 'commission_fee' => $commissionFee, 'stylist_amount' => $stylistAmount, 'order_id' => $order->getId(), 'increment_id' => $order->getIncrementId(), 'order_total' => $grandTotal, 'order_status' => $status, 'credited' => $credited, 'customer_id' => $getCustomerId, 'status' => 1, 'created_at' => $createdAt, 'payment_method' => $paymentMethodCode);
                $commissionId = $this->storeCommissionData($commissionData);
                $orderEmailData[$itemCount]['stylist_id'] = $stylistId;
                $orderEmailData[$itemCount]['product_id'] = $getProductId;
                $orderEmailData[$itemCount]['product_qty'] = $productQty;
                $orderEmailData[$itemCount]['product_amt'] = $productAmt;
                $orderEmailData[$itemCount]['commission_fee'] = $commissionFee;
                $orderEmailData[$itemCount]['stylist_amount'] = $stylistAmount;
                $orderEmailData[$itemCount]['increment_id'] = $order->getIncrementId();
                $orderEmailData[$itemCount]['customer_email'] = $order->getCustomerEmail();
                $orderEmailData[$itemCount]['customer_firstname'] = $order->getCustomerFirstname();
                $itemCount = $itemCount + 1;
            }
            if ($paymentMethodCode == 'paypaladaptive') {
                /**
                 * If payment method is paypal adaptive, then commission table(credited to stylist) and transaction table(amout paid to stylist) will be updated
                 */
                $model = Mage::helper('stylisthub/stylisthub')->getCommissionInfo($commissionId);
                $stylistId = $model->getStylistId();
                $adminCommission = $model->getCommissionFee();
                $stylistCommission = $model->getStylistAmount();
                $orderId = $model->getOrderId();
                $commissionId = $model->getId();
                /**
                 * transaction collection to update the payment information
                 */
                $transaction = Mage::helper('stylisthub/stylisthub')->getTransactionInfo($commissionId);
                $transactionId = $transaction->getId();
                if (empty($transactionId)) {
                    $Data = array('commission_id' => $commissionId, 'stylist_id' => $stylistId, 'stylist_commission' => $stylistCommission, 'admin_commission' => $adminCommission, 'order_id' => $orderId, 'received_status' => 0);
                    Mage::helper('stylisthub/stylisthub')->saveTransactionData($Data);
                }
                /**
                 * Update the database after admin paid stylist amount
                 */
                $transactions = Mage::getModel('stylisthub/transaction')->getCollection()
                        ->addFieldToFilter('stylist_id', $stylistId)
                        ->addFieldToSelect('id')
                        ->addFieldToFilter('paid', 0);
                foreach ($transactions as $transaction) {
                    $transactionId = $transaction->getId();
                    if (!empty($transactionId)) {
                        Mage::helper('stylisthub/stylisthub')->updateTransactionData($transactionId);
                    }
                }
            }
        }

        if (Mage::getStoreConfig('stylisthub/admin_approval_stylist_registration/sales_notification') == 1) {
            $this->sendOrderEmail($orderEmailData);
        }
    }

    /**
     * Save stylist commission data in database and get the commission id
     *
     * Commission information passed to update in database
     * @param array $commissionData
     *
     * This function will return the commission id of the last saved data
     * @return int
     */
    public function storeCommissionData($commissionData) {
        $model = Mage::getModel('stylisthub/commission');
        $model->setData($commissionData);
        $model->save();
        $commissionId = $model->getId();
        return $commissionId;
    }

    /**
     * If Order status changed successfully then commisssion information will be saved in database and email notification
     * will be sent to stylist
     *
     * @return void
     */
    public function salesOrderAfter() {
        $orderId = (int) Mage::app()->getRequest()->getParam('order_id');
        if ($orderId) {
            $orders = Mage::getModel('sales/order')->load($orderId);
            $statusOrder = $orders->getStatus();
            $commissions = Mage::getModel('stylisthub/commission')->getCollection()
                    ->addFieldToFilter('order_id', $orderId)
                    ->addFieldToSelect('id');
            $count = count($commissions);
            if ($count > 0) {
                foreach ($commissions as $commission) {
                    $commissionId = $commission->getId();
                    if (!empty($commissionId)) {
                        Mage::helper('stylisthub/stylisthub')->updateCommissionData($statusOrder, $commissionId);
                    }
                }
            } else {
                $order = Mage::getModel('sales/order')->load($orderId);
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $getCustomerId = $customer->getId();
                $grandTotal = $order->getGrandTotal();
                $status = $order->getStatus();
                /**
                 * get Product details to update commission information
                 */
                $items = $order->getAllItems();
                foreach ($items as $item) {
                    $getProductId = $item->getProductId();
                    $createdAt = $item->getCreatedAt();
                    $paymentMethodCode = $order->getPayment()->getMethodInstance()->getCode();
                    $products = Mage::helper('stylisthub/stylisthub')->getProductInfo($getProductId);
                    $stylistShippingEnabled = Mage::getStoreConfig('carriers/smd/active');
                    if($stylistShippingEnabled == 1){
                    	$nationalShippingPrice = $products->getNationalShippingPrice();
                    	$internationalShippingPrice = $products->getInternationalShippingPrice();
                    	$stylistDefaultCountry = $products->getDefaultCountry();
                    	$shippingCountryId = $orders->getShippingAddress()->getCountry();

                    }
                    $stylistId = $products->getStylistId();
                    if ($stylistId) {
                        $credited = 1;
                        $orderPrice = $item->getPrice() * $item->getQtyOrdered();
                        $productAmt = $item->getPrice();
                        $productQty = $item->getQtyOrdered();
                    if($stylistDefaultCountry == $shippingCountryId){
                    	$shippingPrice = $orderPrice + ($nationalShippingPrice * $productQty);
                    } else {
                    	$shippingPrice = $orderPrice + ($internationalShippingPrice* $productQty);
                    }
                    }
                    /**
                     * Get stylist commission percent
                     */
                    $stylistCollection = Mage::helper('stylisthub/stylisthub')->getStylistCollection($stylistId);
                    $percentPerProduct = $stylistCollection['commission'];
                    $commissionFee = $orderPrice * ($percentPerProduct / 100);
                  $stylistAmount = $shippingPrice - $commissionFee;

                    /**
                     *  Storing commission information in database
                     */
                    $commissionData = array('stylist_id' => $stylistId, 'product_id' => $getProductId, 'product_qty' => $productQty, 'product_amt' => $productAmt, 'commission_fee' => $commissionFee, 'stylist_amount' => $stylistAmount, 'order_id' => $order->getId(), 'increment_id' => $order->getIncrementId(), 'order_total' => $grandTotal, 'order_status' => $status, 'credited' => $credited, 'customer_id' => $getCustomerId, 'status' => 1, 'created_at' => $createdAt, 'payment_method' => $paymentMethodCode);
                    $commissionId = $this->storeCommissionData($commissionData);
                }
                if ($paymentMethodCode == 'paypaladaptive') {
                    /**
                     * If payment method is paypal adaptive, then commission table(credited to stylist) and transaction table(amout paid to stylist) will be updated
                     */
                    $model = Mage::helper('stylisthub/stylisthub')->getCommissionInfo($commissionId);
                    $stylistId = $model->getStylistId();
                    $adminCommission = $model->getCommissionFee();
                    $stylistCommission = $model->getStylistAmount();
                    $orderId = $model->getOrderId();
                    $commissionId = $model->getId();
                    /**
                     * transaction collection to update the payment information
                     */
                    $transaction = Mage::helper('stylisthub/stylisthub')->getTransactionInfo($commissionId);
                    $transactionId = $transaction->getId();
                    if (empty($transactionId)) {
                        $Data = array('commission_id' => $commissionId, 'stylist_id' => $stylistId, 'stylist_commission' => $stylistCommission, 'admin_commission' => $adminCommission, 'order_id' => $orderId, 'received_status' => 0);
                        Mage::getModel('stylisthub/transaction')->setData($Data)->save();
                    }
                    /**
                     * Update the database after admin paid stylist amount
                     */
                    $transactions = Mage::getModel('stylisthub/transaction')->getCollection()
                            ->addFieldToFilter('stylist_id', $stylistId)
                            ->addFieldToSelect('id')
                            ->addFieldToFilter('paid', 0);
                    foreach ($transactions as $transaction) {
                        $transactionId = $transaction->getId();
                        if (!empty($transactionId)) {
                            Mage::helper('stylisthub/stylisthub')->updateTransactionData($transactionId);
                        }
                    }
                }
            }
        }
    }

    /**
     * creditmemo(Refund process)
     *
     * Order information will be get from the $observer parameter
     * @param array $observer
     *
     * @return void
     */
    public function creditMemoEvent(Varien_Event_Observer $observer) {

        $orderId = (int) Mage::app()->getRequest()->getParam('order_id');
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $items = $creditmemo->getAllItems();
        foreach ($items as $item) {
            $getProductId = $item->getProductId();
            /**
             * Gettings commission information in database table
             */
            $commissions = Mage::getModel('stylisthub/commission')->getCollection()
                    ->addFieldToFilter('order_id', $orderId)
                    ->addFieldToFilter('product_id', $getProductId)
                    ->addFieldToSelect('id')
                    ->addFieldToSelect('product_qty');
            foreach ($commissions as $commission) {
                $commissionId = $commission->getId();
                $commissionQty = $commission->getProductQty();
                $qty = $commissionQty - $item->getQty();
                $stylistId = $commission->getStylistId();
                $orderPrice = $item->getPrice() * $qty;
                /**
                 * Gettings stylist information in database table
                 */
                $stylistCollection = Mage::helper('stylisthub/stylisthub')->getStylistCollection($stylistId);
                $percentperproduct = $stylistCollection['commission'];
                $commissionFee = $orderPrice * ($percentperproduct / 100);
                $stylistAmount = $orderPrice - $commissionFee;
                if (empty($stylistAmount)) {
                    $status = 0;
                } else {
                    $status = 1;
                }
                /**
                 * update commission information in database table
                 */
                if (!empty($commissionId)) {
                    $Data = array('product_qty' => $qty, 'commission_fee' => $commissionFee, 'stylist_amount' => $stylistAmount, 'status' => $status);
                    Mage::helper('stylisthub/stylisthub')->saveCommissionData($Data, $commissionId);
                }
            }
        }
    }

    /**
     * If product edit(enable/disable) from admin panel this event function will be called to
     * send email notification to stylist
     *
     * Product information will be get from the $observer parameter
     * @param array $observer
     *
     * @return void
     */
    public function productEditAction($observer) {
        /**
         *  Checking whether email notification enabled or not
         */
        if (Mage::getStoreConfig('stylisthub/product/productmodificationnotification')) {

            $product = array();
            $productGroupId = $stylistId = $productUrl = $stylisthubGroupId = $savedProductStatus = $savedProductCreatedat = $savedProductUpdatedat = $savedProductUpdatedat = '';
            $store = 0;
            $storeName = 'All Store Views';
            $product = $observer->getProduct();
            $productGroupId = $product->getGroupId();
            $stylistId = $product->getStylistId();
            $productStatus = $product->getStatus();
            $stylisthubGroupId = Mage::helper('stylisthub')->getGroupId();
            $observer->getStoreId();
            if ($store != 0) {
                $storeName = Mage::getModel('core/store')->load($store);
            } else {
                $storeName = 'All Store Views';
            }
            $savedProductId = $product->getId();
            $savedProduct = Mage::getModel('catalog/product')->load($savedProductId);
            $savedProductStatus = $savedProduct->getStatus();
            if ($savedProductStatus != $productStatus && count($savedProduct) >= 1) {
                if ($productGroupId == $stylisthubGroupId) {
                    if ($productStatus == 1) {
                        $templateId = (int) Mage::getStoreConfig('stylisthub/product/addproductenabledemailnotificationtemplate');
                    } else {
                        $templateId = (int) Mage::getStoreConfig('stylisthub/product/addproductdisabledemailnotificationtemplate');
                    }
                    $adminEmailId = Mage::getStoreConfig('stylisthub/stylisthub/admin_email_id');
                    $toMailId = Mage::getStoreConfig("trans_email/ident_$adminEmailId/email");
                    $toName = Mage::getStoreConfig("trans_email/ident_$adminEmailId/name");
                    /**
                     *  Selecting template id
                     */
                    if ($templateId) {
                        $emailTemplate = Mage::getModel('core/email_template')->load($templateId);
                    } else {
                        if ($productStatus == 1) {

                            $emailTemplate = Mage::getModel('core/email_template')
                                    ->loadDefault('stylisthub_product_addproductenabledemailnotificationtemplate');
                        } else {

                            $emailTemplate = Mage::getModel('core/email_template')
                                    ->loadDefault('stylisthub_product_addproductdisabledemailnotificationtemplate');
                        }
                    }
                    $customer = Mage::getModel('customer/customer')->load($stylistId);
                    $stylistemail = $customer->getEmail();
                    $recipient = $stylistemail;
                    $stylistname = $customer->getName();
                    $productname = $product->getName();
                    $producturl = $product->getProductUrl();
                    $emailTemplate->setSenderName($toName);
                    $emailTemplate->setSenderEmail($toMailId);
                    $emailTemplateVariables = (array('ownername' => $toName, 'stylistname' => $stylistname, 'adminemailid' => $toMailId, 'productname' => $productname, 'producturl' => $producturl, 'storename' => $storeName));
                    $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                    $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                    $emailTemplate->send($recipient, $toName, $emailTemplateVariables);
                }
            }
        }
    }

    /**
     * If multiple product are selected to edit(enable/disable) from admin panel this event function will be called to
     * send email notification to stylist
     *
     * Product information will be get from the $observer parameter
     * @param array $observer
     *
     * @return void
     */
    public function productMassEditAction($observer) {
        /**
         *  Checking whether email notification enabled or not
         */
        if (Mage::getStoreConfig('stylisthub/product/productmodificationnotification')) {
            $product = $productIds = $attributesData = array();
            $storeName = 'All Store Views';
            $storeName = 0;
            $attributesData = $observer->getAttributesData();
            $status = $attributesData['status'];
            $productIds = $observer->getProductIds();
            $store = $observer->getStoreId();
            if ($store != 0) {
                $storeName = Mage::getModel('core/store')->load($store);
            } else {
                $storeName = 'All Store Views';
            }
            foreach ($productIds as $productId) {
                $stylisthubGroupId = $prdouctGroupId = $stylistId = $productStatus = '';
                $stylisthubGroupId = Mage::helper('stylisthub')->getGroupId();
                $product = Mage::helper('stylisthub/stylisthub')->getProductInfo($productId);
                $prdouctGroupId = $product->getGroupId();
                $stylistId = $product->getStylistId();
                $productStatus = $product->getStatus();
                if ($productStatus != $status && $prdouctGroupId == $stylisthubGroupId) {
                    if ($status == 1) {
                        $templateId = (int) Mage::getStoreConfig('stylisthub/product/addproductenabledemailnotificationtemplate');
                    } else {
                        $templateId = (int) Mage::getStoreConfig('stylisthub/product/addproductdisabledemailnotificationtemplate');
                    }
                    $adminEmailId = Mage::getStoreConfig('stylisthub/stylisthub/admin_email_id');
                    $toMailId = Mage::getStoreConfig("trans_email/ident_$adminEmailId/email");
                    $toName = Mage::getStoreConfig("trans_email/ident_$adminEmailId/name");
                    /**
                     *  Selecting template id
                     */
                    if ($templateId) {
                        $emailTemplate = Mage::helper('stylisthub/stylisthub')->loadEmailTemplate($templateId);
                    } else {
                        if ($status == 1) {
                            $emailTemplate = Mage::getModel('core/email_template')
                                    ->loadDefault('stylisthub_product_addproductenabledemailnotificationtemplate');
                        } else {
                            $emailTemplate = Mage::getModel('core/email_template')
                                    ->loadDefault('stylisthub_product_addproductdisabledemailnotificationtemplate');
                        }
                    }
                    $customer = Mage::helper('stylisthub/stylisthub')->loadCustomerData($stylistId);
                    $stylistemail = $customer->getEmail();
                    $recipient = $stylistemail;
                    $stylistname = $customer->getName();
                    $productname = $product->getName();
                    $producturl = $product->getProductUrl();
                    $emailTemplate->setSenderName($toName);
                    $emailTemplate->setSenderEmail($toMailId);
                    $emailTemplateVariables = (array('ownername' => $toName, 'stylistname' => $stylistname, 'adminemailid' => $toMailId, 'productname' => $productname, 'producturl' => $producturl, 'storename' => $storeName));
                    $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                    $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                    $emailTemplate->send($recipient, $toName, $emailTemplateVariables);
                }
            }
        }
    }

    /**
     * Send Order Email to stylist
     *
     * Passed the order information to send with email
     * @param array $orderEmailData
     *
     * @return void
     */
    public function sendOrderEmail($orderEmailData) {

        $stylistIds = array();
        /**
         * For Language translation assigned the table heading in varibles
         */
        $displayProductName = Mage::helper('stylisthub')->__('Product Name');
        $displayProductQty = Mage::helper('stylisthub')->__('Product QTY');
        $displayProductAmt = Mage::helper('stylisthub')->__('Product Amount');
        $displayProductCommission = Mage::helper('stylisthub')->__('Commission Fee');
        $displayStylistAmount = Mage::helper('stylisthub')->__('Stylist Amount');
        foreach ($orderEmailData as $data) {
	        if (!in_array($data['stylist_id'], $stylistIds)) {
	            $stylistIds[] = $data['stylist_id'];
	        }
        }
        foreach ($stylistIds as $key => $id) {
            $totalProductAmt = $totalCommissionFee = $totalStylistAmt = 0;
            $productDetails = '<table cellspacing="0" cellpadding="0" border="0" width="650" style="border:1px solid #eaeaea">';
            $productDetails .='<thead><tr>';
            $productDetails .='<th align="left" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductName . '</th>';
            $productDetails .='<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductQty . '</th>';
            $productDetails .='<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductAmt . '</th>';
            $productDetails .='<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductCommission . '</th>';
            $productDetails .='<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayStylistAmount . '</th>';
            $productDetails .='</tr></thead>';
            $productDetails .='<tbody bgcolor="#F6F6F6">';
            $currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
            foreach ($orderEmailData as $data) {
                if ($id == $data['stylist_id']) {
                    $stylistId = $data['stylist_id'];
                    $productId = $data['product_id'];
                    $product = Mage::helper('stylisthub/stylisthub')->getProductInfo($productId);
                    /**
                     *  Getting group id
                     */
                    $groupId = Mage::helper('stylisthub')->getGroupId();
                    $productGroupId = $product->getGroupId();
                    $productName = $product->getName();
                    $productDetails .= '<tr>';
                    $productDetails .= '<td align="left" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $productName . '</td>';
                    $productDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $data['product_qty'] . '</td>';
                    $productDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . $data['product_amt'] . '</td>';
                    $productDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . $data['commission_fee'] . '</td>';
                    $productDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . $data['stylist_amount'] . '</td>';
                    $totalProductAmt = $totalProductAmt + $data['product_amt'];
                    $totalCommissionFee = $totalCommissionFee + $data['commission_fee'];
                    $totalStylistAmt = $totalStylistAmt + $data['stylist_amount'];
                    $incrementId = $data['increment_id'];
                    $customerEmail = $data['customer_email'];
                    $customerFirstname = $data['customer_firstname'];
                    $productDetails .= '</tr>';
                }
            }
            $productDetails .= '</tbody>';
            $productDetails .= '<tfoot>
                                 <tr>
                                    <td colspan="4" align="right" style="padding:3px 9px">Commision Fee</td>
                                    <td align="center" style="padding:3px 9px"><span>' . $currencySymbol . $totalCommissionFee . '</span></td>
                                </tr>
                                 <tr>
                                    <td colspan="4" align="right" style="padding:3px 9px">Stylist Amount</td>
                                    <td align="center" style="padding:3px 9px"><span>' . $currencySymbol . $totalStylistAmt . '</span></td>
                                </tr>
                                <tr>
                                    <td colspan="4" align="right" style="padding:3px 9px"><strong>Total Product Amount</strong></td>
                                    <td align="center" style="padding:3px 9px"><strong><span>' . $currencySymbol . $totalProductAmt . '</span></strong></td>
                                </tr>
                            </tfoot>';
            $productDetails .= '</table>';

            if ($groupId == $productGroupId) {
                /**
                 *  Sending order email
                 */
                $templateId = (int) Mage::getStoreConfig('stylisthub/admin_approval_stylist_registration/sales_notification_template_selection');
                $adminEmailId = Mage::getStoreConfig('stylisthub/stylisthub/admin_email_id');
                $toMailId = Mage::getStoreConfig("trans_email/ident_$adminEmailId/email");
                $toName = Mage::getStoreConfig("trans_email/ident_$adminEmailId/name");

                if ($templateId) {
                    $emailTemplate = Mage::helper('stylisthub/stylisthub')->loadEmailTemplate($templateId);
                } else {
                    $emailTemplate = Mage::getModel('core/email_template')
                            ->loadDefault('stylisthub_admin_approval_stylist_registration_sales_notification_template_selection');
                }
                $customer = Mage::helper('stylisthub/stylisthub')->loadCustomerData($stylistId);
                $stylistEmail = $customer->getEmail();
                $stylistName = $customer->getName();
                $recipient = $toMailId;
                $stylistStore = Mage::app()->getStore()->getName();
                $recipientStylist = $stylistEmail;
                $emailTemplate->setSenderName($customerFirstname);
                $emailTemplate->setSenderEmail($customerEmail);
                $emailTemplateVariables = (array('ownername' => $toName, 'productdetails' => $productDetails, 'order_id' => $incrementId, 'stylist_store' => $stylistStore, 'customer_email' => $customerEmail, 'customer_firstname' => $customerFirstname));
                $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                /**
                 *  Sending email to admin
                 */
                $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                $emailTemplate->send($recipient, $stylistName, $emailTemplateVariables);
                /**
                 *  Sending email to stylist
                 */
                $emailTemplateVariables = (array('ownername' => $stylistName, 'productdetails' => $productDetails, 'order_id' => $incrementId, 'stylist_store' => $stylistStore, 'customer_email' => $customerEmail, 'customer_firstname' => $customerFirstname));
                $emailTemplate->send($recipientStylist, $stylistName, $emailTemplateVariables);
            }
        }
    }

    /**
     * Setting Cron job to enable/disable vacation mode by stylist
     *
     * @return void
     */
    public function eventVacationMode() {
        $currentDate = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
        $vacationInfo = Mage::getModel('stylisthub/vacationmode')->getCollection()->addFieldToSelect('*');
        foreach ($vacationInfo as $_vacationInfo) {
            $fromDate = $_vacationInfo['date_from'];
            $toDate = $_vacationInfo['date_to'];
            $stylistId = $_vacationInfo['stylist_id'];
            $productStatus = $_vacationInfo['product_disabled'];
            $product = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('stylist_id', $stylistId);
            $productId = array();
            foreach ($product as $_product) {
                $productId[] = $_product->getId();
            }
            if ($currentDate >= $fromDate && $currentDate <= $toDate && $productStatus == 0) {
                foreach ($productId as $_productId) {
                    Mage::getModel('catalog/product')->load($_productId)->setStatus(2)->save();
                }
            }
            if ($currentDate <= $fromDate && $currentDate >= $toDate) {
                foreach ($productId as $_productId) {
                    Mage::getModel('catalog/product')->load($_productId)->setStatus(1)->save();
                }
            }
        }
    }

    /**
     * Email notification will be sent to stylist after admin cancel a order
     *
     * @return void
     */
    public function cancelOrderEmail($observer) {
       $orderIds = $observer->getEvent()->getOrder()->getId();
       $order = Mage::getModel('sales/order')->load($orderIds);
        /**
         * get Product inforation to send that details in email
         */
        $items = $order->getAllItems();
        $orderEmailData = array();
        foreach ($items as $item) {
            $getProductId = $item->getProductId();
            $products = Mage::helper('stylisthub/stylisthub')->getProductInfo($getProductId);
            $stylistShippingEnabled = Mage::getStoreConfig('carriers/smd/active');
            if($stylistShippingEnabled == 1){
            	$nationalShippingPrice = $products->getNationalShippingPrice();
            	$internationalShippingPrice = $products->getInternationalShippingPrice();
            	$stylistDefaultCountry = $products->getDefaultCountry();
            	$shippingCountryId = $order->getShippingAddress()->getCountry();
            }
            $stylistId = $products->getStylistId();
            if ($stylistId) {
                $orderPrice = $item->getPrice() * $item->getQtyOrdered();
                $productAmt = $item->getPrice();
                $productQty = $item->getQtyOrdered();
            if($stylistDefaultCountry == $shippingCountryId){
                    	$shippingPrice = $orderPrice + ($nationalShippingPrice * $productQty);
                    } else {
                    	$shippingPrice = $orderPrice + ($internationalShippingPrice* $productQty);
                    }
                /**
                 * Get stylist commission percent
                 */
                $stylistCollection = Mage::helper('stylisthub/stylisthub')->getStylistCollection($stylistId);
                $percentperproduct = $stylistCollection['commission'];
                $commissionFee = $orderPrice * ($percentperproduct / 100);
               $stylistAmount = $shippingPrice - $commissionFee;

                $orderEmailData[$itemCount]['stylist_id'] = $stylistId;
                $orderEmailData[$itemCount]['product_id'] = $getProductId;
                $orderEmailData[$itemCount]['product_qty'] = $productQty;
                $orderEmailData[$itemCount]['product_amt'] = $productAmt;
                $orderEmailData[$itemCount]['commission_fee'] = $commissionFee;
                $orderEmailData[$itemCount]['stylist_amount'] = $stylistAmount;
                $orderEmailData[$itemCount]['increment_id'] = $order->getIncrementId();
                $orderEmailData[$itemCount]['customer_email'] = $order->getCustomerEmail();
                $orderEmailData[$itemCount]['customer_firstname'] = $order->getCustomerFirstname();
                $itemCount = $itemCount + 1;
            }
        }
        if (Mage::getStoreConfig('stylisthub/admin_approval_stylist_registration/cancel_order_notification') == 1) {
            $this->sendCancelOrderEmail($orderEmailData);
        }
    }

    /**
     * Send Cancel Order Email to stylist
     *
     * Order information will be get from the $observer parameter
     * @param array $observer
     *
     * @return void
     */
    public function sendCancelOrderEmail($orderEmailData) {
        $stylistIds = array();
        /**
         * For Language translation assigned the table heading in varibles
         */
        $displayProductName = Mage::helper('stylisthub')->__('Product Name');
        $displayProductQty = Mage::helper('stylisthub')->__('Product QTY');
        $displayProductAmt = Mage::helper('stylisthub')->__('Product Amount');
        $displayProductCommission = Mage::helper('stylisthub')->__('Commission Fee');
        $displayStylistAmount = Mage::helper('stylisthub')->__('Stylist Amount');
        foreach ($orderEmailData as $data) {
            if (!in_array($data['stylist_id'], $stylistIds)) {
                $stylistIds[] = $data['stylist_id'];
            }
        }
        foreach ($stylistIds as $key => $id) {
            $totalProductAmt = $totalCommissionFee = $totalStylistAmt = 0;
            $productDetails = '<table cellspacing="0" cellpadding="0" border="0" width="650" style="border:1px solid #eaeaea">';
            $productDetails .='<thead><tr>';
            $productDetails .='<th align="left" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductName . '</th>';
            $productDetails .='<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductQty . '</th>';
            $productDetails .='<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductAmt . '</th>';
            $productDetails .='<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductCommission . '</th>';
            $productDetails .='<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayStylistAmount . '</th>';
            $productDetails .='</tr></thead>';
            $productDetails .='<tbody bgcolor="#F6F6F6">';
            $currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
            foreach ($orderEmailData as $data) {

                if ($id == $data['stylist_id']) {
                    $stylistId = $data['stylist_id'];
                    $productId = $data['product_id'];
                    $product = Mage::helper('stylisthub/stylisthub')->getProductInfo($productId);
                    /**
                     *  Getting group id
                     */
                    $groupId = Mage::helper('stylisthub')->getGroupId();
                    $productGroupId = $product->getGroupId();
                    $productName = $product->getName();
                    $productDetails .= '<tr>';
                    $productDetails .= '<td align="left" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $productName . '</td>';
                    $productDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $data['product_qty'] . '</td>';
                    $productDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . $data['product_amt'] . '</td>';
                    $productDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . $data['commission_fee'] . '</td>';
                    $productDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . $data['stylist_amount'] . '</td>';
                    $totalProductAmt = $totalProductAmt + $data['product_amt'];
                    $totalCommissionFee = $totalCommissionFee + $data['commission_fee'];
                    $totalStylistAmt = $totalStylistAmt + $data['stylist_amount'];
                    $incrementId = $data['increment_id'];
                    $customerEmail = $data['customer_email'];
                    $customerFirstname = $data['customer_firstname'];
                    $productDetails .= '</tr>';
                }
            }
            $productDetails .= '</tbody>';
            $productDetails .= '<tfoot>
                                 <tr>
                                    <td colspan="4" align="right" style="padding:3px 9px">Commision Fee</td>
                                    <td align="center" style="padding:3px 9px"><span>' . $currencySymbol . $totalCommissionFee . '</span></td>
                                </tr>
                                 <tr>
                                    <td colspan="4" align="right" style="padding:3px 9px">Stylist Amount</td>
                                    <td align="center" style="padding:3px 9px"><span>' . $currencySymbol . $totalStylistAmt . '</span></td>
                                </tr>
                                <tr>
                                    <td colspan="4" align="right" style="padding:3px 9px"><strong>Total Product Amount</strong></td>
                                    <td align="center" style="padding:3px 9px"><strong><span>' . $currencySymbol . $totalProductAmt . '</span></strong></td>
                                </tr>
                            </tfoot>';
            $productDetails .= '</table>';

            if ($groupId == $productGroupId) {

                /**
                 *  Sending order email
                 */
                $templateId = (int) Mage::getStoreConfig('stylisthub/admin_approval_stylist_registration/cancel_notification_template_selection');
                $adminEmailId = Mage::getStoreConfig('stylisthub/stylisthub/admin_email_id');
                $toMailId = Mage::getStoreConfig("trans_email/ident_$adminEmailId/email");
                $toName = Mage::getStoreConfig("trans_email/ident_$adminEmailId/name");
                if ($templateId) {
                    $emailTemplate = Mage::helper('stylisthub/stylisthub')->loadEmailTemplate($templateId);
                } else {
                    $emailTemplate = Mage::getModel('core/email_template')
                            ->loadDefault('stylisthub_admin_approval_stylist_registration_cancel_notification_template_selection');
                }
                /**
                 * Loading customer data to send in email
                 */
                $customer = Mage::helper('stylisthub/stylisthub')->loadCustomerData($stylistId);
                $stylistEmail = $customer->getEmail();
                $stylistName = $customer->getName();
                $recipient = $toMailId;
                $stylistStore = Mage::app()->getStore()->getName();
                $recipientStylist = $stylistEmail;
                $emailTemplate->setSenderName($toName);
                $emailTemplate->setSenderEmail($toMailId);
                $emailTemplateVariables = (array('ownername' => $customerFirstname, 'productdetails' => $productDetails, 'order_id' => $incrementId, 'stylist_store' => $stylistStore, 'customer_email' => $customerEmail, 'customer_firstname' => $customerFirstname));
                $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                /**
                 *  Sending email to admin
                 */
                $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                $emailTemplate->send($recipient, $stylistName, $emailTemplateVariables);

                /**
                 *  Sending email to stylist
                 */
                $emailTemplateVariables = (array('ownername' => $stylistName, 'productdetails' => $productDetails, 'order_id' => $incrementId, 'stylist_store' => $stylistStore, 'customer_email' => $customerEmail, 'customer_firstname' => $customerFirstname));
                $emailTemplate->send($recipientStylist, $stylistName, $emailTemplateVariables);
            }
        }
    }
}
