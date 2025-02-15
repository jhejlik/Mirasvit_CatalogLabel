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


class Mirasvit_CatalogLabel_Block_Adminhtml_Label_Edit_Tab_Rule
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return Mage::helper('cataloglabel')->__('Conditions');
    }

    public function getTabTitle()
    {
        return Mage::helper('cataloglabel')->__('Conditions');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl(Mage::getModel('adminhtml/url')->getUrl('*/*/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend' => Mage::helper('cataloglabel')->__('Conditions (leave blank for all products)'))
        )->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name'     => 'conditions',
            'label'    => Mage::helper('cataloglabel')->__('Conditions'),
            'title'    => Mage::helper('cataloglabel')->__('Conditions'),
            'required' => true,
        ))->setRule($model->getRule())->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $form->setValues($model->getData());

        $info = $form->addFieldset('info_fieldset', array());
        $productIds = $model->getRule()->getProductIds();
        $info->addField('count_proudcts', 'label', array(
            'name'     => 'count_proudcts',
            'label'    => Mage::helper('cataloglabel')->__(
                'These conditions applied for %s product(s)',
                count($productIds)
            ),
        ));

        if (count($productIds) > 0) {
            $productIdsPrepared = array_slice($productIds, 0, 100);
            $info->addField('proudct_ids', 'label', array(
                'name'     => 'proudct_ids',
                'label'    => Mage::helper('cataloglabel')->__(
                    'First 100 product(s) Ids: %s',
                    implode(', ', $productIdsPrepared)
                ),
            ));
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
