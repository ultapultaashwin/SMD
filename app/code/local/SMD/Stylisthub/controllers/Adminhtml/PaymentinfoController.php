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
 * This file is used to maintain stylist payment information
 */
class SMD_Stylisthub_Adminhtml_PaymentinfoController extends Mage_Adminhtml_Controller_action
{
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('stylisthub/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }
    /**
     * Load phtml file layout
     *
     * @return void
     */
    public function indexAction() {
        $this->_initAction()
        ->renderLayout();
    }
    /**
     * Export transaction info as csv file
     *
     * @return void
     */
     public function exportCsvAction()
    {
        $fileName   = 'transaction.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/customer_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * Export transaction info as xml file
     *
     * @return void
     */
    public function exportXmlAction()
    {
        $fileName   = 'transaction.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/customer_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
}
