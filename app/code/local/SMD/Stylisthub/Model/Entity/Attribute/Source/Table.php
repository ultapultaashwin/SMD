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
class SMD_Stylisthub_Model_Entity_Attribute_Source_Table extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Add approve and disapprove action in grid
     *
     * Return the available options
     * @return string
     */
   public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $withEmpty = true;
        $defaultValues = false;
        $options[] = array(
                'value' => 0,
                'label' => 'Pending'
        );
        $options[] = array(
                'value' => 1,
                'label' => 'Approve'
        );
        $options[] = array(
                'value' => 2,
                'label' => 'Disapprove'
        );
        return $options;
    }
}
