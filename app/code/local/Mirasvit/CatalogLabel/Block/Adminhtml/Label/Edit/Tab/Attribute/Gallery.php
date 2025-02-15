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

    protected function _prepareLayout()
    {
        $this->setChild(
            'upload_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                    'id'      => '',
                    'label'   => Mage::helper('cataloglabel')->__('Upload Files'),
                    'type'    => 'button',
                    'onclick' => 'CatalogLabel.Label.massUpload();'
                ))
        );

        return parent::_prepareLayout();
    }

    public function getUploadButtonHtml()
    {
        return $this->getChild('upload_button')->toHtml();
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

                foreach ($this->getImageType() as $type => $label) {
                    $options[$key][$type.'_title']       = $attr->getDisplay()->getTitle($type);
                    $options[$key][$type.'_description'] = $attr->getDisplay()->getDescription($type);
                    $options[$key][$type.'_url']         = $attr->getDisplay()->getUrl($type);
                    $options[$key][$type.'_file_save'] = array(
                        'url'  => $attr->getDisplay()->getImageUrl($type),
                        'file' => $attr->getDisplay()->getImage($type),
                    );
                }
            }
        } else {
            $attributes = Mage::getModel('cataloglabel/label_attribute')->getCollection()
                ->addFieldToFilter('label_id', $this->_getModel()->getId());
            foreach ($attributes as $key => $attr) {
                $options[$key] = $attr->getData();
                $options[$key]['file_save'] = array(
                    'url'  => $attr->getImageUrl(),
                    'file' => $attr->getImage()
                );
            }
        }

        return $options;
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
        $this->getConfig()->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/cataloglabel_label/upload', array('_secure' => true)));
        $this->getConfig()->setParams(array('form_key' => $this->getFormKey()));
        $this->getConfig()->setFileField('file');
        $this->getConfig()->setFilters(array(
            'all'    => array(
                'label' => Mage::helper('adminhtml')->__('All Files'),
                'files' => array('*.*')
            )
        ));
        $this->getConfig()->setReplaceBrowseWithRemove(true);
        $this->getConfig()->setWidth('32');
        $this->getConfig()->setHideUploadButton(true);
        return Mage::helper('core')->jsonEncode($this->getConfig()->getData());
    }

    public function getConfig()
    {
        if(is_null($this->_config)) {
            $this->_config = new Varien_Object();
        }

        return $this->_config;
    }
}