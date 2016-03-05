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
 * Render the Amount received from admin to stylist
 */
class SMD_Stylisthub_Block_Adminhtml_Renderersource_Amountreceived extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Function to render data of received amount from admin
     *
     * Return the received amount
     * @return float
     */
   public function render(Varien_Object $row)
   {
      $return       = '';
      $value        = $row->getData($this->getColumn()->getIndex());
      $collection  = Mage::getModel('stylisthub/transaction')->getCollection()
                        ->addFieldToSelect('stylist_commission')
                        ->addFieldToFilter('stylist_id', $value)
                        ->addFieldToFilter('paid', 1);
      $collection->getSelect()
                ->columns('SUM(	stylist_commission) AS stylist_commission')
                ->group('stylist_id');
      foreach ($collection as $amount)
      {
            $return = $amount->getStylistCommission();
      }
      return Mage::helper('core')->currency($return, true, false);
    }
}
