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
if ($version == '0.0.2') {
    return;
} elseif ($version != '0.0.1') {
    die("Please, run migration 0.0.1");
}

$installer->startSetup();

$helper = Mage::helper('cataloglabel/migration');
$table = $installer->getTable('cataloglabel/label_display');

//if magento 1.4.x or 1.5.x
if (($magentoVer['major'] == 1)
    && (($magentoVer['minor'] == 4)
    	|| ($magentoVer['minor'] == 5))) {
			$sql = "
				ALTER TABLE {$installer->getTable('cataloglabel/label_display')} ADD list_url TEXT COMMENT 'List Url';
				ALTER TABLE {$this->getTable('cataloglabel/label_display')} ADD view_url TEXT COMMENT 'View Url';
			";
			$helper->trySql($installer, $sql);
} else {
	$installer->getConnection()->addColumn($installer->getTable('cataloglabel/label_display'), 'list_url', array(
	    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
	    'comment'   => 'List Url',
	));

	$installer->getConnection()->addColumn($installer->getTable('cataloglabel/label_display'), 'view_url', array(
	    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
	    'comment'   => 'View Url',
	));
}

$installer->endSetup();