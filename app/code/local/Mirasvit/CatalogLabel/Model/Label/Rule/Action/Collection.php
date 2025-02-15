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


class Mirasvit_CatalogLabel_Model_Label_Rule_Action_Collection extends Mage_Rule_Model_Action_Collection
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('cataloglabel/label_rule_action_collection');
    }

    public function getNewChildSelectOptions()
    {
        $actions = parent::getNewChildSelectOptions();
        $actions = array_merge_recursive($actions, array(
            array(
                'value' => 'cataloglabel/label_rule_action_product',
                'label' => Mage::helper('cataloglabel')->__('Update the Product'))
        ));
        return $actions;
    }
}
