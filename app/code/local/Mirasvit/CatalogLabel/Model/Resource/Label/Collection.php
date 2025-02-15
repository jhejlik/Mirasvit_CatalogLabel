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


class Mirasvit_CatalogLabel_Model_Resource_Label_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('cataloglabel/label');
    }

    public function addActiveFilter()
    {
        $date = Mage::app()->getLocale()->date();

        $activeFrom   = array();
        $activeFrom[] = array('date' => true, 'to' => date($date->toString('YYYY-MM-dd H:mm:ss')));
        $activeFrom[] = array('date' => true, 'null' => true);

        $activeTo     = array();
        $activeTo[]   = array('date' => true, 'from' => date($date->toString('YYYY-MM-dd H:mm:ss')));
        $activeTo[]   = array('date' => true, 'null' => true);

        $this->addFieldToFilter('is_active', 1);
        $this->addFieldToFilter('active_from', $activeFrom);
        $this->addFieldToFilter('active_to', $activeTo);

        return $this;
    }


    public function addStoreFilter($store)
    {
        if (!Mage::app()->isSingleStoreMode()) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }

            $this->getSelect()
                ->join(array('store_table' => $this->getTable('cataloglabel/label_store')), 'main_table.label_id = store_table.label_id', array())
                ->where('store_table.store_id in (?)', array(0, $store));
        }

        return $this;
    }
}