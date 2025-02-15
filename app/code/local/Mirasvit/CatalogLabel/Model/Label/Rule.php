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


class Mirasvit_CatalogLabel_Model_Label_Rule extends Mage_Rule_Model_Rule
{
    protected $_productIds;

    private static $conditionsMem = null;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('cataloglabel/label_rule');
        $this->setIdFieldName('rule_id');
    }

    public function getDisplay()
    {
        $display = Mage::getModel('cataloglabel/label_display');
        if ($this->getDisplayId()) {
            $display->load($this->getDisplayId());
        }

        return $display;
    }

    public function getLabel()
    {
        $label = Mage::getModel('cataloglabel/label');
        if ($this->getLabelId()) {
            $label->load($this->getLabelId());
        }

        return $label;
    }

    public function getConditionsInstance()
    {
        return Mage::getModel('cataloglabel/label_rule_condition_combine');
    }

    public function getActionsInstance()
    {
        return Mage::getModel('cataloglabel/label_rule_action_collection');
    }

    protected function _afterSave()
    {
        $this->_getResource()->updateRuleProductData($this);
        parent::_afterSave();
    }

    public function getMatchingProductIds()
    {
        if (is_null($this->_productIds)) {
            $this->_productIds = array();
            $this->setCollectedAttributes(array());

            $productCollection = Mage::getResourceModel('catalog/product_collection');

            $categoryIds = $this->getConditionsCategoryIds();
            //improve category_ids speed
            if ($categoryIds) {
                $productCollection
                    ->joinField('category_id',
                        'catalog/category_product',
                        'category_id',
                        'product_id=entity_id',
                        null,
                        'left')
                    ->addAttributeToFilter('category_id', array('in' => $categoryIds))
                    ->getSelect()->group('e.entity_id');
            }

            $this->getConditions()->collectValidatedAttributes($productCollection);

            Mage::getSingleton('core/resource_iterator')->walk(
                $productCollection->getSelect(),
                array(array($this, 'callbackValidateProduct')),
                array(
                    'attributes' => $this->getCollectedAttributes(),
                    'product'    => Mage::getModel('catalog/product'),
                )
            );
        }

        return $this->_productIds;
    }

    /**
     * @return array
     */
    public function getConditionsCategoryIds()
    {
        $conditions = $this->getConditions();
        $categoryIds = array();
        if ($conditions && isset($conditions['conditions']) && is_array($conditions['conditions'])) {
            foreach ($conditions['conditions'] as $condition) {
                if (isset($condition['attribute'])
                    && isset($condition['value'])
                    && $condition['attribute'] == 'category_ids') {
                    $categoryIds[] = $condition['value'];
                } else {
                    $categoryIds = array();
                }
            }
        }

        return $categoryIds;
    }

    /**
     * Retrieve rule combine conditions model
     *
     * @return Mage_Rule_Model_Condition_Combine
     */
    public function getConditions()
    {
        if (isset(self::$conditionsMem[$this->getId()]) && self::$conditionsMem[$this->getId()]) {
            return self::$conditionsMem[$this->getId()];
        }

        self::$conditionsMem[$this->getId()] = parent::getConditions();

        return self::$conditionsMem[$this->getId()];
    }

    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
        }
    }

    public function getProductIds()
    {
        return $this->_getResource()->getRuleProductIds($this->getId());
    }

    public function isProductId($productId)
    {
        return $this->_getResource()->isRuleProductId($this->getId(), $productId);
    }
}