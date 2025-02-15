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


class Mirasvit_CatalogLabel_Model_Placeholder extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('cataloglabel/placeholder');
    }

    public function getImageType()
    {
        return explode(',', $this->getData('image_type'));
    }

    public function loadByCode($code)
    {
        return $this->getCollection()
            ->addFieldToFilter('code', $code)
            ->getFirstItem();
    }
}