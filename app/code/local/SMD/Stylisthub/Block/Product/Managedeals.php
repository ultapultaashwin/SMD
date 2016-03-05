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
 * This Class is used for manage product deals functionality
 */
class SMD_Stylisthub_Block_Product_Managedeals extends Mage_Core_Block_Template {
    /**
     * Collection for manage product deals
     *
     * @return \SMD_Stylisthub_Block_Product_Managedeals
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $manage_collection = $this->manageProducts();
        $this->setCollection($manage_collection);
        $pager = $this->getLayout()
                ->createBlock('page/html_pager', 'my.pager')
                ->setCollection($manage_collection);
        $pager->setAvailableLimit(array(10 => 10, 20 => 20, 30 => 30, 50 => 50));
        $pager->setLimit(20);
        $this->setChild('pager', $pager);
        return $this;
    }

    /**
     * Function to get the product details
     *
     * This function will return the array of product collection
     * @return array
     */
    public function manageProducts() {
        $entityIds = $this->getRequest()->getParam('id');
        $delete = $this->getRequest()->getPost('multi');
        if (count($entityIds) > 0 && $delete == 'delete') {
            foreach ($entityIds as $_entity_ids) {
                Mage::helper('stylisthub/stylisthub')->deleteDeal($_entity_ids);
                Mage::getSingleton('core/session')->addSuccess($this->__("Selected Products Deal are Deleted Successfully"));
            }
        }
        $filterId = $this->getRequest()->getParam('filter_id');
        $filterName = $this->getRequest()->getParam('filter_name');
        $filterPrice = $this->getRequest()->getParam('filter_price');
        $filterStatus = $this->getRequest()->getParam('filter_status');
        $todayDate = date('m/d/y');
        $tomorrow = mktime(0, 0, 0, date('m'), date('d') + 1, date('y'));
        $tomorrowDate = date('m/d/y', $tomorrow);
        $cusId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $products = Mage::getModel('catalog/product')->getCollection();
        $products->addAttributeToSelect('*');
        $products->addAttributeToFilter('stylist_id', array('eq' => $cusId));
        if ($filterId != '') {
            $products->addAttributeToFilter('entity_id', array('eq' => $filterId));
        }
        if ($filterName != '') {
            $products->addAttributeToFilter('name', array('like' => '%' . $filterName . '%'));
        }
        if ($filterPrice != '') {
            $products->addAttributeToFilter('price', array('eq' => $filterPrice));
        }
        if ($filterStatus != 0) {
            $products->addAttributeToFilter('status', array('eq' => $filterStatus));
        }
        $products->addAttributeToSort('entity_id', 'DESC');
        $products->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate));
        $products->addAttributeToFilter('special_to_date', array('or' => array(
                0 => array('date' => true, 'from' => $tomorrowDate),
                1 => array('is' => new Zend_Db_Expr('null')))
                ), 'left');
        return $products;
    }

    /**
     * Function to get pagination
     *
     * Return pagination for collection
     * @return array
     */
    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    /**
     * Function to get product multi select action url
     *
     * This function will return the manage deals redirect url
     * return string
     */
    public function getmultiselectUrl() {
        return Mage::getUrl('stylisthub/product/managedeals');
    }

}
