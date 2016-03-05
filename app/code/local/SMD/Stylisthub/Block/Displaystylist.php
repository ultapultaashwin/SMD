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
class SMD_Stylisthub_Block_Displaystylist extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
       $id           = $this->getRequest()->getParam('id');
       $stylistPage   = Mage::getModel('stylisthub/stylistprofile')->collectprofile($id);
       /**
        * set Meta information for the stylist
        */
       $head         = $this->getLayout()->getBlock('head');
                        $head->setTitle($stylistPage->getStoreTitle());
                        $head->setKeywords($stylistPage->getMetaKeyword());
                        $head->setDescription($stylistPage->getMetaDescription());
      $displayCollection = $this->categoryProducts();
                        $this->setCollection($displayCollection);
       $pager        = $this->getLayout()
                        ->createBlock('page/html_pager', 'my.pager')
                        ->setCollection($displayCollection);
                      $pager->setAvailableLimit(array(10 => 10,20 => 20,30=>30,50=>50));
                 $pager->setLimit(20);
       $this->setChild('pager', $pager);
       return $this;
    }
   /**
     * Function to get the collection according to pagination
     *
     * Return the Stylist product collection
     * @return array
     */
    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

   /**
     * Function to get the Stylist products
     *
     * Return the Stylist product collection
     * @return array
     */
    function stylistproduct($stylistid){
         $collection = Mage::getModel('catalog/product')->getCollection()
                        ->addFieldToFilter('stylist_id',$stylistid)
                        ->joinField('category_id',
                            Mage::getConfig()->getTablePrefix().'catalog_category_product',
                            'category_id',
                            'product_id=entity_id',
                            null,
                            'right');
        $value       = $collection->getData('category_id');
        return $value;
    }

    /**
     * Get category products
     *
     * Return the category product collection
     * @return array
     */
    function categoryProducts(){
        $displayCatProduct = $this->getRequest()->getParam('category_name');
        $sortProduct        = $this->getRequest()->getParam('sorting');
        $id                  = $this->getRequest()->getParam('id');
        $catagoryModel      = Mage::getModel('catalog/category')->load($displayCatProduct);
        $collection          = Mage::getResourceModel('catalog/product_collection');
                                $collection->addCategoryFilter($catagoryModel); //category filter
                                $collection->addAttributeToFilter('status',1); //only enabled product
                                $collection->addAttributeToSelect('*'); //add product attribute to be fetched
                                $collection->addAttributeToFilter('stylist_id',$id);
                                $collection->addStoreFilter();
                                $collection->addAttributeToSort($sortProduct);
        return $collection;
    }

    /**
     * Get category Url
     *
     * Return the category link url
     * @return string
     */
    function getCategoryUrl($customerId,$id){
        return  Mage::getUrl('stylisthub/stylist/categorylist',array('id'=>$id,'cat'=>$customerId));
    }

    /**
     * Get url for review form
     *
     * Passed the stylist id to get the review collection
     * @param int $id
     *
     * Customer id is passed to get the particular customer reviews
     * @param int $customerId
     *
     * Product id is passed to get the particular products reviews
     * @param int $productId
     *
     * Return the average rating of particular stylist
     * @return int
     */
    function reviewUrl($customerId,$id,$productId){
        return  Mage::getUrl('stylisthub/stylist/reviewform',array('id'=>$id,'cus'=>$customerId,'product'=>$productId));
    }

    /**
     * Get login url if customer not logged in
     *
     * Return the customer login url
     * @return string
     */
    function loginUrl(){
        return  Mage::getUrl('customer/account/login/');
    }

    /**
     * Get all reviews link
     *
     * Passed the stylist id to get the review collection
     * @param int $id
     *
     * Customer id is passed to get the particular customer reviews
     * @param int $customerId
     *
     * Product id is passed to get the particular products reviews
     * @param int $productId
     *
     * Return the average rating of particular stylist
     * @return int
     */
    function getAllreview($customerId,$id,$productId){
        return  Mage::getUrl('stylisthub/stylist/allreview',array('id'=>$id,'cus'=>$customerId,'product'=>$productId));
  }
  /**
   * Calculating average rating for each stylist
   *
   * Passed the stylist id to get the review collection
   * @param int $id
   *
   * Return the average rating of particular stylist
   * @return int
   */
  public function averageRatings($id) {
  	/**
  	 *  Review Collection to retrive the ratings of the stylist
  	 */
  	$storeId = Mage::app()->getStore()->getId();
  	$reviews = Mage::getModel('stylisthub/stylistreview')->getCollection()
  	->addFieldToFilter('stylist_id', $id)
  	->addFieldToFilter('status', 1)
  	->addFieldToFilter('store_id', $storeId);
  	/**
  	 *  Calculate average ratings
  	 */
  	$ratings = array();
  	$avg = 0;
  	if (count($reviews) > 0) {
  		foreach ($reviews as $review) {
  			$ratings[] = $review->getRating();
  		}
  		$count = count($ratings);
  		$avg = array_sum($ratings) / $count;
  	}
  	return $avg;
  }
  public function reviewCount($id){
  	/**
  	 *  Review Collection to retrive the ratings of the stylist
  	 */
  	$storeId = Mage::app()->getStore()->getId();
  	$reviews = Mage::getModel('stylisthub/stylistreview')->getCollection()
  	->addFieldToFilter('stylist_id', $id)
  	->addFieldToFilter('status', 1)
  	->addFieldToFilter('store_id', $storeId);
  	return count($reviews);
  }
}
