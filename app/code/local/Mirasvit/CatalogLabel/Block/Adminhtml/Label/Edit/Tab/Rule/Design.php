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


class Mirasvit_CatalogLabel_Block_Adminhtml_Label_Edit_Tab_Rule_Design extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model       = Mage::registry('current_model');
        $rule        = $model->getRule();
        $display     = $rule->getDisplay();
        $placeholder = $model->getPlaceholder();

        $form = new Varien_Data_Form();
        $this->setForm($form);

        foreach ($placeholder->getImageType() as $type) {
            $label  = Mage::getSingleton('cataloglabel/system_config_source_imageType')->getLabel($type);
            $design = $form->addFieldset('fieldset_'.$type, array('legend'=> Mage::helper('cataloglabel')->__($label)));

            $design->addField('image'.$type, 'image', array(
                'label'    => Mage::helper('cataloglabel')->__('Image'),
                'required' => false,
                'name'     => 'display_'.$type.'_image',
                'value'    => $display->getImageUrl($type),
            ));

            if ($placeholder->getIsPositioned()) {
                $design->addField('position'.$type, 'labelposition', array(
                    'label'    => Mage::helper('cataloglabel')->__('Position'),
                    'name'     => 'rule[display]['.$type.'_position]',
                    'required' => false,
                    'value'    => $display->getPosition($type),
                ));
            }

            $design->addField('title'.$type, 'text', array(
                'label'    => Mage::helper('cataloglabel')->__('Title'),
                'required' => false,
                'name'     => 'rule[display]['.$type.'_title]',
                'value'    => $display->getTitle($type),
            ));

            $design->addField('description'.$type, 'textarea', array(
                'label'    => Mage::helper('cataloglabel')->__('Description'),
                'required' => false,
                'name'     => 'rule[display]['.$type.'_description]',
                'value'    => $display->getDescription($type),
            ));

            $design->addField('url'.$type, 'text', array(
                'label'    => Mage::helper('cataloglabel')->__('Url'),
                'required' => false,
                'name'     => 'rule[display]['.$type.'_url]',
                'value'    => $display->getUrl($type),
                'note'     => 'For internal links you can use relative path like: /category/product-url-key.html </br> For extenal linking please use full URL like: http://www.google.com'
            ));
        }


        return parent::_prepareForm();
    }
}
