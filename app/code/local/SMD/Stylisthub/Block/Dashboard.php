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
 * This file is used to display stylist dashboard with functionalities like Total sales, Average orders,
 * Last five orders, Most viewed products and Sales report
 */
class SMD_Stylisthub_Block_Dashboard extends Mage_Core_Block_Template
{
    /**
     * Function to get profile url
     *
     * Return the stylist profile url
     * @return string
     */
    function profileUrl()
    {
        return  Mage::getUrl('stylisthub/stylist/addprofile');
    }
   /**
    * Function to get most viewed product information
    *
    * Return the Most viewed products as array
    * @return array
    */
   public function mostViewed(){
       $storeId    = Mage::app()->getStore()->getId();
       $products   = Mage::getResourceModel('reports/product_collection')
                    ->addOrderedQty()
                    ->addAttributeToSelect('*')
                    ->setStoreId($storeId)
                    ->addStoreFilter($storeId)
                    ->addViewsCount();
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        $products->setPageSize(5)->setCurPage(1);
        return $products;
   }
   /**
    * Getting sales report collection
    *
    * Passed the From date as $dbFrom to sort the sales orders
    * @param int $dbFrom
    *
    * Passed the To date as $dbTo to sort the sales orders
    * @param int $dbTo
    *
    * Passed the stylist id as $id to get particular stylist orders
    * @param int $id
    *
    * Return commission collection as array
    * @return array
    *
    */
    public function advancedSalesReportCollection($dbFrom,$dbTo,$id) {
        $selectFilter = '';
        $data           = $this->getRequest()->getPost();
        if(isset($data['select_filter'])){
            $selectFilter  = $data['select_filter'];
        }
        if($selectFilter=='today')
        {
          $from    = date("Y-m-d", strtotime("-1 days"));
          $to      = date("Y-m-d", strtotime("mid"));
          $dbFrom    = Mage::getModel('core/date')->gmtDate(null, strtotime($from));
          $dbTo      = Mage::getModel('core/date')->gmtDate(null, strtotime($to));
        }
        $collection = Mage::getModel('stylisthub/commission')->getCollection()
                        ->addFieldToFilter('order_status','complete')
                        ->addFieldToFilter('stylist_id',$id)
                        ->addFieldToFilter('created_at', array('from' =>$dbFrom, 'to'=>$dbTo));
        return $collection;
    }
}
