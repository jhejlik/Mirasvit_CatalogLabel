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


class Mirasvit_CatalogLabel_Model_System_Config_Source_Attribute
{
    public function toOptionArray()
    {
        $result = array();

        $productAttributeCollection = Mage::getResourceModel('catalog/product_attribute_collection');
        $productAttributeCollection
            ->addFieldToFilter('frontend_input', array('like' => '%select'))
            ->addFieldToFilter('is_user_defined', 1)
            ->setOrder('frontend_label', 'asc');
        foreach ($productAttributeCollection as $attribute) {
            $result[] = array(
                'label' => $attribute->getFrontendLabel().' ['.$attribute->getAttributeCode().']',
                'value' => $attribute->getAttributeId(),
            );
        }

        return $result;
    }
}