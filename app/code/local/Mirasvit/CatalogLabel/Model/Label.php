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


class Mirasvit_CatalogLabel_Model_Label extends Mage_Core_Model_Abstract
{
    const TYPE_ATTRIBUTE = 'attribute';
    const TYPE_RULE      = 'rule';

    protected function _construct()
    {
        $this->_init('cataloglabel/label');
    }

    public function getRule()
    {
        $rule = Mage::getModel('cataloglabel/label_rule')->getCollection()
            ->addFieldToFilter('label_id', $this->getId())
            ->getFirstItem();
        $rule = $rule->load($rule->getId());

        return $rule;
    }

    public function getAttribute()
    {
        $code      = Mage::getModel('eav/entity_attribute')->load($this->getAttributeId())->getAttributeCode();
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $code);

        return $attribute;
    }

    public function getPlaceholder()
    {
        return Mage::getModel('cataloglabel/placeholder')->load($this->getPlaceholderId());
    }

    public function getDisplays($product)
    {
        $result = array();

        if ($this->getType() == self::TYPE_ATTRIBUTE) {
            $option    = Mage::getResourceModel('catalog/product')->getAttributeRawValue($product->getId(), $this->getAttributeId(), Mage::app()->getStore()->getStoreId());
            $attribute = $this->getAttribute();

            if (!$attribute->usesSource()) {
                //text attribute
                $labelAttributes = Mage::getModel('cataloglabel/label_attribute')->getCollection()
                    ->addFieldToFilter('label_id', $this->getId());
                foreach ($labelAttributes as $labelAttribute) {
                    $optionText = (string) $labelAttribute->getOptionText();
                    if ($optionText !== '' && strpos((string) $option, $optionText) !== false) {
                        $result[] = $labelAttribute->getDisplay();
                    }
                }
            } else {
                //int attribute
                $labelAttribute = Mage::getModel('cataloglabel/label_attribute')->loadByKey($this->getId(), $option);
                $result[]       = $labelAttribute->getDisplay();
            }

            if ($product->getTypeId() == 'configurable') {
                $productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
                foreach ($productAttributeOptions as $productAttribute) {
                    foreach ($productAttribute['values'] as $attribute) {
                        $labelAttribute = Mage::getModel('cataloglabel/label_attribute')->loadByKey($this->getId(), $attribute['value_index']);
                        $result[] = $labelAttribute->getDisplay();
                    }
                }
            }
        } elseif ($this->getType() == self::TYPE_RULE) {
            $rule = $this->getRule();
            if ($rule->isProductId($product->getId())) {
                $result[] = $rule->getDisplay();
            }
        }

        return $result;
    }
}