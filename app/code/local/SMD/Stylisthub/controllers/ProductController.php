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
 * This file is used to add/edit stylist products
 */
class SMD_Stylisthub_ProductController extends Mage_Core_Controller_Front_Action {

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Load phtml file layout
     *
     * @return void
     */
    public function indexAction() {
        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Add New Products Form
     *
     * @return void
     */
    public function newAction() {
        /**
         *  Check license key
         */

        /**
         *  Initilize customer and stylist group id
         */
        $customerGroupId = $stylistGroupId = $customerStatus = '';
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $stylistGroupId = Mage::helper('stylisthub')->getGroupId();
        $customerStatus = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();
        if (!$this->_getSession()->isLoggedIn() && $customerGroupId != $stylistGroupId) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        /**
         *  Checking whether customer approved or not
         */
        if ($customerStatus != 1) {
            Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirms your Stylist Account'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Save New Products
     *
     * @return void
     */
    public function newpostAction() {
        /**
         *  Check license key
         */

        /**
         *  Initilize customer and stylist group id
         */
        $customerGroupId = $stylistGroupId = $customerStatus = '';
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $stylistGroupId = Mage::helper('stylisthub')->getGroupId();
        $customerStatus = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();
        if (!$this->_getSession()->isLoggedIn() && $customerGroupId != $stylistGroupId) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        /**
         *  Checking whether customer approved or not
         */
        if ($customerStatus != 1) {
            Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirms your Stylist Account'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        /**
         *  Initializing variables
         */
        $sku = $skuFirst = $skuSecond = $skuThird = $productNameTrim = $set = $setbase = $type = $store = $stylistId = $setbanner = $comparePrice = '';
        /**
         *  Getting  product values
         */
        $type = $this->getRequest()->getPost('type');
        /**
         *  Attribute set
         */
        $set = $this->getRequest()->getPost('set');
       $setbase = $this->getRequest()->getPost('setbase');
        $store = $this->getRequest()->getPost('store');
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $stylistId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        }
        /**
         *  Getting group id
         */
        $groupId = Mage::helper('stylisthub')->getGroupId();
        /**
         *  Getting product data from product array
         */
        $productData = $this->getRequest()->getPost('product');

        /**
         *  Getting product categories from category_ids array
         */
        $categoryIds = $this->getRequest()->getPost('category_ids');
        if (!empty($productData['name']) && !empty($productData['description']) && isset($productData['price']) && isset($productData['stock_data']['qty']) && !empty($type)) {
            /**
             *  Initilize product weight
             */
            if ($type == 'simple') {
                if (!isset($productData['weight'])) {
                    $productData['weight'] = 0;
                }
            }
            /**
             *  Assing product short description
             */
            if (!empty($productData['short_description'])) {
                $productData['short_description'] = $productData['short_description'];
            }
            /**
             *  Assign create at time
             */
            $createdAt = Mage::getModel('core/date')->gmtDate();

            /**
             *  Getting instance for catalog product collection
             */
            $product = Mage::getModel('catalog/product');
            /**
             *  Initialize product sku
             */
            if (!empty($sku)) {
                $productData['sku'] = $sku;
            }
            /**
             *  Initialize product attribute set id
             */
            if (isset($set)) {
                $product->setAttributeSetId($set);
            }
            /**
             *  Initialize product type
             */
            if (isset($type)) {
                $product->setTypeId($type);
            }


            /**
             *  Initialize product categories
             */
            if (isset($categoryIds)) {
                $product->setCategoryIds($categoryIds);
            }

            /**
             *  Storing product data's to all store view
             */
            $product->setStoreId(0);

            /**
             *  Initialize product create at time
             */
            if (isset($createdAt)) {
                $product->setCreatedAt($createdAt);
            }
            /**
             *  Initialize stylist id
             */
            if (isset($stylistId)) {
                $product->setStylistId($stylistId);
            }

            /**
             *  Initialize group id
             */
            if (isset($groupId)) {
                $product->setGroupId($groupId);
            }

            /**
             * Set product in banner image
             */
            if (isset($productData['setbanner'])) {
                $product->setSetbanner($productData['setbanner']);
            }
            $uploadsData = new Zend_File_Transfer_Adapter_Http();
            $filesDataArray = $uploadsData->getFileInfo();

            /**
             *  Checking whether image exist or not
             */
            if (!empty($filesDataArray)) {
                foreach ($filesDataArray as $key => $value) {
                    /**
                     *  Initilize file name
                     */
                    $filename = $key;

                    if (substr($key, 0, 5) == 'image') {
                        if (isset($filesDataArray[$filename]['name']) && (file_exists($filesDataArray[$filename]['tmp_name']))) {
                            try {
                                $imagesPath[] = Mage::helper('stylisthub/stylisthub')->uploadImage($filename, $filesDataArray);
                            } catch (Exception $e) {
                                /**
                                 *  Display error message for images upload
                                 */
                                Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                            }
                        }
                    }
                }
            }

            /**
             *  Adding Product images
             */
            if (!empty($imagesPath)) {
                $product->setMediaGallery(array('images' => array(), 'values' => array()));
                foreach ($imagesPath as $value) {
                    $product->addImageToMediaGallery($value, array('image', 'small_image', 'thumbnail'), false, false);
                }
            }


            /**
             *   Initialize dispatch event for product prepare
             */
            Mage::dispatchEvent(
                    'catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest())
            );

            /**
             *  Adding data to product instanse
             */
            if (!empty($productData)) {
                $product->addData($productData);
            }
            /**
             *  Saving new product
             */
            try {
                $product->save();

                $productId = $product->getId();

                /**
                 * Load the product
                 */
                $product = Mage::getModel('catalog/product')->load($productId);
                /**
                 * Get all images
                 */
                $mediaGallery = $product->getMediaGallery();
                /**
                 * If there are images
                 */
                if (isset($mediaGallery['images']) && !empty($store)) {
                    /**
                     * Loop through the images
                     */
                	$increment = 0;
                    foreach ($mediaGallery['images'] as $image) {
                        /**
                         * Set the first image as the base image
                         */
                    	if($increment == $setbase){
                        $product->setStoreId($store)
                                ->setImage($image['file'])
                                ->setSmallImage($image['file'])
                                ->setThumbnail($image['file']);
                        if (isset($productData['setbanner'])) {
                            $product->setBanner($image['file']);
                        	}
                        	$product->save();
                    	}
                        $increment++;
                        /**
                         * Stop
                         */

                    }

                }

                /**
                 *   Initialize product options
                 */
                if (!empty($productData['options'])) {
                    $product->setProductOptions($productData['options']);
                    $product->setCanSaveCustomOptions(1);
                    $product->save();
                }

                /**
                 *  Checking whether image or not
                 */
                if (!empty($imagesPath)) {
                    foreach ($imagesPath as $deleteImage) {
                        /**
                         *  Checking whether image exist or not
                         */
                        if (file_exists($deleteImage)) {
                            /**
                             *  Delete images from temporary folder
                             */
                            unlink($deleteImage);
                        }
                    }
                }

                /**
                 *  Function for adding downloadable product sample and link data
                 */
                $downloadProductId = $product->getId();
                if ($type == 'downloadable' && isset($downloadProductId) && isset($store)) {
                    $this->addDownloadableProductData($downloadProductId, $store);
                }

                /**
                 *  Success message redirect to manage product page
                 */
                if (Mage::helper('stylisthub')->getProductApproval() == 1) {
                    Mage::getSingleton('core/session')->addSuccess($this->__('Your product is added successfully'));


                    if (Mage::getStoreConfig('stylisthub/product/addproductemailnotification') == 1) {
                        /**
                         *  Sending email for added new product
                         */
                        $templateId = (int) Mage::getStoreConfig('stylisthub/product/addproductemailnotificationtemplate');
                        $adminEmailId = Mage::getStoreConfig('stylisthub/stylisthub/admin_email_id');
                        $toMailId = Mage::getStoreConfig("trans_email/ident_$adminEmailId/email");
                        $toName = Mage::getStoreConfig("trans_email/ident_$adminEmailId/name");

                        /**
                         *  Selecting template id
                         */
                        if ($templateId) {
                            $emailTemplate = Mage::getModel('core/email_template')->load($templateId);
                        } else {
                            $emailTemplate = Mage::getModel('core/email_template')
                                    ->loadDefault('stylisthub_product_addproductemailnotificationtemplate');
                        }
                        $customer = Mage::getModel('customer/customer')->load($stylistId);
                        $stylistemail = $customer->getEmail();
                        $recipient = $toMailId;
                        $stylistname = $customer->getName();
                        $productname = $product->getName();
                        $producturl = $product->getProductUrl();
                        $emailTemplate->setSenderName($stylistname);
                        $emailTemplate->setSenderEmail($stylistemail);
                        $emailTemplateVariables = (array('ownername' => $toName, 'stylistname' => $stylistname, 'stylistemail' => $stylistemail, 'productname' => $productname, 'producturl' => $producturl));
                        $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                        $emailTemplate->send($recipient, $stylistname, $emailTemplateVariables);
                    }
                } else {
                    Mage::getSingleton('core/session')->addSuccess($this->__('Your product is awaiting moderation'));

                    if (Mage::getStoreConfig('stylisthub/product/addproductemailnotification') == 1) {
                        /**
                         *  Sending email for added new product
                         */
                        $templateId = (int) Mage::getStoreConfig('stylisthub/product/addproductapprovalemailnotificationtemplate');
                        $adminEmailId = Mage::getStoreConfig('stylisthub/stylisthub/admin_email_id');
                        $toMailId = Mage::getStoreConfig("trans_email/ident_$adminEmailId/email");
                        $toName = Mage::getStoreConfig("trans_email/ident_$adminEmailId/name");

                        if ($templateId) {
                            $emailTemplate = Mage::getModel('core/email_template')->load($templateId);
                        } else {
                            $emailTemplate = Mage::getModel('core/email_template')
                                    ->loadDefault('stylisthub_product_addproductapprovalemailnotificationtemplate');
                        }
                        $customer = Mage::getModel('customer/customer')->load($stylistId);
                        $stylistemail = $customer->getEmail();
                        $recipient = $toMailId;
                        $stylistname = $customer->getName();
                        $productname = $product->getName();
                        $producturl = Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/edit', array('id' => $product->getId()));

                        $emailTemplate->setSenderName($stylistname);
                        $emailTemplate->setSenderEmail($stylistemail);
                        $emailTemplateVariables = (array('ownername' => $toName, 'stylistname' => $stylistname, 'stylistemail' => $stylistemail, 'productname' => $productname, 'producturl' => $producturl));
                        $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                        $emailTemplate->send($recipient, $stylistname, $emailTemplateVariables);
                    }
                }
                $this->_redirect('stylisthub/product/manage/');
            } catch (Mage_Core_Exception $e) {
                /**
                 *  Error message redirect to create new product page
                 */
                Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                $this->_redirect('stylisthub/product/new/');
            } catch (Exception $e) {
                /**
                 *  Error message redirect to create new product page
                 */
                Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                $this->_redirect('stylisthub/product/new/');
            }
        } else {
            Mage::getSingleton('core/session')->addError($this->__('Please enter all required fields'));
            $this->_redirect('stylisthub/product/new');
        }
    }

    /**
     * Manage Stylist Products
     *
     * @return void
     */
    public function manageAction() {
        /**
         *  Check license key
         */

        /**
         *  Initilize customer and stylist group id
         */
        $customerGroupId = $stylistGroupId = $customerStatus = '';
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $stylistGroupId = Mage::helper('stylisthub')->getGroupId();
        $customer = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();
        if (!$this->_getSession()->isLoggedIn() && $customerGroupId != $stylistGroupId) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page.'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        /**
         *  Checking whether customer approved or not
         */
        if ($customer != 1) {
            Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirms your Stylist Account.'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Edit Existing Products
     *
     * @return void
     */
    public function editAction() {
        /**
         *  Check license key
         */


        /**
         *  Initilize customer and stylist group id
         */
        $customerGroupId = $stylistGroupId = $customerStatus = '';
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $stylistGroupId = Mage::helper('stylisthub')->getGroupId();
        /**
         *  Checking whether customer and stylist group id
         */
        if (!$this->_getSession()->isLoggedIn() && $customerGroupId != $stylistGroupId) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page.'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        /**
         *  Initilize product id , customer id and stylist id
         */
        $entityId = (int) $this->getRequest()->getParam('id');
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection = Mage::getModel('catalog/product')->load($entityId);
        $stylistId = $collection->getStylistId();
        /**
         *  Checking for edit permission
         */
        if ($customerId != $stylistId) {
            Mage::getSingleton('core/session')->addError($this->__("You don't have enough permission to edit this product details."));
            $this->_redirect('stylisthub/product/manage');
            return;
        }
        $customerStatus = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();
        /**
         *  Checking whether customer approved or not
         */
        if ($customerStatus != 1) {
            Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirm your Stylist Account'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Save Edited Products
     *
     * @return void
     */
    public function editpostAction() {
        /**
         *  Check license key
         */

        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        /**
         *  Initilize customer and stylist group id
         */
        $customerGroupId = $stylistGroupId = $customerStatus = '';
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $stylistGroupId = Mage::helper('stylisthub')->getGroupId();
        $customerStatus = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();
        if (!$this->_getSession()->isLoggedIn() && $customerGroupId != $stylistGroupId) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        /**
         *  Checking whether customer approved or not
         */
        if ($customerStatus != 1) {
            Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirms your Stylist Account'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        /**
         *  Initialize product data
         */
        $productId = $name = $weight = $description = $shortDescription = $price = $metaTitle = $metaKeyword = $metaDescription = $store = $sku = $shippingOption= $nationalShippingPrice  =$internationalShippingPrice=$defaultCountry='';
        $categoryIds = $stockData = $deleteimages = array();
        $type = $this->getRequest()->getPost('type');
        $productData = $this->getRequest()->getPost('product');
        $productId = $this->getRequest()->getPost('product_id');
        $categoryIds = $this->getRequest()->getPost('category_ids');
        $store = Mage::app()->getStore()->getId();
        $name = $productData['name'];
        $sku = $productData['sku'];
        $description = $productData['description'];
        $shortDescription = $productData['short_description'];
        $price = $productData['price'];
        $metaTitle = $productData['meta_title'];
        $metaKeyword = $productData['meta_keyword'];
        $metaDescription = $productData['meta_description'];
        $qty = $productData['stock_data']['qty'];
        $isInStock = $productData['stock_data']['is_in_stock'];
        $specialPrice = $productData['special_price'];
        $specialFromDate = $productData['special_from_date'];
        $specialToDate = $productData['special_to_date'];
        $deleteimages = $this->getRequest()->getPost('deleteimages');
        $baseimage = $this->getRequest()->getPost('baseimage');
        $comparePrice = $productData['compare_product_id'];
        if(isset($productData['stylist_shipping_option'])){
            $shippingOption = $productData['stylist_shipping_option'];
        }
        if(isset($productData['national_shipping_price'])){
            $nationalShippingPrice = $productData['national_shipping_price'];
        }
        if(isset($productData['international_shipping_price'])){
            $internationalShippingPrice = $productData['international_shipping_price'];
        }
        if(isset($productData['default_country'])){
            $defaultCountry = $productData['default_country'];
        }
        /**
         *  Checking whether select type custom option having values or not
         */
        if (!empty($productData['options'])) {
            foreach ($productData['options'] as $o) {
                if ($o['is_delete'] != 1) {
                    $optionType = $o['type'];
                    if ($optionType == 'drop_down' || $optionType == 'radio' || $optionType == 'checkbox' || $optionType == 'multiple') {
                        if (!isset($o['values']) || empty($o['values'])) {
                            Mage::getSingleton('core/session')->addError($this->__('Custom type option not selected.'));
                            $this->_redirect('stylisthub/product/edit/id/' . $productId);
                            return;
                        }
                    }
                }
            }
        }
        /**
         *  Initilize product categories
         */
        if (empty($categoryIds)) {
            $categoryIds = array();
        }
        if (!empty($sku) && !empty($productId) && !empty($name) && !empty($description) && !empty($shortDescription) && isset($price) && isset($qty) && !empty($type)) {
            $product = Mage::getModel("catalog/product")->load($productId);
            /**
             *  Initilize product weight
             */
            if ($type == 'simple') {
                if (empty($productData['weight'])) {
                    $weight = 0;
                } else {
                    $weight = $productData['weight'];
                }
                $product->setWeight($weight);
            }
            /**
             *  Initilize product in stock
             */
            $stockData['is_in_stock'] = Mage::helper('stylisthub/stylisthub')->productInStock($isInStock);

            /**
             *  Initilize product store
             */
            if (isset($store)) {
                $product->setStoreId($store);
            }

            /**
             *  Initilize product special price and date
             */
            if (isset($specialPrice)) {
                $product->setSpecialPrice($specialPrice);
            }
            if (isset($productData['setbanner'])) {
                $product->setSetbanner($productData['setbanner']);
                $product->setBanner($baseimage);
            } else {
                $product->setSetbanner(0);
            }

            if (!empty($specialFromDate)) {
                $product->setSpecialFromDate($specialFromDate);
            } else {
                $product->setSpecialFromDate('');
            }
            if (!empty($specialToDate)) {
                $product->setSpecialToDate($specialToDate);
            } else {
                $product->setSpecialToDate('');
            }

            /**
             *  Updating product data
             */
            $product->setName($name);
            $product->setShortDescription($shortDescription);
            $product->setDescription($description);
            $product->setPrice($price);
            $product->setSku($sku);
            $product->setCompareProductId($comparePrice);
            $product->setStylistShippingOption($shippingOption);
            $product->setNationalShippingPrice($nationalShippingPrice);
            $product->setInternationalShippingPrice($internationalShippingPrice);
            $product->setDefaultCountry($defaultCountry);
            $product->setMetaTitle($metaTitle);
            $product->setMetaKeyword($metaKeyword);
            $product->setMetaDescription($metaDescription);
            $product->setCategoryIds($categoryIds);
            $stockData['qty'] = $qty;
            $product->setStockData($stockData);
            $uploadsData = new Zend_File_Transfer_Adapter_Http();
            $filesDataArray = $uploadsData->getFileInfo();
            /**
             *  Checking whether image exist or not
             */
            if (!empty($filesDataArray)) {
                foreach ($filesDataArray as $key => $value) {
                    /**
                     *  Initilize file name
                     */
                    $filename = $key;

                    if (substr($key, 0, 5) == 'image') {
                        if (isset($filesDataArray[$filename]['name']) && (file_exists($filesDataArray[$filename]['tmp_name']))) {
                            try {
                                $imagesPath[] = Mage::helper('stylisthub/stylisthub')->uploadImage($filename, $filesDataArray);
                            } catch (Exception $e) {
                                /**
                                 *  Display error message for images upload
                                 */
                                Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                            }
                        }
                    }
                }
            }

            /**
             *  Adding Product images
             */
            if (!empty($imagesPath)) {
                $product->setMediaGallery(array('images' => array(), 'values' => array()));
                foreach ($imagesPath as $value) {
                    $product->addImageToMediaGallery($value, array('image', 'small_image', 'thumbnail'), false, false);
                }
            }

            try {
                /**
                 *  Updating product data
                 */
                $product->save();
                /**
                 *  Setting product base image
                 */
                if (!empty($baseimage) && !empty($productId) && !empty($store)) {
                    $product = Mage::getModel("catalog/product")->load($productId);
                    $product->setStoreId($store)
                            ->setImage($baseimage)
                            ->setSmallImage($baseimage)
                            ->setThumbnail($baseimage);
                    if (isset($productData['setbanner'])) {
                        $product->setBanner($baseimage);
                    }
                    $product->save();
                } else {
                    /**
                     * Get all images
                     */
                    $mediaGallery = $product->getMediaGallery();
                    /**
                     * If there are images
                     */
                    if (isset($mediaGallery['images']) && !empty($productId)) {
                        $product = Mage::getModel("catalog/product")->load($productId);
                        /**
                         * Loop through the images
                         */
                        foreach ($mediaGallery['images'] as $image) {
                            /**
                             * Set the first image as the base image
                             */
                            $product->setStoreId(0)
                                    ->setImage($image['file'])
                                    ->setSmallImage($image['file'])
                                    ->setThumbnail($image['file']);
                            if (isset($productData['setbanner'])) {
                                $product->setBanner($image['file']);
                            }
                            $product->save();
                            /**
                             * Stop
                             */
                            break;
                        }
                    }
                }

                /**
                 *  Removing product images
                 */
                if (!empty($deleteimages) && !empty($productId)) {
                    $mediaApi = Mage::getModel("catalog/product_attribute_media_api");
                    foreach ($deleteimages as $image) {
                        $mediaApi->remove($productId, $image);
                    }
                }

                /**
                 *  Delete existing product custom option
                 */
                if ($product->getOptions()) {
                    foreach ($product->getOptions() as $opt) {
                        Mage::helper('stylisthub/stylisthub')->deleteCustomOption($opt);
                    }
                    $product->setCanSaveCustomOptions(1);
                    $product->save();
                }

                /**
                 *   Initialize product options
                 */
                if (!empty($productData['options'])) {
                    $product->setProductOptions($productData['options']);
                    $product->setCanSaveCustomOptions(1);
                    $product->save();
                }

                /**
                 *  Checking whether image or not
                 */
                if (!empty($imagesPath)) {
                    foreach ($imagesPath as $deleteImage) {
                        /**
                         *  Checking whether image exist or not
                         */
                        if (file_exists($deleteImage)) {
                            /**
                             *  Delete images from temporary folder
                             */
                            unlink($deleteImage);
                        }
                    }
                }

                /**
                 *  Function for edit downloadable product sample and link data
                 */
                $downloadProductId = $product->getId();
                if ($type == 'downloadable' && isset($downloadProductId) && isset($store)) {
                    $this->editDownloadableProductData($downloadProductId, $store);
                }

                /**
                 *  Success message redirect to manage product page
                 */
                Mage::getSingleton('core/session')->addSuccess($this->__('Your product details are updated successfully.'));
                $this->_redirect('stylisthub/product/manage/');
            } catch (Mage_Core_Exception $e) {
                /**
                 *  Error message redirect to create new product page
                 */
                Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                $this->_redirect('stylisthub/product/edit/id/' . $productId);
            } catch (Exception $e) {
                /**
                 *  Error message redirect to create new product page
                 */
                Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                $this->_redirect('stylisthub/product/edit/id/' . $productId);
            }
        } else {
            Mage::getSingleton('core/session')->addError($this->__('Please enter all required fields'));
            $this->_redirect('stylisthub/product/edit/id/' . $productId);
        }
    }

    /**
     * Delete Stylist Products
     *
     * @return void
     */
    public function deleteAction() {

        /**
         * check license key
         */


        /**
         *  Initilize customer and stylist group id
         */
        $customerGroupId = $stylistGroupId = $customerStatus = '';
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $stylistGroupId = Mage::helper('stylisthub')->getGroupId();
        $customerStatus = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();

        if (!$this->_getSession()->isLoggedIn() && $customerGroupId != $stylistGroupId) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
            $this->_redirect('stylisthub/stylist/login');
            return false;
        }
        /**
         *  Checking whether customer approved or not
         */
        if ($customerStatus != 1) {
            Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirms your Stylist Account'));
            $this->_redirect('stylisthub/stylist/login');
            return false;
        }
        $this->loadLayout();
        $this->renderLayout();
        $entity_id = (int) $this->getRequest()->getParam('id');

        /**
         * delete collection
         */
        /**
         *  set secure admin area
         */
        Mage::register('isSecureArea', true);
        Mage::getModel('catalog/product')->setId($entity_id)->delete();
        /**
         *  un set secure admin area
         */
        Mage::unregister('isSecureArea');
        Mage::getSingleton('core/session')->addSuccess($this->__("Product Deleted Successfully"));
        $this->_redirect('*/product/manage/');
        return true;
    }

    /**
     * Save Downloadable Products
     *
     * Passed the downloadable product id to save files
     * @param int $downloadProductId
     *
     * Passed the store id to save files
     * @param int $store
     *
     * @return void
     */
    public function addDownloadableProductData($downloadProductId, $store) {
        /**
         *  Initilize downloadable product sample and link files
         */
        $sampleTpath = $linkTpath = $slinkTpath = array();
        $uploadsData = new Zend_File_Transfer_Adapter_Http();
        $filesDataArray = $uploadsData->getFileInfo();
        foreach ($filesDataArray as $key => $result) {
            if (isset($filesDataArray[$key]['name']) && (file_exists($filesDataArray[$key]['tmp_name']))) {

                $type = '';
                if (substr($key, 0, 5) == 'sampl') {
                    $tmpPath = Mage_Downloadable_Model_Sample::getBaseTmpPath();
                    $type = 'samples';
                } elseif (substr($key, 0, 5) == 'links') {
                    $tmpPath = Mage_Downloadable_Model_Link::getBaseTmpPath();
                    $type = 'links';
                } elseif (substr($key, 0, 5) == 'l_sam') {
                    $tmpPath = Mage_Downloadable_Model_Link::getBaseSampleTmpPath();
                    $type = 'link_samples';
                }

                if ($type == 'samples' || $type == 'links' || $type == 'link_samples') {
                    $result = array();
                    try {
                        /**
                         *  Initilize validate flag
                         */
                        $validateFlag = 0;
                        /**
                         *  Getting uploaded file extension type
                         */
                        $uploaderExtension = pathinfo($filesDataArray[$key]['name'], PATHINFO_EXTENSION);
                        $validateImage = array('jpg', 'jpeg', 'gif', 'png');
                        if (in_array($uploaderExtension, $validateImage)) {
                            $imgSize = getimagesize($filesDataArray[$key]['tmp_name']);
                            if (!$imgSize) {
                                $uploader->setFilesDispersion(true);

                                $validateFlag = 1;
                            }
                        }

                        /**
                         *  Disallow php file for downloadable product
                         */
                        if ($uploaderExtension != 'php' && $validateFlag == 0) {
                            $uploader = new Varien_File_Uploader($key);
                            $uploader->setAllowRenameFiles(true);
                            $result = $uploader->save($tmpPath);
                            $result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
                            $result['path'] = str_replace(DS, "/", $result['path']);

                            if (isset($result['file'])) {
                                $fullPath = rtrim($tmpPath, DS) . DS . ltrim($result['file'], DS);
                                Mage::helper('core/file_storage_database')->saveFile($fullPath);
                            }

                            $fileName = $filePath = $fileSize = $sampleNo = '';
                            $fileName = $uploader->getUploadedFileName();
                            $filePath = ltrim($result['file'], DS);
                            $fileSize = $result['size'];

                            if ($type == 'samples') {
                                $sampleNo = substr($key, 7);
                                $sampleTpath[$sampleNo] = array(
                                    'file' => $filePath,
                                    'name' => $fileName,
                                    'size' => $fileSize,
                                    'status' => 'new'
                                );
                            } elseif ($type == 'links') {

                                $sampleNo = substr($key, 6);
                                $linkTpath[$sampleNo] = array(
                                    'file' => $filePath,
                                    'name' => $fileName,
                                    'size' => $fileSize,
                                    'status' => 'new'
                                );
                            } elseif ($type == 'link_samples') {

                                $sampleNo = substr($key, 9);
                                $slinkTpath[$sampleNo] = array(
                                    'file' => $filePath,
                                    'name' => $fileName,
                                    'size' => $fileSize,
                                    'status' => 'new'
                                );
                            }
                        } else {
                            Mage::getSingleton('core/session')->addError($this->__('Disallowed file type.'));
                        }
                    } catch (Exception $e) {
                        Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                    }
                }
            }
        }

        /**
         *  Initilize Downloadable product data
         */
        $downloadableData = $this->getRequest()->getPost('downloadable');

        try {

            /**
             *  Storing Downloadable product sample data
             */
            if (isset($downloadableData['sample'])) {
                foreach ($downloadableData['sample'] as $sampleItem) {
                    $sampleId = '';
                    $sample = array();
                    $sampleId = $sampleItem['sample_id'];
                    $sample[] = $sampleTpath[$sampleId];
                    $sampleModel = Mage::getModel('downloadable/sample');
                    $sampleModel->setData($sample)
                            ->setSampleType($sampleItem['type'])
                            ->setProductId($downloadProductId)
                            ->setStoreId(0)
                            ->setWebsiteIds(array(Mage::app()->getStore($store)->getWebsiteId()))
                            ->setTitle($sampleItem['title'])
                            ->setDefaultTitle(false)
                            ->setSortOrder($sampleItem['sort_order']);
                    if ($sampleItem['type'] == 'url') {
                        $sampleModel->setSampleUrl($sampleItem['sample_url']);
                    }
                    if (!empty($sampleTpath[$sampleId])) {
                        if ($sampleModel->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                            $sampleFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                    Mage_Downloadable_Model_Sample::getBaseTmpPath(), Mage_Downloadable_Model_Sample::getBasePath(), $sample
                            );
                        }
                        $sampleModel->setSampleFile($sampleFileName);
                    }

                    Mage::helper('stylisthub/stylisthub')->saveDownLoadLink($sampleModel);
                }
            }

            /**
             *  Storing Downloadable product sample data
             */
            if (isset($downloadableData['link'])) {
                foreach ($downloadableData['link'] as $linkItem) {
                    $linkId = '';
                    $linkfile = $samplefile = array();
                    $linkId = $linkItem['link_id'];
                    $linkfile[] = $linkTpath[$linkId];
                    $samplefile[] = $slinkTpath[$linkId];
                    $linkModel = Mage::getModel('downloadable/link')
                            ->setData($linkfile)
                            ->setLinkType($linkItem['type'])
                            ->setProductId($downloadProductId)
                            ->setWebsiteIds(array(Mage::app()->getStore($store)->getWebsiteId()))
                            ->setStoreId(0)
                            ->setSortOrder($linkItem['sort_order'])
                            ->setTitle($linkItem['title'])
                            ->setIsShareable($linkItem['is_shareable']);
                    if ($linkItem['type'] == 'url') {
                        $linkModel->setLinkUrl($linkItem['link_url']);
                    }
                    $linkModel->setPrice($linkItem['price']);
                    $linkModel->setNumberOfDownloads($linkItem['number_of_downloads']);
                    if (isset($linkItem['sample']['type'])) {
                        if ($linkItem['sample']['type'] == 'url') {
                            $linkModel->setSampleUrl($linkItem['sample']['url']);
                        }
                        $linkModel->setSampleType($linkItem['sample']['type']);
                    }
                    $sampleFile = '';
                    $sampleFile = Zend_Json::decode(json_encode($samplefile));
                    if (!empty($linkTpath[$linkId]) && $linkItem['type'] == 'file') {
                        $linkFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                Mage_Downloadable_Model_Link::getBaseTmpPath(), Mage_Downloadable_Model_Link::getBasePath(), $linkfile
                        );
                        $linkModel->setLinkFile($linkFileName);
                    }
                    if (!empty($slinkTpath[$linkId]) && isset($sampleFile) && $linkItem['sample']['type'] = 'file') {
                        $linkSampleFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                Mage_Downloadable_Model_Link::getBaseSampleTmpPath(), Mage_Downloadable_Model_Link::getBaseSamplePath(), $sampleFile
                        );
                        $linkModel->setSampleFile($linkSampleFileName);
                    }
                    Mage::helper('stylisthub/stylisthub')->saveDownLoadLink($linkModel);
                }
            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
        }
    }

    /**
     * Edit and save downloadable products
     *
     * @param type $downloadProductId
     * @param type $store Edit downloable product sample and link data
     *
     *  @return void
     */
    public function editDownloadableProductData($downloadProductId, $store) {

        /**
         *  Initilize downloadable product sample and link data
         */
        $sampleTpath = $linkTpath = $slinkTpath = array();
        $uploadsData = new Zend_File_Transfer_Adapter_Http();
        $filesDataArray = $uploadsData->getFileInfo();
        foreach ($filesDataArray as $key => $result) {
            if (isset($filesDataArray[$key]['name']) && (file_exists($filesDataArray[$key]['tmp_name']))) {
                $type = '';
                if (substr($key, 0, 5) == 'sampl') {
                    $tmpPath = Mage_Downloadable_Model_Sample::getBaseTmpPath();
                    $type = 'samples';
                } elseif (substr($key, 0, 5) == 'links') {
                    $tmpPath = Mage_Downloadable_Model_Link::getBaseTmpPath();
                    $type = 'links';
                } elseif (substr($key, 0, 5) == 'l_sam') {
                    $tmpPath = Mage_Downloadable_Model_Link::getBaseSampleTmpPath();
                    $type = 'link_samples';
                }
                if ($type == 'samples' || $type == 'links' || $type == 'link_samples') {
                    $result = array();
                    try {
                        /**
                         *  Initilize validate flag
                         */
                        $validateFlag = 0;
                        /**
                         *  Getting uploaded file extension type
                         */
                        $uploaderExtension = pathinfo($filesDataArray[$key]['name'], PATHINFO_EXTENSION);
                        $validateImage = array('jpg', 'jpeg', 'gif', 'png');
                        if (in_array($uploaderExtension, $validateImage)) {
                            $imgSize = getimagesize($filesDataArray[$key]['tmp_name']);
                            if (!$imgSize) {
                                $validateFlag = 1;
                            }
                        }

                        /**
                         *  Disallow php file for downloadable product
                         */
                        if ($uploaderExtension != 'php' && $validateFlag == 0) {
                            $uploader = new Varien_File_Uploader($key);
                            $uploader->setAllowRenameFiles(true);
                            $uploader->setFilesDispersion(true);
                            $result = $uploader->save($tmpPath);
                            $result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
                            $result['path'] = str_replace(DS, "/", $result['path']);
                            if (isset($result['file'])) {
                                $fullPath = rtrim($tmpPath, DS) . DS . ltrim($result['file'], DS);
                                Mage::helper('core/file_storage_database')->saveFile($fullPath);
                            }
                            $fileName = $filePath = $fileSize = $sampleNo = '';
                            $fileName = $uploader->getUploadedFileName();
                            $filePath = ltrim($result['file'], DS);
                            $fileSize = $result['size'];
                            if ($type == 'samples') {
                                $sampleNo = substr($key, 7);
                                $sampleTpath[$sampleNo] = array(
                                    'file' => $filePath,
                                    'name' => $fileName,
                                    'size' => $fileSize,
                                    'status' => 'new'
                                );
                            } elseif ($type == 'links') {
                                $sampleNo = substr($key, 6);
                                $linkTpath[$sampleNo] = array(
                                    'file' => $filePath,
                                    'name' => $fileName,
                                    'size' => $fileSize,
                                    'status' => 'new'
                                );
                            } elseif ($type == 'link_samples') {
                                $sampleNo = substr($key, 9);
                                $slinkTpath[$sampleNo] = array(
                                    'file' => $filePath,
                                    'name' => $fileName,
                                    'size' => $fileSize,
                                    'status' => 'new'
                                );
                            }
                        } else {
                            Mage::getSingleton('core/session')->addError($this->__('Disallowed file type.'));
                        }
                    } catch (Exception $e) {
                        Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                    }
                }
            }
        }

        /**
         *  Edit downloadable product sample data
         */
        try {

            /**
             *  Getting downloadable product sample collection
             */
            $downloadableSample = Mage::getModel('downloadable/sample')->getCollection()
                    ->addProductToFilter($downloadProductId)
                    ->addTitleToResult($store);
            $sampleDeleteItems = array();

            /**
             *  Removing all sample data
             */
            foreach ($downloadableSample as $sampleDelete) {
                $sampleDeleteItems[] = $sampleDelete->getSampleId();
            }
            if (!empty($sampleDeleteItems)) {
                Mage::getResourceModel('downloadable/sample')->deleteItems($sampleDeleteItems);
            }
            /**
             *  Getting downloadable product link collection
             */
            $downloadableLink = Mage::getModel('downloadable/link')->getCollection()
                    ->addProductToFilter($downloadProductId)
                    ->addTitleToResult($store);

            /**
             *  Removing all link data
             */
            $linkDeleteItems = array();
            foreach ($downloadableLink as $linkDelete) {
                $linkDeleteItems[] = $linkDelete->getLinkId();
            }
            if (!empty($linkDeleteItems)) {
                Mage::getResourceModel('downloadable/link')->deleteItems($linkDeleteItems);
            }

            /**
             *  Initilize downloadable product data
             */
            $downloadableData = $this->getRequest()->getPost('downloadable');
            if (isset($downloadableData['sample'])) {
                foreach ($downloadableData['sample'] as $sampleItem) {
                    $sampleId = '';
                    $sample = array();
                    $sampleId = $sampleItem['sample_id'];
                    if (empty($sampleId) && isset($sampleTpath[$sampleId])) {
                        $sample[] = $sampleTpath[$sampleId];
                    }
                    if (isset($sample)) {
                        $sampleModel = Mage::getModel('downloadable/sample');
                        $sampleModel->setData($sample)
                                ->setSampleType($sampleItem['type'])
                                ->setProductId($downloadProductId)
                                ->setStoreId($store)
                                ->setWebsiteIds(array(Mage::app()->getStore($store)->getWebsiteId()))
                                ->setTitle($sampleItem['title'])
                                ->setDefaultTitle(false)
                                ->setSortOrder($sampleItem['sort_order']);
                        if ($sampleItem['type'] == 'url') {
                            $sampleModel->setSampleUrl($sampleItem['sample_url']);
                        }
                        if (!empty($sampleTpath[$sampleId]) && $sampleItem['type'] == 'file') {
                            if ($sampleModel->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                                $sampleFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                        Mage_Downloadable_Model_Sample::getBaseTmpPath(), Mage_Downloadable_Model_Sample::getBasePath(), $sample
                                );
                            }
                            $sampleModel->setSampleFile($sampleFileName);
                        } else {
                            if (!empty($sampleItem['sample_file'])) {
                                $sampleFileName = $sampleItem['sample_file'];
                                $sampleModel->setSampleFile($sampleFileName);
                            }
                        }
                        Mage::helper('stylisthub/stylisthub')->saveDownLoadLink($sampleModel);
                    }
                }
            }
            /**
             *  Editing downloadable product link data
             */
            if (isset($downloadableData['link'])) {
                foreach ($downloadableData['link'] as $linkItem) {
                    $linkId = '';
                    $linkfile = $samplefile = array();
                    $linkId = $linkItem['link_id'];
                    if (isset($linkTpath[$linkId])) {
                        $linkfile[] = $linkTpath[$linkId];
                    }
                    if (isset($slinkTpath[$linkId])) {
                        $samplefile[] = $slinkTpath[$linkId];
                    }
                    $linkModel = Mage::getModel('downloadable/link')
                            ->setData($linkfile)
                            ->setLinkType($linkItem['type'])
                            ->setProductId($downloadProductId)
                            ->setWebsiteIds(array(Mage::app()->getStore($store)->getWebsiteId()))
                            ->setStoreId(0)
                            ->setSortOrder($linkItem['sort_order'])
                            ->setTitle($linkItem['title'])
                            ->setIsShareable($linkItem['is_shareable']);
                    if ($linkItem['type'] == 'url') {
                        $linkModel->setLinkUrl($linkItem['link_url']);
                    }
                    $linkModel->setPrice($linkItem['price']);
                    $linkModel->setNumberOfDownloads($linkItem['number_of_downloads']);
                    if (isset($linkItem['sample']['type']) && $linkItem['sample']['type'] == 'url') {
                        $linkModel->setSampleUrl($linkItem['sample']['url']);
                        $linkModel->setSampleType($linkItem['sample']['type']);
                    }

                    $sampleFile = '';
                    $sampleFile = Zend_Json::decode(json_encode($samplefile));
                    if (!empty($linkTpath[$linkId]) && $linkItem['type'] == 'file') {
                        $linkFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                Mage_Downloadable_Model_Link::getBaseTmpPath(), Mage_Downloadable_Model_Link::getBasePath(), $linkfile
                        );
                        $linkModel->setLinkFile($linkFileName);
                    } else {
                        if (!empty($linkItem['link_file'])) {
                            $linkFileName = $linkItem['link_file'];
                            $linkModel->setLinkFile($linkFileName);
                        }
                    }
                    if (!empty($slinkTpath[$linkId]) && isset($sampleFile) && $linkItem['sample']['type'] = 'file') {
                        $linkSampleFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                Mage_Downloadable_Model_Link::getBaseSampleTmpPath(), Mage_Downloadable_Model_Link::getBaseSamplePath(), $sampleFile
                        );
                        $linkModel->setSampleFile($linkSampleFileName);
                    } else {
                        if (!empty($linkItem['link_sample_file'])) {
                            $linkSampleFileName = $linkItem['link_sample_file'];
                            $linkModel->setSampleFile($linkSampleFileName);
                        }
                    }
                    Mage::helper('stylisthub/stylisthub')->saveDownLoadLink($linkModel);
                }
            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
        }
    }

    /**
     * Manage Deals products by stylist
     *
     *  @return void
     */
    public function managedealsAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Manage Deals products by stylist
     *
     *  @return void
     */
    public function deletesingledealAction() {
        $entityId = $this->getRequest()->getParam('id');
        Mage::getModel('catalog/product')->load($entityId)->setSpecialFromDate('')->setSpecialToDate('')->setSpecialPrice('')->save();
        Mage::getSingleton('core/session')->addSuccess($this->__("Product Deal Deleted Successfully"));
        $this->_redirect('*/product/managedeals/');
        return true;
    }

    /**
     * Function to check availability of sku
     *
     *  @return int
     */
    public function checkskuAction() {
        $inputSku = $this->getRequest()->getParam('sku');
        $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('sku', array("like" => $inputSku));
        $count = count($collection);
        echo $count;
        return true;
    }
    /**
     * Function to display the product name for price comparison
     *
     * Return the product name according to the search string
     * @return varchar
     */
    public function comparepriceAction() {
        $inputWord = $this->getRequest()->getParam('q');
        $productId = $this->getRequest()->getParam('id');
        $productName = array();
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToFilter('name', array("like" => '%'.$inputWord.'%'));
        if($productId!=''){
        	$collection->addAttributeToFilter('entity_id',array('neq'=>$productId));
        }
        echo '<ul>';
        foreach($collection as $_collection)
    	{
            echo $productName = '<li id="'.$_collection->getId().'">'.$_collection->getName().'</li>';
        }
        echo '</ul>';
    }
    /**
     * Function to display the view all compare price products
     *
     * @return void
     */
    public function comparestylistpriceAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
}
