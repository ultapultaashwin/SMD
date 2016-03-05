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
class SMD_Stylisthub_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Function to get stylist group id
     *
     * Return stylist group id
     * @return int
     */
    public function getGroupId()
    {
      $cGroup = Mage::getModel('customer/group')->load('stylist', 'customer_group_code');
      $result = $cGroup->getCustomerGroupId();
      return $result;
    }
    /**
     * Getting selected prdouct types
     *
     * Return selected product types
     * @return int
     */
    public function getSelectedPrdouctType()
    {
      return Mage::getStoreConfig('stylisthub/product/producttype');
    }
    /**
     * Getting product custom option configuration
     *
     * Return product custom option enable or not
     * @return int
     */

    public function getPrdouctCustomOptions()
    {
        return Mage::getStoreConfig('stylisthub/product/productcustomoptions');
    }
    /**
     * Getting product approval option configuration
     *
     * Return product approval enable or not
     * @return int
     */

    public function getProductApproval()
    {
        return Mage::getStoreConfig('stylisthub/product/productapproval');
    }
    /**
     * Getting product types
     *
     * Return enabled product types
     * @return int
     */

    public function getProductTypes()
    {
        $productTypes = array();
        $productTypes = array(
        "simple" => "Simple Product",
        "virtual" => "Virtual Product",
        "downloadable" => "Downloadable Product",
        );
        return $productTypes;
    }
    /**
     * License key
     *
     * Return license key given or not
     * @return int
     */

    public function checkStylisthubKey()
    {
        /*$apikey = Mage::getStoreConfig('stylisthub/stylisthub/apply_smd_licensekey');
        $stylisthubApiKey = $this->stylisthubApiKey();
        if ($apikey != $stylisthubApiKey) {
           $keyerror = base64_decode('PGgzIGlkPSJ0aXRsZS10ZXh0IiBzdHlsZT0iZmxvYXQ6bGVmdDtjb2xvcjpyZWQ7bWFyZ2luOiAyNTBweCA1MTBweDsiPjxhIHN0eWxlPSJjb2xvcjpyZWQ7IiBocmVmPSJodHRwOi8vd3d3LmFwcHRoYS5jb20vY2hlY2tvdXQvY2FydC9hZGQvcHJvZHVjdC8xNTYiIHRhcmdldD0iX2JsYW5rIj5JbnZhbGlkIExpY2Vuc2UgS2V5IC0gQnV5IG5vdzwvYT48L2gzPg==');
            die($keyerror);
        }*/

		return true;
    }
    /**
     * Function to get the license key
     *
     * Return generated license key
     * @return string
     */

    public function stylisthubApiKey()
    {
        $code      = $this->genenrateOscdomain();
        $domainKey = substr($code, 0, 25) . "CONTUS";
        return $domainKey;
    }
    /**
     * Function to get the domain key
     *
     * Return domain key
     * @return string
     */

    public function domainKey($tkey)
    {
        $message = "EM-MKTPMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
        $stringLength = strlen($tkey);
        for($i = 0; $i < $stringLength; $i++) {
          $keyArray[] = $tkey[$i];
        }
        $encMessage = "";
        $kPos = 0;
        $charsStr = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $strLen = strlen($charsStr);
        for($i = 0; $i < $strLen; $i++) {
          $charsArray[] = $charsStr[$i];
        }
        $lenMessage = strlen($message);
        $count = count($keyArray);
        for($i = 0; $i < $lenMessage; $i++) {
                $char   = substr($message, $i, 1);
                $offset = $this->getOffset($keyArray[$kPos], $char);
                $encMessage .= $charsArray[$offset];
                $kPos++;

                if ($kPos >= $count) {
                        $kPos = 0;
                }
        }
        return $encMessage;
    }
    /**
     * Function to get the offset for license key
     *
     * Return offset key
     * @return string
     */

    public function getOffset($start, $end)
    {
        $charsStr = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $strLen = strlen($charsStr);
        for ($i = 0; $i < $strLen; $i++) {
           $charsArray[] = $charsStr[$i];
        }
        for ($i = count($charsArray) - 1; $i >= 0; $i--) {
           $lookupObj[ord($charsArray[$i])] = $i;
        }
        $sNum   = $lookupObj[ord($start)];
        $eNum   = $lookupObj[ord($end)];
        $offset = $eNum - $sNum;
        if ($offset < 0) {
                $offset = count($charsArray) + ($offset);
        }
        return $offset;
    }
    /**
     * Function to generate license key
     *
     * Return license key
     * @return string
     */

    public function genenrateOscdomain()
    {
        $subfolder = $matches = '';
        $strDomainName = Mage::app()->getFrontController()->getRequest()->getHttpHost();
        preg_match("/^(http:\/\/)?([^\/]+)/i", $strDomainName, $subfolder);
        preg_match("/^(https:\/\/)?([^\/]+)/i", $strDomainName, $subfolder);
        preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $subfolder[2], $matches);
        if (isset($matches['domain']))
        {
           $customerurl = $matches['domain'];
        } else {
           $customerurl = "";
        }
        $customerurl = str_replace("www.", "", $customerurl);
        $customerurl = str_replace(".", "D", $customerurl);
        $customerurl = strtoupper($customerurl);
        if (isset($matches['domain']))
        {
           $response = $this->domainKey($customerurl);
        } else {
           $response = "";
        }
        return $response;
    }
}
