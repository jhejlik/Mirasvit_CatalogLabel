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


class Mirasvit_CatalogLabel_Block_Adminhtml_Label_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('label_tabs')
            ->setDestElementId('edit_form')
            ->setTitle(Mage::helper('cataloglabel')->__('Label Information'));
    }

    protected function _beforeToHtml()
    {
        if ($this->_getModel()->getId() > 0) {
            $this->addTab('general_section', array(
                'label'   => Mage::helper('cataloglabel')->__('General Information'),
                'title'   => Mage::helper('cataloglabel')->__('General Information'),
                'content' => $this->getLayout()->createBlock('cataloglabel/adminhtml_label_edit_tab_general')->toHtml(),
            ));

            if ($this->_getModel()->getType() == Mirasvit_CatalogLabel_Model_Label::TYPE_ATTRIBUTE) {
                $this->addTab('attribute_section', array(
                    'label'   => Mage::helper('cataloglabel')->__('Gallery'),
                    'title'   => Mage::helper('cataloglabel')->__('Gallery'),
                    'content' => $this->getLayout()->createBlock('cataloglabel/adminhtml_label_edit_tab_attribute')->toHtml(),
                ));
            } elseif ($this->_getModel()->getType() == Mirasvit_CatalogLabel_Model_Label::TYPE_RULE) {
                $this->addTab('rule_section', array(
                    'label'   => Mage::helper('cataloglabel')->__('Conditions'),
                    'title'   => Mage::helper('cataloglabel')->__('Conditions'),
                    'content' => $this->getLayout()->createBlock('cataloglabel/adminhtml_label_edit_tab_rule')->toHtml(),
                ));

                $this->addTab('rule_design', array(
                    'label'   => Mage::helper('cataloglabel')->__('Design'),
                    'title'   => Mage::helper('cataloglabel')->__('Design'),
                    'content' => $this->getLayout()->createBlock('cataloglabel/adminhtml_label_edit_tab_rule_design')->toHtml(),
                ));
            }
        } else {
            $this->addTab('setting_section', array(
                'label'   => Mage::helper('cataloglabel')->__('Settings'),
                'title'   => Mage::helper('cataloglabel')->__('Settings'),
                'content' => $this->getLayout()->createBlock('cataloglabel/adminhtml_label_edit_tab_new')->toHtml(),
            ));
        }

        return parent::_beforeToHtml();
    }

    protected function _getModel()
    {
        return Mage::registry('current_model');
    }
}