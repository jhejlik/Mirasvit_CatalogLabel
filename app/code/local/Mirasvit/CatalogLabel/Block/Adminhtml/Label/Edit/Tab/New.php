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


class Mirasvit_CatalogLabel_Block_Adminhtml_Label_Edit_Tab_New extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('cataloglabel')->__('Continue'),
                    'onclick'   => "saveAndContinueEdit()",
                    'class'     => 'save'
                    ))
                );

        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');

        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('label');
        $this->setForm($form);

        $fieldset = $form->addFieldset('placeholder_form', array('legend'=> Mage::helper('cataloglabel')->__('General Information')));

        $fieldset->addField('name', 'text', array(
            'label'    => Mage::helper('cataloglabel')->__('Title'),
            'required' => true,
            'name'     => 'name',
            'value'    => $model->getName()
        ));

        $fieldset->addField('placeholder_id', 'select', array(
            'label'    => Mage::helper('cataloglabel')->__('Placeholder'),
            'required' => true,
            'name'     => 'placeholder_id',
            'value'    => $model->getPlaceholderId(),
            'values'   => Mage::getModel('cataloglabel/placeholder')->getCollection()->toOptionArray(),
        ));

        $fieldset->addField('type', 'select', array(
            'label'  => Mage::helper('cataloglabel')->__('Relation Type'),
            'name'   => 'type',
            'values' => Mage::getSingleton('cataloglabel/system_config_source_labelType')->toOptionArray(),
            'value'  => $model->getType(),
        ));

        $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));

        return parent::_prepareForm();
    }
}
