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


class Mirasvit_CatalogLabel_Model_Resource_Label_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('cataloglabel/label_rule', 'rule_id');
    }

    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $this->_saveDisplay($object);

        parent::_beforeSave($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassStatus()) {
        }

        return parent::_afterSave($object);
    }

    protected function _saveDisplay(Mage_Core_Model_Abstract $object)
    {
        $displayData = $object->getData('display');
        $display = $object->getDisplay();
        $display->addData($displayData);
        $display->save();

        $object->setDisplayId($display->getId());

        return $this;
    }

    public function updateRuleProductData($rule)
    {
        $ruleId     = $rule->getId();
        $productIds = $rule->getMatchingProductIds();
        $write      = $this->_getWriteAdapter();

        $write->beginTransaction();
        $write->delete($this->getTable('cataloglabel/label_rule_product'), $write->quoteInto('rule_id = ?', $ruleId));

        $rows = array();
        $queryStart = 'INSERT INTO '.$this->getTable('cataloglabel/label_rule_product').' (
                rule_id, product_id) values ';
        $queryEnd = ' ON DUPLICATE KEY UPDATE product_id=VALUES(product_id)';

        try {
            foreach ($productIds as $productId) {
                $rows[] = "('".implode("','", array($ruleId, $productId))."')";

                if (sizeof($rows) == 1000) {
                    $sql = $queryStart.join(',', $rows).$queryEnd;
                    $write->query($sql);
                    $rows = array();
                }
            }

            if (!empty($rows)) {
                $sql = $queryStart.join(',', $rows).$queryEnd;
                $write->query($sql);
            }

            $write->commit();
        } catch (Exception $e) {
            $write->rollback();
            throw $e;
        }

        return $this;
    }

    public function getRuleProductIds($ruleId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('cataloglabel/label_rule_product'), 'product_id')
            ->where('rule_id=?', $ruleId);
        return $read->fetchCol($select);
    }

    public function isRuleProductId($ruleId, $productId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('cataloglabel/label_rule_product'), 'product_id')
            ->where('rule_id=?', $ruleId)
            ->where('product_id=?', $productId);
        return count($read->fetchCol($select));
    }
}
