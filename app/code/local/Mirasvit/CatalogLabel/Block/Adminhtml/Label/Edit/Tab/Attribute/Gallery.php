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


class Mirasvit_CatalogLabel_Block_Adminhtml_Label_Edit_Tab_Attribute_Gallery extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mst_cataloglabel/label/edit/tab/attribute/gallery.phtml');
    }

    public function getAddButtonHtml()
    {
        $input = $this->_getAttribute()->getData('frontend_input');

        $addButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('cataloglabel')->__('Add New Row'),
                'id'    => 'add_link_item',
                'class' => 'add',
            ));

        if (!$this->_getAttribute()->usesSource()) {
            return $addButton->toHtml();
        }
    }

    public function getAttibuteOptions()
    {
        $options   = array();
        $attribute = $this->_getAttribute();

        if ($attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);

            foreach ($options as $key => $option) {
                $attr = Mage::getModel('cataloglabel/label_attribute')->loadByKey($this->_getModel()->getId(), $option['value']);
                $options[$key] = array_merge($option, $this->_getDisplayData($attr));
            }
        } else {
            // text attribute: rows are stored label attributes, option_id is the row key
            $attributes = Mage::getModel('cataloglabel/label_attribute')->getCollection()
                ->addFieldToFilter('label_id', $this->_getModel()->getId())
                ->setOrder('option_id', 'ASC');
            foreach ($attributes as $attr) {
                $options[] = array_merge(array(
                    'value'       => $attr->getOptionId(),
                    'option_text' => $attr->getOptionText(),
                ), $this->_getDisplayData($attr));
            }
        }

        return $options;
    }

    protected function _getDisplayData($attr)
    {
        $data    = array();
        $display = $attr->getDisplay();

        foreach ($this->getImageType() as $type => $label) {
            $data[$type.'_title']       = $display->getTitle($type);
            $data[$type.'_description'] = $display->getDescription($type);
            $data[$type.'_url']         = $display->getUrl($type);
            $data[$type.'_file_save']   = array(
                'url'  => $display->getImageUrl($type),
                'file' => $display->getImage($type),
            );
        }

        return $data;
    }

    public function getImageType()
    {
        $images = array();
        $placeholder = $this->_getModel()->getPlaceholder();
        foreach ($placeholder->getImageType() as $type) {
            $images[$type] = Mage::getSingleton('cataloglabel/system_config_source_imageType')->getLabel($type);
        }

        return $images;
    }

    protected function _getModel()
    {
        return Mage::registry('current_model');
    }

    protected function _getAttribute()
    {
        $attributeId   = $this->_getModel()->getAttributeId();
        $attributeCode = Mage::getModel('eav/entity_attribute')->load($attributeId)->getAttributeCode();
        $attribute     = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attributeCode);

        return $attribute;
    }

    public function getConfigJson()
    {
        $helper     = Mage::helper('cataloglabel');
        $extensions = Mage::getSingleton('cataloglabel/config')->getAllowedImageExtensions();
        $maxSize    = $this->getMaxUploadSize();

        return Mage::helper('core')->jsonEncode(array(
            'url'               => $this->getUrl('*/cataloglabel_label/upload'),
            'params'            => array('form_key' => $this->getFormKey()),
            'fileField'         => 'file',
            'allowedExtensions' => $extensions,
            'maxFileSize'       => $maxSize,
            'messages'          => array(
                'invalidType' => $helper->__('Allowed file types: %s.', implode(', ', $extensions)),
                'tooLarge'    => $helper->__('File is too large. Maximum size is %s MB.', round($maxSize / 1048576, 1)),
                'failed'      => $helper->__('Upload failed.'),
            ),
        ));
    }

    public function getAcceptAttribute()
    {
        $accept = array();
        foreach (Mage::getSingleton('cataloglabel/config')->getAllowedImageExtensions() as $extension) {
            $accept[] = '.'.$extension;
        }

        return implode(',', $accept);
    }

    /**
     * Max upload size in bytes by upload_max_filesize / post_max_size, 0 = unlimited
     */
    public function getMaxUploadSize()
    {
        $sizes = array_filter(array(
            $this->_iniSizeToBytes(ini_get('upload_max_filesize')),
            $this->_iniSizeToBytes(ini_get('post_max_size')),
        ));

        return $sizes ? min($sizes) : 0;
    }

    protected function _iniSizeToBytes($value)
    {
        $value = trim($value);
        $bytes = (int) $value;

        switch (strtolower(substr($value, -1))) {
            case 'g':
                $bytes *= 1024;
            case 'm':
                $bytes *= 1024;
            case 'k':
                $bytes *= 1024;
        }

        return $bytes;
    }
}
