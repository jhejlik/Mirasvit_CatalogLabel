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


class Mirasvit_CatalogLabel_Model_Config
{
    /**
     * Allowed label image extensions => matching IMAGETYPE_* constants (getimagesize).
     * webp: add 'webp' => IMAGETYPE_WEBP (PHP 7.1+), svg needs separate validation.
     */
    protected $_allowedImageTypes = array(
        'jpg'  => IMAGETYPE_JPEG,
        'jpeg' => IMAGETYPE_JPEG,
        'png'  => IMAGETYPE_PNG,
        'gif'  => IMAGETYPE_GIF,
    );

    public function getAllowedImageExtensions()
    {
        return array_keys($this->_allowedImageTypes);
    }

    public function getAllowedImageTypes()
    {
        return array_unique(array_values($this->_allowedImageTypes));
    }

    public function getBaseMediaPath()
    {
        return Mage::getBaseDir('media').DS.'cataloglabel';
    }

    public function getBaseMediaUrl()
    {
        return Mage::getBaseUrl('media').DS.'cataloglabel';
    }

    public function getBaseTmpMediaPath()
    {
        return Mage::getBaseDir('media').DS.'cataloglabel';
    }

    public function getBaseTmpMediaUrl()
    {
        return Mage::getBaseUrl('media').DS.'cataloglabel';
    }
}