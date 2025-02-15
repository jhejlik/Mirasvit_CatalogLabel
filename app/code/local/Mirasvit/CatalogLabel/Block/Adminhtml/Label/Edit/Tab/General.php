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


class Mirasvit_CatalogLabel_Block_Adminhtml_Label_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');

        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('label');
        $this->setForm($form);

        $general = $form->addFieldset('label_form', array('legend'=> Mage::helper('cataloglabel')->__('General Information')));

        $cronStatus = Mage::helper('mstcore/cron')->checkCronStatus(false, false, '<span style="color:red;"><b>Cron job is required for automatical label update.</b></span>');
        if ($cronStatus !== true) {
            $general->addField('cronjob_status', 'note', array(
                'name' => 'cronjob_status',
                'label' => Mage::helper('cataloglabel')->__('Cron job'),
                'note' => $cronStatus,
            ));
        }

        $general->addField('name', 'text', array(
            'label'    => Mage::helper('cataloglabel')->__('Title'),
            'required' => true,
            'name'     => 'name',
            'value'    => $model->getName()
        ));

        $general->addField('placeholder_id', 'select', array(
            'label'    => Mage::helper('cataloglabel')->__('Placeholder'),
            'required' => true,
            'name'     => 'placeholder_id',
            'value'    => $model->getPlaceholderId(),
            'values'   => Mage::getModel('cataloglabel/placeholder')->getCollection()->toOptionArray(),
        ));

        $general->addField('type', 'select', array(
            'label'    => Mage::helper('cataloglabel')->__('Type'),
            'name'     => 'type',
            'values'   => Mage::getSingleton('cataloglabel/system_config_source_labelType')->toOptionArray(),
            'value'    => $model->getType(),
            'disabled' => true,
        ));

        $general->addField('is_active', 'select', array(
            'label'  => Mage::helper('cataloglabel')->__('Is Active'),
            'name'   => 'is_active',
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'value'  => $model->getIsActive(),
        ));

        if ($model->getType() == Mirasvit_CatalogLabel_Model_Label::TYPE_ATTRIBUTE) {
            $general->addField('attribute_id', 'select', array(
                'label'    => Mage::helper('cataloglabel')->__('Attribute'),
                'required' => true,
                'name'     => 'attribute_id',
                'required' => true,
                'value'    => $model->getAttributeId(),
                'values'   => Mage::getSingleton('cataloglabel/system_config_source_attribute')->toOptionArray(),
                'disabled' => $model->getAttributeId() ? true : false,
            ));
        }

        if ($model->getActiveFrom() == '0000-00-00 00:00:00' || $model->getActiveFrom() == NULL) {
            $activeFromDate = null;
        } else {
            $activeFromDate =  Mage::getModel('core/date')->date(null,strtotime($model->getActiveFrom()));
        }

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $general->addField('active_from', 'date', array(
            'label'    => Mage::helper('cataloglabel')->__('Active From'),
            'required' => false,
            'name'     => 'active_from',
            'value'    => $activeFromDate,
            'image'    => $this->getSkinUrl('images/grid-cal.gif'),
            'format'   => $dateFormatIso
        ));

        if ($model->getActiveTo() == '0000-00-00 00:00:00' || $model->getActiveTo() == NULL) {
            $activeToDate = null;
        } else {
            $activeToDate =  Mage::getModel('core/date')->date(null,strtotime($model->getActiveTo()));
        }

        $general->addField('active_to', 'date', array(
            'label'    => Mage::helper('cataloglabel')->__('Active To'),
            'required' => false,
            'name'     => 'active_to',
            'value'    => $activeToDate,
            'image'    => $this->getSkinUrl('images/grid-cal.gif'),
            'format'   => $dateFormatIso
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $general->addField('stores', 'multiselect', array(
                'label'    => Mage::helper('cataloglabel')->__('Visible In'),
                'required' => true,
                'name'     => 'stores[]',
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
                'value'    => $model->getStoreId()
            ));
        } else {
            $general->addField('stores', 'hidden', array(
                'name'  => 'stores[]',
                'value' => Mage::app()->getStore(true)->getId()
            ));
        }

        $general->addField('customer_group_ids', 'multiselect', array(
            'label'    => Mage::helper('cataloglabel')->__('Visible for Customer Groups'),
            'required' => true,
            'name'     => 'customer_group_ids[]',
            'values'   => Mage::getSingleton('customer/group')->getCollection()->toOptionArray(),
            'value'    => $model->getCustomerGroupIds()
        ));

        if ($model->getType() == 'rule') {
            $general->addField('sort_order', 'text', array(
                'label'    => Mage::helper('cataloglabel')->__('Sort order'),
                'required' => false,
                'name'     => 'sort_order',
                'value'    => $model->getSortOrder(),
                'note'     => 'It will be applied only if more than one labels are in the same position.'
            ));
        }

        return parent::_prepareForm();
    }
}
