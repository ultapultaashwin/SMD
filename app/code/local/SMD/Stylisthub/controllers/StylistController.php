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
 * This file contains stylist login/ registration, stylist profile page functionality
 */
class SMD_Stylisthub_StylistController extends Mage_Core_Controller_Front_Action {

    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('loginPost', 'createpost');

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Check whether VAT ID validation is enabled
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return bool
     */
    protected function _isVatValidationEnabled($store = null) {
        return Mage::helper('customer/address')->isVatValidationEnabled($store);
    }

    /**
     * Function to display stylist login and registration page
     *
     * Checking license key and stylist logged in or not
     * @return void
     */
    public function indexAction() {

            if (!$this->_getSession()->isLoggedIn()) {
                Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
                $this->_redirect('*/*/login');
                return;

            $this->loadLayout();
            $this->renderLayout();
        }
    }

    /**
     * Function to display login page
     *
     * Checking license key and stylist logged in or not
     * @return void
     */
    public function loginAction() {

            if ($this->_getSession()->isLoggedIn()) {
                $this->_redirect('stylisthub/stylist/dashboard');
                return;
            }
            $this->loadLayout();
            $this->renderLayout();

    }

    /**
     * Function to post login page data's
     *
     * Checking username and password for log in and redirect to stylist/customer account
     * @return void
     */
    public function loginPostAction() {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('stylisthub/stylist/dashboard');
            return;
        }
        $session = $this->_getSession();
        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $customer = Mage::getModel('customer/customer');
                    $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                            ->loadByEmail($login['username']);
                    $customerGroupid = $customer->getGroupId();
                    $groupId = Mage::helper('stylisthub')->getGroupId();
                    if ($customerGroupid != $groupId) {
                        Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
                        $this->_redirect('*/*/login');
                        return;
                    }
                    $customerStatus = $customer->getCustomerstatus();
                    if ($customerStatus == 2 || $customerStatus == 0) {
                        Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required for Stylist Account'));
                        $this->_redirect('*/*/login');
                        return;
                    }
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $session->addError($message);
                    $session->setUsername($login['username']);
                }
            } else {
                $session->addError($this->__('Login and password are must.'));
            }
        }
        $this->_redirect('stylisthub/stylist/dashboard');
    }

    /**
     * Function to display registration page
     *
     * Display stylist/customer account registration form
     * @return void
     */
    public function createAction() {

            $this->loadLayout();
            $this->renderLayout();

    }

    /**
     * Function to post the registration page data's
     *
     * Get and validate stylist/customer account registration form
     * @return void
     */
    public function createPostAction() {
        $adminApproval = Mage::getStoreConfig('stylisthub/admin_approval_stylist_registration/need_approval');
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if ($this->getRequest()->isPost()) {
            $errors = array();
            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }
            $groupId = Mage::helper('stylisthub')->getGroupId();
            $customer->setGroupId($groupId);

            if ($adminApproval == 1) {
                $customer->setCustomerstatus('0');
            } else {
                $customer->setCustomerstatus('1');
            }
            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')
                    ->setEntity($customer);
            $customerData = $customerForm->extractData($this->getRequest());
            $customer->getGroupId();
            if ($this->getRequest()->getPost('create_address')) {
                /* @var $address Mage_Customer_Model_Address */
                $address = Mage::getModel('customer/address');
                /* @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('customer_register_address')
                        ->setEntity($address);
                $addressData = $addressForm->extractData($this->getRequest(), 'address', false);
                $addressErrors = $addressForm->validateData($addressData);
                if ($addressErrors === true) {
                    $address->setId(null)
                            ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                            ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
                    $addressForm->compactData($addressData);
                    $customer->addAddress($address);

                    $addressErrors = $address->validate();
                    if (is_array($addressErrors)) {
                        $errors = array_merge($errors, $addressErrors);
                    }
                } else {
                    $errors = array_merge($errors, $addressErrors);
                }
            }
            try {
                $customerErrors = $customerForm->validateData($customerData);
                if ($customerErrors !== true) {
                    $errors = array_merge($customerErrors, $errors);
                } else {
                    $customerForm->compactData($customerData);
                    $customer->setPassword($this->getRequest()->getPost('password'));
                    $customer->setPasswordConfirmation($this->getRequest()->getPost('confirmation'));
                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($customerErrors, $errors);
                    }
                }
                $validationResult = count($errors) == 0;
                if (true === $validationResult) {
                    $customerId = $customer->save()->getId();
                    if ($adminApproval == 1) {
                        Mage::getModel('stylisthub/stylistprofile')->adminApproval($customerId);
                        Mage::dispatchEvent('customer_register_success', array('account_controller' => $this, 'customer' => $customer));
                        Mage::getSingleton('core/session')->addSuccess($this->__('Admin Approval is required. Please wait until admin confirms your Stylist Account'));
                        $this->_redirect('*/*/login');
                        return;
                    } else {

                        Mage::getModel('stylisthub/stylistprofile')->newStylist($customerId);
                        Mage::dispatchEvent('customer_register_success', array('account_controller' => $this, 'customer' => $customer)
                        );
                        $session->setCustomerAsLoggedIn($customer);
                        $session->renewSession();
                        $url = $this->_welcomeCustomer($customer);
                        $this->_redirectSuccess($url);
                    }
                } else {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->addError($errorMessage);
                        }
                    } else {
                        $session->addError($this->__('Invalid customer data'));
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $url = Mage::getUrl('customer/account/forgotpassword');
                    Mage::getSingleton('core/session')->addError($this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url));
                    $this->_redirect('*/*/login');
                } else {
                    $message = $e->getMessage();
                }
                $session->addError($message);
            } catch (Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost())
                        ->addException($e, $this->__('Customer details not saved.'));
            }
        }

        $this->_redirectError(Mage::getUrl('*/*/login', array('_secure' => true)));
    }

    /**
     * Function to display welcome message
     *
     * Display welcome message for stylist/ customer
     * @return void
     */
    protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false) {
        $this->_getSession()->addSuccess(
                $this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
        );
        if ($this->_isVatValidationEnabled()) {
            /**
             * Show corresponding VAT message to customer
             */
            $configAddressType = Mage::helper('customer/address')->getTaxCalculationAddressType();
            $userPrompt = '';
            switch ($configAddressType) {
                case Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you shipping address for proper VAT calculation', Mage::getUrl('customer/address/edit'));
                    break;
                default:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you billing address for proper VAT calculation', Mage::getUrl('customer/address/edit'));
            }
            $this->_getSession()->addSuccess($userPrompt);
        }
        $customer->sendNewAccountEmail(
                $isJustConfirmed ? 'confirmed' : 'registered', '', Mage::app()->getStore()->getId()
        );
        $successUrl = Mage::getUrl('*/*/index', array('_secure' => true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }

    /**
     * Function to display add profile form
     *
     * Display Add profile form url
     * @return void
     */
    function addprofileAction() {

        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Function to save stylist profile information
     *
     * Save Add profile form
     * @return void
     */
    function saveprofileAction() {
        $data = $this->getRequest()->getPost();
        $stylistId = $storeName = $storeLogo = $description = $metaKeyword = $metaDescription = '';
        $bankPayment = $paypalId = $remove = $contact = $showProfile = '';
        $uploadsData = new Zend_File_Transfer_Adapter_Http();
        $filesDataArray = $uploadsData->getFileInfo();

		 $commission = $data['commission'];
        $stylistId = $data['stylist_id'];
        $storeName = $data['store_name'];
        $storeLogo = $filesDataArray['store_logo']['name'];
        //$country            = $data['country'];
        $description = $data['description'];
        $metaKeyword = $data['meta_keyword'];
        $metaDescription = $data['meta_description'];
        $twitter_id         = $data['twitter_id'];
        $facebook_id        = $data['facebook_id'];
        $bankPayment = $data['bank_payment'];
        $paypalId = $data['paypal_id'];
        if (isset($data['remove'])) {
            $remove = $data['remove'];
        }
        $contact = $data['contact'];
        if (isset($data['show_profile'])) {
            $showProfile = $data['show_profile'];
        }
        $basedir = Mage::getBaseDir('media');
        $file = new Varien_Io_File();
        /**
         * create a folder to save the logo and banner images in media folder
         */
        if (!is_dir($basedir . '/stylistimage')) {
            $file->mkdir($basedir . '/stylistimage');
        }
        if (isset($filesDataArray['store_logo']['name']) && (file_exists($filesDataArray['store_logo']['tmp_name']))) {
            try {
                $uploader = new Varien_File_Uploader($filesDataArray['store_logo']);
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->addValidateCallback('catalog_product_image', Mage::helper('catalog/image'), 'validateUploadFile');
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $path = $basedir . DS . 'stylistimage';
                $uploader->save($path, $filesDataArray['store_logo']['name']);
                $imagesPath = $uploader->getUploadedFileName();
            } catch (Exception $e) {
                /**
                 * Display error message for images upload
                 */
                Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
            }
            if (!is_dir($basedir . '/stylisthub/resized')) {
                $file->mkdir($basedir . '/stylisthub/resized');
            }
            $imageUrlLogo = Mage::getBaseDir('media') . DS . 'stylistimage' . DS . $imagesPath;
            $imageResizedLogo = Mage::getBaseDir('media') . DS . 'stylisthub' . DS . 'resized' . DS . $imagesPath;
            if (file_exists($imageUrlLogo) && !file_exists($imageResizedLogo)) {
                $imageObj = new Varien_Image($imageUrlLogo);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(False);
                $imageObj->keepFrame(FALSE);
                 $imageObj->resize(95, 62);
                $imageObj->save($imageResizedLogo);
            }
        }
        $collection = Mage::getModel('stylisthub/stylistprofile')->load($stylistId, 'stylist_id');
        $getId = $collection->getId();
        try {
            if ($getId) {
                /**
                 * Update form input data in database
                 */
                $collection = Mage::getModel('stylisthub/stylistprofile')->load($stylistId, 'stylist_id');
                $collection->setStylistId($stylistId);
                $collection->setStoreTitle($storeName);
                if (!empty($storeLogo)) {
                    $collection->setStoreLogo($imagesPath);
                }
                if ($remove == 1) {
                    $collection->setStoreLogo('');
                }

                // $collection->setCountry($country);
                $collection->setDescription($description);
                $collection->setMetaKeyword($metaKeyword);
                $collection->setMetaDescription($metaDescription);
                 $collection->setTwitterId($twitter_id);
                $collection->setFacebookId($facebook_id);
                $collection->setContact($contact);
                $collection->setBankPayment($bankPayment);
                $collection->setPaypalId($paypalId);
				$collection->setCommission($commission);
                if ($showProfile) {
                    $collection->setShowProfile($showProfile);
                } else {
                    $collection->setShowProfile('');
                }
                $collection->save();

                $targetPath = 'stylisthub/stylist/displaystylist/id/' . $stylistId;
                $mainUrlRewrite = Mage::getModel('core/url_rewrite')->load($targetPath, 'target_path');
                $getRequestPath = $mainUrlRewrite->getRequestPath();
                $newGetRequestPath = Mage::getUrl($getRequestPath);
                if ($newGetRequestPath == Mage::getBaseUrl()) {
                    Mage::getModel('stylisthub/stylistprofile')->addRewriteUrl($storeName, $stylistId);
                }
                Mage::getSingleton('core/session')->addSuccess($this->__('Your profile information is saved successfully'));
                $this->_redirect('stylisthub/stylist/addprofile');
                return true;
            } else {
                /**
                 * insert form input data in database
                 */
                $collection = Mage::getModel('stylisthub/stylistprofile');
                $collection->setStylistId($stylistId);
                $collection->setStoreTitle($storeName);
                $collection->setStoreLogo($imagesPath);
                //$collection->setCountry($country);
                $collection->setContact($contact);
                $collection->setDescription($description);
                $collection->setMetaKeyword($metaKeyword);
                $collection->setMetaDescription($metaDescription);
                $collection->setTwitterId($twitter_id);
                $collection->setFacebookId($facebook_id);
                $collection->setBankPayment($bankPayment);
                $collection->setPaypalId($paypalId);
                $collection->setShowProfile($showProfile);
                $collection->save();
                /**
                 * url management
                 */
                Mage::getModel('stylisthub/stylistprofile')->addRewriteUrl($storeName, $stylistId);
                Mage::getSingleton('core/session')->addSuccess($this->__('Your profile information is saved successfully'));
                $this->_redirect('stylisthub/stylist/addprofile');
                return true;
            }
        } catch (Exception $e) {
            /**
             * Error message redirect to create new product page
             */
            Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
            $this->_redirect('stylisthub/stylist/addprofile');
        }
    }

    /**
     * Function to display change buyer into stylist form
     *
     * change buyer in to stylist form
     * @return void
     */
    function changebuyerAction() {

        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
            $this->_redirect('customer/account/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Function to change buyer into stylist
     *
     * convert and change group id from buyer into stylist
     * @return void
     */
    function becomestylistAction() {
        $adminApproval = Mage::getStoreConfig('stylisthub/admin_approval_stylist_registration/need_approval');
        $approval = 0;
        if ($adminApproval == 1) {
            $approval = 0;
        } else {
            $approval = 1;
        }
        $getGroupId = Mage::helper('stylisthub')->getGroupId();
        $customer = Mage::getSingleton("customer/session")->getCustomer();
        $customer->setGroupId($getGroupId)->save();
        $customerId = $customer->getId();
        $model = Mage::getModel('customer/customer')->load($customerId);
        $model->setCustomerstatus($approval)
                ->save();
        if ($adminApproval == 1) {
            Mage::getModel('stylisthub/stylistprofile')->adminApproval($customerId);
            Mage::getSingleton('core/session')->addSuccess($this->__('Admin Approval is required. Please wait until admin confirms your Stylist Account'));
        } else {
            Mage::getModel('stylisthub/stylistprofile')->newStylist($customerId);
            Mage::getSingleton('core/session')->addSuccess($this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName()));
        }
        $this->_redirect('customer/account');
    }

    /**
     * Function to display stylist profile information
     *
     * Display stylist profile page
     * @return void
     */
    function displaystylistAction() {

        $this->loadLayout();
        $this->renderLayout();
        $id = $this->getRequest()->getParam('id');
        $stylistPage = Mage::getModel('stylisthub/stylistprofile')->collectprofile($id);
        $head = $this->getLayout()->getBlock('head');
        $head->setTitle($stylistPage->getStoreTitle());
        $head->setKeywords($stylistPage->getMetaKeyword());
        $head->setDescription($stylistPage->getMetaDescription());
    }

    /**
     * Function to display stylist dashboard
     *
     * Display stylist dashboard page
     * @return void
     */
    function dashboardAction() {

        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Stylist Account to access this page'));
            $this->_redirect('stylisthub/stylist/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Function to display top stylist
     *
     * Display stylist top stylist page
     * @return void
     */
    function topstylistAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Function to display All stylist information
     *
     * Display all stylist page
     * @return void
     */
    function allstylistAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Function to display category wise stylist products
     *
     * Display category wise stylist products
     * @return void
     */
    function categorylistAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Function to save reviews and ratings
     *
     * Save stylist review
     * @return void
     */
    function savereviewAction() {
        $data = $this->getRequest()->getPost();
        $id = $data['stylist_id'];
        $url = Mage::getModel('stylisthub/stylistreview')->backUrl($id);
        $saveReview = Mage::getModel('stylisthub/stylistreview')->saveReview($data);
        if ($saveReview == 1) {
            Mage::getSingleton('core/session')->addSuccess($this->__('Your review has been accepted for moderation.'));
            $this->_redirectUrl($url);
        } else {
            Mage::getSingleton('core/session')->addError($this->__('Sorry there was an error occured while submiting your review'));
            $this->_redirectUrl($url);
        }
    }

    /**
     * Function to display all reviews in stylist profile page
     *
     * Display stylist review page
     * @return void
     */
    function allreviewAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Function to display customer review to stylist
     *
     * Display customer review page
     * @return void
     */
    function customerreviewAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Function to display vacation mode to stylist
     *
     * Display vacation mode page
     * @return void
     */
    function vacationmodeAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Function to save vacation mode to stylist
     *
     * Display vacation mode save page
     * @return void
     */
    function vacationmodesaveAction() {
        $vacationStatus = $this->getRequest()->getParam('vacation_status');
        $vacationMessage = $this->getRequest()->getParam('vacation_message');
        $disableProducts = $this->getRequest()->getParam('disable_products');
        $dateFrom = $this->getRequest()->getParam('date_from');
        $dateTo = $this->getRequest()->getParam('date_to');
        $currentDate = Mage::getModel('core/date')->date('Y-m-d');
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $stylist = Mage::getSingleton('customer/session')->getCustomer();
            $stylistId = $stylist->getId();
            $product = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('stylist_id', $stylistId);
            $productId = array();
            foreach ($product as $_product) {
                $productId[] = $_product->getId();
            }
            $stylistInfo = Mage::getModel('stylisthub/vacationmode')->load($stylistId, 'stylist_id');
            $getId = $stylistInfo->getId();
            if ($getId) {
                $updateExisting = Mage::getModel('stylisthub/vacationmode')->load($stylistId, 'stylist_id');
                $updateExisting->setVacationMessage($vacationMessage);
                $updateExisting->setVacationStatus($vacationStatus);
                $updateExisting->setProductDisabled($disableProducts);
                $updateExisting->setDateFrom($dateFrom);
                $updateExisting->setDateTo($dateTo);
                $updateExisting->setStylistId($stylistId);
                $updateExisting->save();

            } else {
                $insertNew = Mage::getModel('stylisthub/vacationmode');
                $insertNew->setVacationMessage($vacationMessage);
                $insertNew->setVacationStatus($vacationStatus);
                $insertNew->setProductDisabled($disableProducts);
                $insertNew->setDateFrom($dateFrom);
                $insertNew->setDateTo($dateTo);
                $insertNew->setStylistId($stylistId);
                $insertNew->save();
            }
            if($vacationStatus == 0){
                if ($disableProducts == 0) {
                    foreach ($productId as $_productId) {
                        Mage::getModel('catalog/product')->load($_productId)->setStatus(2)->save();
                    }
                } elseif ($disableProducts == 1) {
                	foreach ($productId as $_productId) {
                        Mage::getModel('catalog/product')->load($_productId)->setStatus(1)->save();
                    }
                }

            } else {
            	foreach ($productId as $_productId) {
            		Mage::getModel('catalog/product')->load($_productId)->setStatus(1)->save();
            	}
            }
            Mage::getSingleton('core/session')->addSuccess($this->__('Your vacation mode information is saved successfully'));
            $this->_redirect('stylisthub/stylist/vacationmode');
            return true;
        }
        }
}
