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


class Mirasvit_CatalogLabel_Model_Label_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    protected $_groups = array(
        'base' => array(
            'name',
            'attribute_set_id',
            'sku',
            'category_ids',
            'url_key',
            'visibility',
            'status',
            'default_category_id',
            'meta_description',
            'meta_keyword',
            'meta_title',
            'price',
            'special_price',
            'special_price_from_date',
            'special_price_to_date',
            'tax_class_id',
            'short_description',
            'full_description'
        ),
        'extra' => array(
            'created_at',
            'updated_at',
            'qty',
            'price_diff',
            'percent_discount'
        )
    );
    public function __construct()
    {
        parent::__construct();
        $this->setType('cataloglabel/label_rule_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        $productCondition  = Mage::getModel('cataloglabel/label_rule_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();

        $attributes        = array();
        foreach ($productAttributes as $code => $label) {
            $group = 'attributes';
            foreach ($this->_groups as $key => $values) {
                if (in_array($code, $values)) {
                    $group = $key;
                }
            }
            $attributes[$group][] = array(
                'value' => 'cataloglabel/label_rule_condition_product|'.$code,
                'label' => $label
            );
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array(
                'value' => 'cataloglabel/label_rule_condition_combine',
                'label' => Mage::helper('cataloglabel')->__('Conditions Combination')
            ),
            array(
                'label' => Mage::helper('cataloglabel')->__('Product'),
                'value' => $attributes['base']
            ),
            array(
                'label' => Mage::helper('cataloglabel')->__('Product Additional'),
                'value' => $attributes['extra']
            ),
        ));

        if (isset($attributes['attributes'])) {
            $conditions = array_merge_recursive($conditions, array(
                array(
                    'label' => Mage::helper('cataloglabel')->__('Product Attribute'),
                    'value' => $attributes['attributes']
                ),
            ));
        }

        return $conditions;
    }

    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
