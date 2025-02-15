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



$installer = $this;

$version = Mage::helper('mstcore/version')->getModuleVersionFromDb('cataloglabel');
if ($version == '0.0.4') {
    return;
} elseif ($version != '0.0.3') {
    die("Please, run migration 0.0.3");
}

$installer->startSetup();
$helper = Mage::helper('cataloglabel/migration');

$table = $installer->getTable('cataloglabel/label_rule_product');

$helper->addIndex($installer, $table, 'IDX_RULE_ID', 'rule_id');
$helper->addIndex($installer, $table, 'IDX_PRODUCT_ID', 'product_id');

$installer->endSetup();
