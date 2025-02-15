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


class Mirasvit_CatalogLabel_Block_Adminhtml_Placeholder_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $general = $form->addFieldset('placeholder_form', array('legend'=> Mage::helper('cataloglabel')->__('General Information')));

        $general->addField('name', 'text', array(
            'label'    => Mage::helper('cataloglabel')->__('Title'),
            'required' => true,
            'name'     => 'name',
            'value'    => $model->getName()
        ));

        $general->addField('code', 'text', array(
            'label'    => Mage::helper('cataloglabel')->__('Identifier'),
            'required' => true,
            'name'     => 'code',
            'value'    => $model->getCode(),
            'class'    => 'validate-xml-identifier',
        ));

        $general->addField('is_active', 'select', array(
            'label'  => Mage::helper('cataloglabel')->__('Is Active'),
            'name'   => 'is_active',
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'value'  => $model->getIsActive(),
        ));

        $general->addField('is_auto_for_list', 'select', array(
            'label'  => Mage::helper('cataloglabel')->__('Add label in the product list page'),
            'name'   => 'is_auto_for_list',
            'values' => Mage::getSingleton('cataloglabel/system_config_source_renderMode')->toOptionArray(),
            'value'  => $model->getIsAutoForList(),
        ));

        $general->addField('is_auto_for_view', 'select', array(
            'label'  => Mage::helper('cataloglabel')->__('Add label in the product page'),
            'name'   => 'is_auto_for_view',
            'values' => Mage::getSingleton('cataloglabel/system_config_source_renderMode')->toOptionArray(),
            'value'  => $model->getIsAutoForView(),
        ));

        $general->addField('is_positioned', 'select', array(
            'label'    => Mage::helper('cataloglabel')->__('Allow Positioning'),
            'name'     => 'is_positioned',
            'required' => true,
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'value'    => $model->getIsPositioned(),
        ));

        $general->addField('image_type', 'multiselect', array(
            'label'    => Mage::helper('cataloglabel')->__('Allowed Images'),
            'name'     => 'image_type',
            'required' => true,
            'values'   => Mage::getSingleton('cataloglabel/system_config_source_imageType')->toOptionArray(),
            'value'    => $model->getImageType(),
        ));

        return parent::_prepareForm();
    }
}