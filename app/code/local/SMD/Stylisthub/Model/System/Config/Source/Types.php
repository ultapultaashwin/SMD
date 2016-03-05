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
class SMD_Stylisthub_Model_System_Config_Source_Types
{
	/**
	 * Available product type
	 *
	 * @return multitype:multitype:string NULL
	 */
   public function toOptionArray()
    {
        return array(
            array('value' => 'simple', 'label'=>Mage::helper('adminhtml')->__('Simple')),
            array('value' => 'virtual', 'label'=>Mage::helper('adminhtml')->__('Virtual')),
            array('value' => 'downloadable', 'label'=>Mage::helper('adminhtml')->__('Downloadable')),
        );
    }
}
