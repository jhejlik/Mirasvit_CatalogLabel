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


class Mirasvit_CatalogLabel_Model_Resource_Label_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('cataloglabel/label_attribute', 'attribute_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        $this->_saveDisplay($object);

        return parent::_beforeSave($object);
    }

    protected function _saveDisplay(Mage_Core_Model_Abstract $object)
    {
        $displayData = $object->getData('display');
        
        $images = Mage::getSingleton('cataloglabel/system_config_source_imageType')->toOptionArray();
        foreach ($images as $image) {
            $code = $image['value'];
            if (isset($displayData[$code.'_file'])) {
                $image = json_decode($displayData[$code.'_file']);
                if (isset($image[0])) {
                    $displayData[$code.'_image'] = $image[0]->file;
                } else {
                    $displayData[$code.'_image'] = false;
                }
            }
        }

        $display = $object->getDisplay();
        $display->addData($displayData);
        $display->save();

        $object->setDisplayId($display->getId());
        
        return $this;
    }
}