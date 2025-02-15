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


class Mirasvit_CatalogLabel_Model_Label_Attribute extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('cataloglabel/label_attribute');
    }

    public function getDisplay()
    {
        $display = Mage::getModel('cataloglabel/label_display');
        if ($this->getDisplayId()) {
            $display->load($this->getDisplayId());
        }

        return $display;
    }

    public function loadByKey($labelId, $optionId)
    {
        return $this->getCollection()->addFieldToFilter('label_id', $labelId)
            ->addFieldToFilter('option_id', $optionId)
            ->getFirstItem();
    }
}