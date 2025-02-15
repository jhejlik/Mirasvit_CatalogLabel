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


class Mirasvit_CatalogLabel_Model_Resource_Label_Display extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('cataloglabel/label_display', 'display_id');
    }

    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $this->_saveImages($object);

        parent::_beforeSave($object);
    }

    protected function _saveImages(Mage_Core_Model_Abstract $object)
    {
        $images = Mage::getSingleton('cataloglabel/system_config_source_imageType')->toOptionArray();
        foreach ($images as $image) {
            $code = 'display_'.$image['value'].'_image';
            if(isset($_FILES[$code]['name']) && $_FILES[$code]['name']) {
                try {
                    $uploader = new Varien_File_Uploader($code);
                    $uploader->setAllowedExtensions(null)
                        ->setAllowCreateFolders(true)
                        ->setAllowRenameFiles(true)
                        ->setFilesDispersion(true);

                    $result = $uploader->save(Mage::getSingleton('cataloglabel/config')->getBaseMediaPath());
                    $object->setData($image['value'].'_image', $result['file']);
                } catch (Exception $e) {
                    Mage::throwException($e);
                }
            }

            if (isset($_POST[$code]) && isset($_POST[$code]['delete']) && $_POST[$code]['delete'] == 1) {
                $object->setData($image['value'].'_image', '');
            }
        }
    }
}