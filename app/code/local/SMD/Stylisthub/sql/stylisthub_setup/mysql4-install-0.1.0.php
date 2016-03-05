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
$installer = $this;

/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("INSERT INTO {$this->getTable('customer_group')} (`customer_group_code`,`tax_class_id`) values ('stylist','3');");

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute('customer', 'customerstatus', array(
	'label'     => 'Customer Status',
        'visible'   => true,
        'required'  => false,
        'type'      => 'varchar',
        'input'     => 'select',
        'source'    => 'stylisthub/entity_attribute_source_table'
	));

$eavConfig = Mage::getSingleton('eav/config');
$attribute = $eavConfig->getAttribute('customer', 'customerstatus');
$attribute->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit'));  //enable all action
$attribute->save();

// create customerid
$setup->addAttribute('catalog_product', 'stylist_id', array(
    'group'         => 'Special Attributes',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Customer Id',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'searchable' => 1,
    'filterable' => 0,
    'comparable'    => 1,
    'visible_on_front' => 1,
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

// create Groupid

$setup->addAttribute('catalog_product', 'group_id', array(
    'group'         => 'Special Attributes',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Group Id',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'searchable' => 1,
    'filterable' => 0,
    'comparable'    => 1,
    'visible_on_front' => 1,
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
//Set product as Banner image or not
$setup->addAttribute('catalog_product', 'setbanner', array(
        'group' => 'Special Attributes',
        'label' => 'Display Product in Homepage Banner',
       'type' => 'int',
        'input' => 'select',
        'default' => '',
        'backend' => '',
        'frontend' => '',
        'source' => 'eav/entity_attribute_source_boolean',
        'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => true,
        'visible_in_advanced_search' => false,
        'unique' => false,
       'sort_order'        => 30
    ));
$setup->addAttribute('catalog_product', 'banner', array(
    'group' => 'Images',
    'label' => 'Product Banner(Size: 775px X 440px)',
    'type' => 'varchar',
    'input' => 'media_image',
    'default' => '',
    'class' => '',
    'backend' => '',
    'frontend' => 'catalog/product_attribute_frontend_image',
    'source' => '',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => true,
    'unique' => false,
));

$setup->updateAttribute('catalog_product', 'banner', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL);
$setup->updateAttribute('catalog_product', 'banner', 'apply_to', 'simple,virtual,bundle,configurable,grouped,downloadable');
////balag

/**
  * Table structure for table `stylisthub_commission`
  */

$installer->run("
  DROP TABLE IF EXISTS {$this->getTable('stylisthub_commission')};

  CREATE TABLE IF NOT EXISTS {$this->getTable('stylisthub_commission')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stylist_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_qty` decimal(12,0) NOT NULL,
  `product_amt` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `increment_id` int(11) NOT NULL,
  `commission_fee` decimal(12,4) NOT NULL,
  `stylist_amount` decimal(12,4) NOT NULL,
  `order_total` decimal(12,4) NOT NULL,
  `order_status` varchar(30) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `credited` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ");


 /*
 -- Table structure for table `stylisthub_transaction`
*/

$installer->run("
   DROP TABLE IF EXISTS {$this->getTable('stylisthub_transaction')};

   CREATE TABLE IF NOT EXISTS  {$this->getTable('stylisthub_transaction')}  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commission_id` int(11) NOT NULL,
  `stylist_id` int(11) NOT NULL,
  `stylist_commission` decimal(12,4) NOT NULL,
  `admin_commission` decimal(12,4) NOT NULL,
  `order_id` int(11) NOT NULL,
  `paid` tinyint(4) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `paid_date` datetime NOT NULL,
  `comment` text NOT NULL,
  `received_status` tinyint(4) NOT NULL,
  `acknowledge_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8");


/*
 -- Table structure for table `stylisthub_stylistprofile`
*/

$installer->run("
 DROP TABLE IF EXISTS {$this->getTable('stylisthub_stylistprofile')};

 CREATE TABLE IF NOT EXISTS {$this->getTable('stylisthub_stylistprofile')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stylist_id` int(11) NOT NULL,
  `store_title` varchar(50) NOT NULL,
  `country` varchar(25) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `store_banner` varchar(150) NOT NULL,
  `store_logo` varchar(150) NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `meta_description` varchar(150) NOT NULL,
  `meta_keyword` varchar(150) NOT NULL,
  `twitter_id` varchar(100) NOT NULL,
  `facebook_id` varchar(100) NOT NULL,
  `bank_payment` varchar(250) NOT NULL,
  `paypal_id` varchar(35) NOT NULL,
  `show_profile` tinyint(4) NOT NULL,
  `commission` decimal(12,2) NOT NULL,
  `setbanner` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8");

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('stylisthub_stylistreview')};

CREATE TABLE IF NOT EXISTS {$this->getTable('stylisthub_stylistreview')} (
  `stylist_review_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `stylist_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text NOT NULL,
  `store_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`stylist_review_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8");



$installer->endSetup();
