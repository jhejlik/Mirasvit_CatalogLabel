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


class Mirasvit_CatalogLabel_Model_Resource_Label extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('cataloglabel/label', 'label_id');
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object = $this->_loadStore($object);
        $object = $this->_loadCustomerGroup($object);
        return parent::_afterLoad($object);
    }

    protected function _loadStore(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('cataloglabel/label_store'))
            ->where('label_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = $row['store_id'];
            }
            $object->setData('store_id', $array);
        }
        return $object;
    } 

    protected function _loadCustomerGroup(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('cataloglabel/label_customer_group'))
            ->where('label_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = $row['customer_group_id'];
            }
            $object->setData('customer_group_ids', $array);
        }

        return $object;
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassStatus()) {
            switch ($object->getType()) {
                case  Mirasvit_CatalogLabel_Model_Label::TYPE_ATTRIBUTE:
                    $this->_saveAttribute($object);
                    break;
                case Mirasvit_CatalogLabel_Model_Label::TYPE_RULE:
                    $this->_saveRule($object);
                    break;
            }

            $this->_saveStore($object);
            $this->_saveCustomerGroup($object);
        }

        return parent::_afterSave($object);
    }

    protected function _saveAttribute(Mage_Core_Model_Abstract $object)
    {
        if ($object->getData('attribute') && is_array($object->getData('attribute'))) {
            foreach ($object->getData('attribute') as $item) {

                $model = Mage::getModel('cataloglabel/label_attribute')->loadByKey($object->getId(), $item['option_id']);

                // row removed in gallery grid
                if (!empty($item['display']['view_is_delete'])) {
                    if ($model->getId()) {
                        if ($model->getDisplayId()) {
                            $model->getDisplay()->delete();
                        }
                        $model->delete();
                    }
                    continue;
                }

                $model->setLabelId($object->getId())
                    ->addData($item)
                    ->save();
            }
        }
    }

    protected function _saveRule(Mage_Core_Model_Abstract $object)
    {
        if ($object->getData('rule') && is_array($object->getData('rule'))) {
            $ruleData = $object->getData('rule');

            $model = $object->getRule();
            $model->setLabelId($object->getId())
                ->loadPost($ruleData)
                ->save();
        }
    }

    protected function _saveStore(Mage_Core_Model_Abstract $object)
    {
        $storeTable = $this->getTable('cataloglabel/label_store');
        $adapter    = $this->_getWriteAdapter();

        if (!$object->getData('stores')) {
            $condition = $adapter->quoteInto('label_id = ?', $object->getId());
            $adapter->delete($storeTable, $condition);

            $storeArray = array(
                'label_id' => $object->getId(),
                'store_id' => '0'
            );

            $adapter->insert($storeTable, $storeArray);

            return $this;
        }

        $condition = $adapter->quoteInto('label_id = ?', $object->getId());
        $adapter->delete($storeTable, $condition);
        foreach ((array)$object->getData('stores') as $store) {
            $storeArray = array();
            $storeArray['label_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $adapter->insert($storeTable, $storeArray);
        }
    }    

    protected function _saveCustomerGroup(Mage_Core_Model_Abstract $object)
    {
        $groupTable = $this->getTable('cataloglabel/label_customer_group');
        $adapter    = $this->_getWriteAdapter();

        $condition = $adapter->quoteInto('label_id = ?', $object->getId());
        $adapter->delete($groupTable, $condition);
        foreach ((array) $object->getData('customer_group_ids') as $group) {
            $groupArray = array(
                'label_id'          => $object->getId(),
                'customer_group_id' => $group,
            );
            $adapter->insert($groupTable, $groupArray);
        }

        return $this;
    }    
}