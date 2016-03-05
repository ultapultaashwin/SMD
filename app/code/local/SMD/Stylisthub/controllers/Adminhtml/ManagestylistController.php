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
class SMD_Stylisthub_Adminhtml_ManagestylistController extends Mage_Adminhtml_Controller_action {
	protected function _initAction() {
		$this->loadLayout ()->_setActiveMenu ( 'stylisthub/items' )->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Items Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Stylist Manager' ) );
		return $this;
	}
	/**
	 * Load phtml file layout
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->_initAction ()->renderLayout ();
	}
	/**
	 * Edit stylist data
	 *
	 * @return void
	 */
	public function editAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$model = Mage::getModel ( 'stylisthub/stylisthub' )->load ( $id );
		if ($model->getId () || $id == 0) {
			$data = Mage::getSingleton ( 'adminhtml/session' )->getFormData ( true );
			if (! empty ( $data )) {
				$model->setData ( $data );
			}
			Mage::register ( 'stylisthub_data', $model );
			$this->loadLayout ();
			$this->_setActiveMenu ( 'stylisthub/items' );
			$this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Stylist Manager' ) );
			$this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Item News' ), Mage::helper ( 'adminhtml' )->__ ( 'Stylist News' ) );
			$this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
			$this->_addContent ( $this->getLayout ()->createBlock ( 'stylisthub/adminhtml_stylisthub_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'stylisthub/adminhtml_stylisthub_edit_tabs' ) );
			$this->renderLayout ();
		} else {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'stylisthub' )->__ ( 'Stylist details does not exist' ) );
			$this->_redirect ( '*/*/' );
		}
	}
	/**
	 * Edit stylist data
	 *
	 * @return void
	 */
	public function newAction() {
		$this->_forward ( 'edit' );
	}
	/**
	 * Save stylist data and change the status
	 *
	 * @return void
	 */
	public function saveAction() {
		$data = $this->getRequest ()->getPost ();
		if ($data) {
			$model = Mage::getModel ( 'stylisthub/stylisthub' );
			$model->setData ( $data )->setId ( $this->getRequest ()->getParam ( 'id' ) );
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime () == NULL) {
					$model->setCreatedTime ( now () )->setUpdateTime ( now () );
				} else {
					$model->setUpdateTime ( now () );
				}
				$model->save ();
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'stylisthub' )->__ ( 'Stylist approved successfully.' ) );
				Mage::getSingleton ( 'adminhtml/session' )->setFormData ( false );
				if ($this->getRequest ()->getParam ( 'back' )) {
					$this->_redirect ( '*/*/edit', array (
							'id' => $model->getId ()
					) );
					return;
				}
				$this->_redirect ( '*/*/' );
				return;
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
				Mage::getSingleton ( 'adminhtml/session' )->setFormData ( $data );
				$this->_redirect ( '*/*/edit', array (
						'id' => $this->getRequest ()->getParam ( 'id' )
				) );
				return;
			}
		}
		Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'stylisthub' )->__ ( 'Stylist details not updated' ) );
		$this->_redirect ( '*/*/' );
	}
	/**
	 * Delete multiple stylist's at a time
	 *
	 * @return void
	 */
	public function massDeleteAction() {
		$stylisthubIds = $this->getRequest ()->getParam ( 'stylisthub' );
		if (! is_array ( $stylisthubIds )) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select at least one stylist' ) );
		} else {
			try {
				foreach ( $stylisthubIds as $stylisthubId ) {
					Mage::helper ( 'stylisthub/stylisthub' )->deleteStylist ( $stylisthubId );
				}
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were successfully deleted', count ( $stylisthubIds ) ) );
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
			}
		}
		$this->_redirect ( '*/*/index' );
	}
	/**
	 * Setting commission for admin
	 *
	 * @return void
	 */
	public function setcommissionAction() {
		$this->_initAction ()->renderLayout ();
	}
	/**
	 * Save commission information in database
	 *
	 * @return void
	 */
	public function savecommissionAction() {
		if ($this->getRequest ()->getParam ( 'id' ) > 0) {
			$id = $this->getRequest ()->getParam ( 'id' );
			$commission = $this->getRequest ()->getParam ( 'commission' );
			try {
				$collection = Mage::getModel ( 'stylisthub/stylistprofile' )->load ( $id, 'stylist_id' );
				$getId = $collection->getId ();
				if ($getId != '') {
					Mage::getModel ( 'stylisthub/stylistprofile' )->load ( $id, 'stylist_id' )->setCommission ( $commission )->save ();
				} else {
					$collection = Mage::getModel ( 'stylisthub/stylistprofile' );
					$collection->setCommission ( $commission );
					$collection->setStylistId ( $id );
					$collection->save ();
				}
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'stylisthub' )->__ ( 'Stylist commission saved successfully .' ) );
				$this->_redirect ( '*/*/' );
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
				$this->_redirect ( '*/*/' );
			}
		}
		$this->_redirect ( '*/*/' );
	}
	/**
	 * Set stylist status as pending once stylist register in the website
	 *
	 * @return void
	 */
	public function pendingAction() {
		if ($this->getRequest ()->getParam ( 'id' ) > 0) {
			$id = $this->getRequest ()->getParam ( 'id' );
			$commission = $this->getRequest ()->getParam ( 'commission' );
			try {
				$model = Mage::getModel ( 'customer/customer' )->load ( $this->getRequest ()->getParam ( 'id' ) );
				$model->setCustomerstatus ( '0' )->save ();
				/**
				 * send email to admin regarding new stylist registration
				 */
				$templateId = ( int ) Mage::getStoreConfig ( 'stylisthub/admin_approval_stylist_registration/stylist_email_template_selection' );
				$adminEmailId = Mage::getStoreConfig ( 'stylisthub/stylisthub/admin_email_id' );
				$toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
				$toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
				if ($templateId) {
					$emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
				} else {
					$emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'stylisthub_admin_approval_stylist_registration_stylist_email_template_selection' );
				}
				$customer = Mage::getModel ( 'customer/customer' )->load ( $id );
				$recipient = $customer->getEmail ();
				$cname = $customer->getName ();
				$emailTemplate->setSenderName ( ucwords ( $toName ) );
				$emailTemplate->setSenderEmail ( $toMailId );
				$emailTemplateVariables = (array (
						'ownername' => ucwords ( $toName ),
						'cname' => ucwords ( $cname )
				));
				$emailTemplate->setDesignConfig ( array (
						'area' => 'frontend'
				) );
				$processedTemplate = $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
				$emailTemplate->send ( $recipient, ucwords ( $cname ), $emailTemplateVariables );
				/**
				 * end email
				 */
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'stylisthub' )->__ ( 'Stylist approved successfully.' ) );
				$this->_redirect ( '*/*/' );
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
				$this->_redirect ( '*/*/' );
			}
		}
		$this->_redirect ( '*/*/' );
	}
	/**
	 * Set stylist status as approve after stylist register in the website
	 *
	 * @return void
	 */
	public function approveAction() {
		if ($this->getRequest ()->getParam ( 'id' ) > 0) {
			$id = $this->getRequest ()->getParam ( 'id' );
			$commission = $this->getRequest ()->getParam ( 'commission' );
			try {
				$model = Mage::getModel ( 'customer/customer' )->load ( $this->getRequest ()->getParam ( 'id' ) );
				$model->setCustomerstatus ( '1' )->save ();
				/**
				 * send email to customer regarding approval of stylist registration
				 */
				$template_id = ( int ) Mage::getStoreConfig ( 'stylisthub/admin_approval_stylist_registration/stylist_email_template_selection' );
				$admin_email_id = Mage::getStoreConfig ( 'stylisthub/stylisthub/admin_email_id' );
				$toMailId = Mage::getStoreConfig ( "trans_email/ident_$admin_email_id/email" );
				$toName = Mage::getStoreConfig ( "trans_email/ident_$admin_email_id/name" );
				if ($template_id) {
					$emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $template_id );
				} else {
					$emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'stylisthub_admin_approval_stylist_registration_stylist_email_template_selection' );
				}
				$customer = Mage::getModel ( 'customer/customer' )->load ( $id );
				$recipient = $customer->getEmail ();
				$cname = $customer->getName ();
				$emailTemplate->setSenderName ( ucwords ( $toName ) );
				$emailTemplate->setSenderEmail ( $toMailId );
				$emailTemplateVariables = (array (
						'ownername' => ucwords ( $toName ),
						'cname' => ucwords ( $cname )
				));
				$emailTemplate->setDesignConfig ( array (
						'area' => 'frontend'
				) );
				$processedTemplate = $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
				$emailTemplate->send ( $recipient, ucwords ( $cname ), $emailTemplateVariables );
				/**
				 * end email
				 */
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'stylisthub' )->__ ( 'Stylist approved successfully.' ) );
				$this->_redirect ( '*/*/' );
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
				$this->_redirect ( '*/*/' );
			}
		}
		$this->_redirect ( '*/*/' );
	}
	/**
	 * Set stylist status as disapprove after stylist register in the website
	 *
	 * @return void
	 */
	public function disapproveAction() {
		if ($this->getRequest ()->getParam ( 'id' ) > 0) {
			$id = $this->getRequest ()->getParam ( 'id' );
			try {
				$model = Mage::getModel ( 'customer/customer' )->load ( $this->getRequest ()->getParam ( 'id' ) );
				$model->setCustomerstatus ( '2' )->save ();
				/**
				 * send email to admin regarding disapprove of stylist registration
				 */
				$template_id = ( int ) Mage::getStoreConfig ( 'stylisthub/admin_approval_stylist_registration/stylist_email_template_disapprove' );
				$admin_email_id = Mage::getStoreConfig ( 'stylisthub/stylisthub/admin_email_id' );
				$toMailId = Mage::getStoreConfig ( "trans_email/ident_$admin_email_id/email" );
				$toName = Mage::getStoreConfig ( "trans_email/ident_$admin_email_id/name" );
				if ($template_id) {
					$emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $template_id );
				} else {
					$emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'stylisthub_admin_approval_stylist_registration_stylist_email_template_disapprove' );
				}
				$customer = Mage::getModel ( 'customer/customer' )->load ( $id );
				$recipient = $customer->getEmail ();
				$cname = $customer->getName ();
				$emailTemplate->setSenderName ( ucwords ( $toName ) );
				$emailTemplate->setSenderEmail ( $toMailId );
				$emailTemplateVariables = (array (
						'ownername' => ucwords ( $toName ),
						'cname' => ucwords ( $cname )
				));
				$emailTemplate->setDesignConfig ( array (
						'area' => 'frontend'
				) );
				$processedTemplate = $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
				$emailTemplate->send ( $recipient, ucwords ( $cname ), $emailTemplateVariables );
				/**
				 * end email
				 */
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'stylisthub' )->__ ( 'Stylist disapproved.' ) );
				$this->_redirect ( '*/*/' );
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
				$this->_redirect ( '*/*/' );
			}
		}
		$this->_redirect ( '*/*/' );
	}
	/**
	 * Deller stylist from website
	 *
	 * @return void
	 */
	public function deleteAction() {
		if ($this->getRequest ()->getParam ( 'id' ) > 0) {
			try {
				/**
				 * Reset group id
				 */
				$model = Mage::getModel ( 'customer/customer' )->load ( $this->getRequest ()->getParam ( 'id' ) );
				$model->setGroupId ( 1 );
				$model->save ();
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Stylist successfully deleted' ) );
				$this->_redirect ( '*/*/' );
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
				$this->_redirect ( '*/*/edit', array (
						'id' => $this->getRequest ()->getParam ( 'id' )
				) );
			}
		}
		$this->_redirect ( '*/*/' );
	}
	/**
	 * Set stylist status as approve multiple stylist register in the website
	 *
	 * @return void
	 */
	public function massApproveAction() {
		$stylisthubIds = $this->getRequest ()->getParam ( 'stylisthub' );
		if (! is_array ( $stylisthubIds )) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select at least one stylist' ) );
		} else {
			try {
				foreach ( $stylisthubIds as $stylisthubId ) {
					Mage::helper ( 'stylisthub/stylisthub' )->approveStylistStatus ( $stylisthubId );
					/**
					 * send email to customer regarding approval of stylist registration
					 */
					$template_id = ( int ) Mage::getStoreConfig ( 'stylisthub/admin_approval_stylist_registration/stylist_email_template_selection' );
					$admin_email_id = Mage::getStoreConfig ( 'stylisthub/stylisthub/admin_email_id' );
					$toMailId = Mage::getStoreConfig ( "trans_email/ident_$admin_email_id/email" );
					$toName = Mage::getStoreConfig ( "trans_email/ident_$admin_email_id/name" );
					if ($template_id) {
						$emailTemplate = Mage::helper ( 'stylisthub/stylisthub' )->loadEmailTemplate ( $template_id );
					} else {
						$emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'stylisthub_admin_approval_stylist_registration_stylist_email_template_selection' );
					}
					$customer = Mage::helper ( 'stylisthub/stylisthub' )->loadCustomerData ( $stylisthubId );
					$recipient = $customer->getEmail ();
					$cname = $customer->getName ();
					$emailTemplate->setSenderName ( ucwords ( $toName ) );
					$emailTemplate->setSenderEmail ( $toMailId );
					$emailTemplateVariables = (array (
							'ownername' => ucwords ( $toName ),
							'cname' => ucwords ( $cname )
					));
					$emailTemplate->setDesignConfig ( array (
							'area' => 'frontend'
					) );
					$processedTemplate = $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
					$emailTemplate->send ( $recipient, ucwords ( $cname ), $emailTemplateVariables );
				/**
				 * end email
				 */
				}
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'A total of %d record(s) is successfully approved', count ( $stylisthubIds ) ) );
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
			}
		}
		$this->_redirect ( '*/*/index' );
	}
	/**
	 * Set stylist status as disapprove multiple stylist register in the website
	 *
	 * @return void
	 */
	public function massDisapproveAction() {
		$stylisthubIds = $this->getRequest ()->getParam ( 'stylisthub' );
		if (! is_array ( $stylisthubIds )) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select at least one stylist' ) );
		} else {
			try {
				foreach ( $stylisthubIds as $stylisthubId ) {
					Mage::helper ( 'stylisthub/stylisthub' )->disapproveStylistStatus ( $stylisthubId );
					/**
					 * send email to admin regarding disapprove of stylist registration
					 */
					$template_id = ( int ) Mage::getStoreConfig ( 'stylisthub/admin_approval_stylist_registration/stylist_email_template_disapprove' );
					$admin_email_id = Mage::getStoreConfig ( 'stylisthub/stylisthub/admin_email_id' );
					$toMailId = Mage::getStoreConfig ( "trans_email/ident_$admin_email_id/email" );
					$toName = Mage::getStoreConfig ( "trans_email/ident_$admin_email_id/name" );
					if ($template_id) {
						$emailTemplate = Mage::helper ( 'stylisthub/stylisthub' )->loadEmailTemplate ( $template_id );
					} else {
						$emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'stylisthub_admin_approval_stylist_registration_stylist_email_template_disapprove' );
					}
					$customer = Mage::helper ( 'stylisthub/stylisthub' )->loadCustomerData ( $stylisthubId );
					$recipient = $customer->getEmail ();
					$cname = $customer->getName ();
					$emailTemplate->setSenderName ( ucwords ( $toName ) );
					$emailTemplate->setSenderEmail ( $toMailId );
					$emailTemplateVariables = (array (
							'ownername' => ucwords ( $toName ),
							'cname' => ucwords ( $cname )
					));
					$emailTemplate->setDesignConfig ( array (
							'area' => 'frontend'
					) );
					$processedTemplate = $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
					$emailTemplate->send ( $recipient, ucwords ( $cname ), $emailTemplateVariables );
				/**
				 * end email
				 */
				}
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'A total of %d record(s) is successfully disapproved', count ( $stylisthubIds ) ) );
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
			}
		}
		$this->_redirect ( '*/*/index' );
	}
}
