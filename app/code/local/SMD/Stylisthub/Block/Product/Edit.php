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
 * Edit product information
 */
class SMD_Stylisthub_Block_Product_Edit extends Mage_Core_Block_Template {

    /**
     * Initilize layout and set page title
     *
     * Return the page title
     * @return varchar
     */
    protected function _prepareLayout() {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('stylisthub')->__('Edit Product'));
        return parent::_prepareLayout();
    }

    /**
     * Product edit action
     *
     * Return edit post action url
     * @return string
     */
    public function editProductAction() {
        return Mage::getUrl('stylisthub/product/editpost');
    }

    /**
     * Get product data collection
     *
     * Passed the product id to get product details
     * @param int $productId
     *
     * Return product details as array
     * @return array
     */
    public function getProductData($productId) {
        return Mage::getModel('catalog/product')->load($productId);
    }

    /**
     * Display Category tree
     *
     * Passed the category data
     * @param array $categories
     *
     * Passed the category id to get particular category info
     * @param int $categoryIds
     *
     * Return category tree as array
     * @return array
     */
    public function getCategoriesTree($categories, $categoryIds) {
        $array = '<ul class="category_ul">';
        foreach ($categories as $category) {
            $catId = $category->getId();
            $cat = Mage::helper('stylisthub/stylisthub')->getCategoryData($catId);
            $count = $cat->getProductCount();
            $catChecked = '';
            if (in_array($category->getId(), $categoryIds)) {
                $catChecked = 'checked';
            }
            if ($category->hasChildren()) {
                $array .= '<li class="level-top  parent"><a href="javascript:void(0);"><span class="end-plus"></span></a><span class="last-collapse"><input id="cat' . $category->getId() . '" type="checkbox" name="category_ids[]" ' . $catChecked . ' value="' . $category->getId() . '"><label for="cat' . $category->getId() . '">' . $category->getName() . '<span>(' . $count . ')</span>' . '</label></span>';
            } else {
                $array .= '<li class="level-top  parent"><a href="javascript:void(0);"><span class="empty_space"></span></a><input id="cat' . $category->getId() . '" type="checkbox" name="category_ids[]" ' . $catChecked . ' value="' . $category->getId() . '"><label for="cat' . $category->getId() . '">' . $category->getName() . '<span>(' . $count . ')</span>' . '</label>';
            }
            if ($category->hasChildren()) {
                $children = Mage::getModel('catalog/category')->getCategories($category->getId());
                $array .= $this->getCategoriesTree($children, $categoryIds);
            }
            $array .= '</li>';
        }
        return $array . '</ul>';
    }

    /**
     * Getting selected product categories id
     *
     * Category data are passed to display the category id
     * @param array $categoryArray
     *
     * Return category id
     * @return array
     */
    public function getCategoryIds($categoryArray) {
        foreach ($categoryArray as $key) {
            foreach ($key as $value) {
                $categoryIds[] = $value;
            }
        }
        return $categoryIds;
    }

}
