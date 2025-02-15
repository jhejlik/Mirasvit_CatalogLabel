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


class Mirasvit_CatalogLabel_Model_Label_Display extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('cataloglabel/label_display');
    }

    public function getType($type)
    {
        if ($type == null && !$this->getData('type')) {
            return 'list';
        } elseif ($type != null) {
            return $type;
        }

        return $this->getData('type');
    }

    public function getImage($type = null)
    {
        $type = $this->getType($type);

        return $this->getData($type.'_image');
    }

    public function getImageUrl($type = null)
    {
        $type = $this->getType($type);

        if ($this->getImage($type)) {
            return Mage::getSingleton('cataloglabel/config')->getBaseMediaUrl().$this->getImage($type);
        }
    }

    public function getPosition($type = null)
    {
        $type     = $this->getType($type);
        $position = $this->getData($type.'_position');
        if (!$position) {
            $position = 'EMPTY';
        }

        return  $position;
    }

    public function getTitle($type = null)
    {
        $type = $this->getType($type);

        return $this->getData($type.'_title');
    }

    public function getDescription($type = null)
    {
        $type = $this->getType($type);

        return $this->getData($type.'_description');
    }

    public function getUrl($type = null)
    {
        $type = $this->getType($type);

        return $this->getData($type.'_url');
    }
}