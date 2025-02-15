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


$magentoVer = Mage::getVersionInfo();
$installer = $this;

$version = Mage::helper('mstcore/version')->getModuleVersionFromDb('cataloglabel');
if ($version == '0.0.3') {
    return;
} elseif ($version != '0.0.2') {
    die("Please, run migration 0.0.2");
}

$installer->startSetup();
$helper = Mage::helper('cataloglabel/migration');
$table = $installer->getTable('cataloglabel/placeholder');

//if magento 1.4.x or 1.5.x
if (($magentoVer['major'] == 1)
    && (($magentoVer['minor'] == 4)
    	|| ($magentoVer['minor'] == 5))) {
			$sql = "				
				ALTER TABLE {$this->getTable('cataloglabel/placeholder')} ADD is_auto_for_list tinyint(1) COMMENT 'Add label automatically in the product list page';
				ALTER TABLE {$this->getTable('cataloglabel/placeholder')} ADD is_auto_for_view tinyint(1) COMMENT 'Add label automatically in the product view page';
			";
			$helper->trySql($installer, $sql);
} else {
	$installer->getConnection()->addColumn($installer->getTable('cataloglabel/placeholder'), 'is_auto_for_list', array(
	    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
	    'default' 	=> 1,
	    'comment'   => 'Add label automatically in the product list page',
	));

	$installer->getConnection()->addColumn($installer->getTable('cataloglabel/placeholder'), 'is_auto_for_view', array(
	    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
	    'default' 	=> 1,
	    'comment'   => 'Add label automatically in the product view page',
	));
}

$installer->endSetup();