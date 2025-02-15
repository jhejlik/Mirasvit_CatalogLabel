<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/extension_cataloglabel
 * @version   1.0.9
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


class Mirasvit_CatalogLabel_Block_Adminhtml_Label_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'label_id';
        $this->_blockGroup = 'cataloglabel';
        $this->_controller = 'adminhtml_label';

        $this->_addButton('saveandcontinue', array(
            'label'   => Mage::helper('cataloglabel')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class'   => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }

        if (!Mage::registry('current_model')->getId()) {
            $this->_removeButton('saveandcontinue');
            $this->_removeButton('save');
            $this->_removeButton('reset');
        }
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_model')->getId() > 0) {
            return Mage::helper('cataloglabel')->__("Edit Label '%s'", $this->htmlEscape(Mage::registry('current_model')->getName()));
        } else {
            return Mage::helper('cataloglabel')->__("Add Label");
        }
    }
}