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
 * This file is used to create table for "Stylist Vacation Mode"
 */
$installer = $this;

/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/*
 * Table structure for table `stylisthub_vacationmode`
 */
$installer->run("
  DROP TABLE IF EXISTS {$this->getTable('stylisthub_vacationmode')};

  CREATE TABLE IF NOT EXISTS {$this->getTable('stylisthub_vacationmode')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stylist_id` int(11) NOT NULL,
  `vacation_message` text CHARACTER SET utf8 NOT NULL,
  `product_disabled` tinyint(4) NOT NULL,
  `vacation_status` tinyint(4) NOT NULL,
  `date_from` datetime NOT NULL,
  `date_to` datetime NOT NULL,
  `set_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ");

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
/**
 * create compare_product_id attribute
 */
$setup->addAttribute('catalog_product', 'compare_product_id', array(
    'group' => 'Special Attributes',
    'input' => 'text',
    'type' => 'varchar',
    'label' => 'Compare Price with Product Id',
    'backend' => '',
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'searchable' => 1,
    'filterable' => 0,
    'comparable' => 1,
    'visible_on_front' => 1,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front' => 0,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
/**
 * create stylist shipping option attribute
 */
$setup->addAttribute('catalog_product', 'stylist_shipping_option', array(
    'group' => 'Special Attributes',
    'label' => 'Shipping',
    'type' => 'varchar',
    'input' => 'select',
    'default' => '',
    'class' => '',
    'backend' => 'eav/entity_attribute_backend_array',
    'frontend' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'apply_to' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    'visible' => true,
    'required' => true,
    'user_defined' => false,
    'searchable' => true,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => true,
    'option' => array(
        'value' => array('Free' => array(0 => 'Free'), 'Shipping Cost' => array(0 => 'Shipping Cost')),
        'order' => array('Free' => '0', 'Shipping Cost' => '1')
    ),
    'visible_in_advanced_search' => true,
));
/**
 * create stylist national shipping price
 */
$setup->addAttribute('catalog_product', 'national_shipping_price', array(
    'group' => 'Special Attributes',
    'input' => 'text',
    'type' => 'int',
    'label' => 'National Shipping Price',
    'apply_to' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    'backend' => '',
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'searchable' => 1,
    'filterable' => 0,
    'comparable' => 1,
    'visible_on_front' => 1,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front' => 0,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
/**
 * create stylist international shipping price
 */
$setup->addAttribute('catalog_product', 'international_shipping_price', array(
    'group' => 'Special Attributes',
    'input' => 'text',
    'type' => 'int',
    'label' => 'International Shipping Price',
    'apply_to' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    'backend' => '',
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'searchable' => 1,
    'filterable' => 0,
    'comparable' => 1,
    'visible_on_front' => 1,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front' => 0,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
/**
 * create stylist default country to set shipping price
 */
$setup->addAttribute('catalog_product', 'default_country', array(
    'group' => 'Special Attributes',
    'input' => 'select',
    'type' => 'varchar',
    'label' => 'Default Shipping Country',
    'apply_to' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    'backend' => '',
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'searchable' => 1,
    'filterable' => 0,
    'comparable' => 1,
    'visible_on_front' => 1,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front' => 0,
    'source' => 'catalog/product_attribute_source_countryofmanufacture',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->endSetup();
