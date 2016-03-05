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
 * This file is used to calculate number of products added by a particular stylist *
 */
class SMD_Stylisthub_Block_Adminhtml_Renderersource_Totalproducts extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Function to render total number of stylist products
     *
     * Return the total number of product count
     * @return int
     */
    public function render(Varien_Object $row) {
        $id = $row->getData();
        foreach ($id as $_id) {
            $stylistProduct = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('stylist_id', $_id);
            $redirectUrl = $this->getUrl('stylisthubadmin/adminhtml_products/index/id/' . $_id);
            $productCount = $stylistProduct->getSize();
            if ($productCount > 0) {
                $url = "<a href='" . $redirectUrl . "'>" . $productCount . "</a>";
            } else {
                $url = $productCount;
            }
            return $url;
        }
    }

}
