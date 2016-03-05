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
 * Renderer to display ordered product information
 */
class SMD_Stylisthub_Block_Adminhtml_Renderersource_OrderProductdetails extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Function to get product order details
     *
     * Return the product order details
     * @return string
     */
    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        $commission = Mage::getModel('stylisthub/commission')->load($value);
        $getProductId = $commission->getProductId();
        $itemItem = Mage::getModel('catalog/product')->load($getProductId);
        $pId = Mage::helper("adminhtml")->getUrl('adminhtml/catalog_product/edit', array('id' => $getProductId));
        return '<a href="' . $pId . '" target="_blank" >' . $itemItem->getName() . '</a>  X ' . '<strong>' . ($commission->getProductQty()) . '</strong>';
    }

}
