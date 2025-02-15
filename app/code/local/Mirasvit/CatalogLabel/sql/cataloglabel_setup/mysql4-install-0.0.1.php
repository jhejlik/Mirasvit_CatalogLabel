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

$installer->startSetup();

$helper = Mage::helper('cataloglabel/migration');

//if magento 1.4.x
$magentoVer = Mage::getVersionInfo();
if (($magentoVer['major'] == 1)
    && ($magentoVer['minor'] == 4)) {
   $sql = "

    DROP TABLE IF EXISTS `{$this->getTable('cataloglabel/placeholder')}`;
    CREATE TABLE `{$this->getTable('cataloglabel/placeholder')}` (
        `placeholder_id`     int(11)      NOT NULL AUTO_INCREMENT,
        `name`               varchar(255) NULL DEFAULT '',
        `code`               varchar(255) NULL DEFAULT '',
        `is_active`          tinyint(1)   NULL DEFAULT '1',
        `is_positioned`      tinyint(1)   NULL DEFAULT '0',
        `output_type`        varchar(255) NULL,
        `image_type`         varchar(255) NULL,
        `created_at`         datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        `updated_at`         datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY (`placeholder_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    DROP TABLE IF EXISTS `{$this->getTable('cataloglabel/label')}`;
    CREATE TABLE `{$this->getTable('cataloglabel/label')}` (
        `label_id`           int(11)      NOT NULL AUTO_INCREMENT,
        `type`               varchar(255) NOT NULL,
        `attribute_id`       int(11)      NULL,
        `placeholder_id`     int(11)      NOT NULL,
        `name`               varchar(255) NULL DEFAULT '',
        `active_from`        datetime     NULL,
        `active_to`          datetime     NULL,
        `sort_order`         int(11)      NOT NULL DEFAULT '0',
        `is_active`          tinyint(1)   NULL DEFAULT '1',
        `created_at`         datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        `updated_at`         datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY (`label_id`),
      CONSTRAINT `FK_CATALOGLABEL_PLACEHOLDER` FOREIGN KEY (`placeholder_id`) REFERENCES {$this->getTable('cataloglabel/placeholder')} (`placeholder_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    DROP TABLE IF EXISTS `{$this->getTable('cataloglabel/label_store')}`;
    CREATE TABLE `{$this->getTable('cataloglabel/label_store')}` (
        `id`                 int(11)     NOT NULL AUTO_INCREMENT COMMENT 'Id',
        `label_id`           int(11)     NOT NULL COMMENT 'Label Id',
        `store_id`           smallint(5) unsigned NOT NULL COMMENT 'Store Id',
        PRIMARY KEY                   (`id`),
        KEY `FK_LABEL_STORE_ID`       (`store_id`),
        KEY `FK_LABEL_STORE_LABEL_ID` (`label_id`),
        CONSTRAINT `fk_label_store_id` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `fk_label_store_label_id` FOREIGN KEY (`label_id`) REFERENCES `{$this->getTable('cataloglabel/label')}` (`label_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;



    DROP TABLE IF EXISTS `{$this->getTable('cataloglabel/label_customer_group')}`;
    CREATE TABLE `{$this->getTable('cataloglabel/label_customer_group')}` (
        `id`                 int(11)      NOT NULL AUTO_INCREMENT COMMENT 'Id',
        `label_id`           int(11)      NOT NULL COMMENT 'label Id',
        `customer_group_id`  smallint(3)  unsigned NOT NULL COMMENT 'Store Id',
        PRIMARY KEY                      (`id`),
        KEY `FK_LABEL_CUSTOMER_GROUP_ID` (`customer_group_id`),
        KEY `FK_LABEL_ID`                (`label_id`),
        CONSTRAINT `FK_CGID_` FOREIGN KEY (`customer_group_id`) REFERENCES `{$this->getTable('customer/customer_group')}` (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `FK_LABEL_ID_LABEL_ID` FOREIGN KEY (`label_id`) REFERENCES `{$this->getTable('cataloglabel/label')}` (`label_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    DROP TABLE IF EXISTS `{$this->getTable('cataloglabel/label_attribute')}`;
    CREATE TABLE `{$this->getTable('cataloglabel/label_attribute')}` (
        `attribute_id`       int(11)      NOT NULL AUTO_INCREMENT,
        `label_id`           int(11)      NOT NULL,
        `display_id`         int(11)      NOT NULL,
        `option_id`          int(11)      NULL,
        `option_text`        varchar(255) NULL DEFAULT '',
        PRIMARY KEY (`attribute_id`),
        CONSTRAINT `FK_CATALOGLABEL_ATTRIBUTE_LABEL` FOREIGN KEY (`label_id`) REFERENCES {$this->getTable('cataloglabel/label')} (`label_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    DROP TABLE IF EXISTS `{$this->getTable('cataloglabel/label_rule')}`;
    CREATE TABLE {$this->getTable('cataloglabel/label_rule')} (
        `rule_id`               int(11)      unsigned NOT NULL auto_increment,
        `label_id`              int(11)      NOT NULL,
        `display_id`            int(11)      NOT NULL,
        `conditions_serialized` text         NOT NULL,
        `actions_serialized`    text         NOT NULL,
        `stop_rules_processing` tinyint(1)   NOT NULL DEFAULT '1',
        PRIMARY KEY  (`rule_id`),
        CONSTRAINT `FK_CATALOGLABEL_RULE_LABEL` FOREIGN KEY (`label_id`) REFERENCES {$this->getTable('cataloglabel/label')} (`label_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS `{$this->getTable('cataloglabel/label_rule_product')}`;
    CREATE TABLE {$this->getTable('cataloglabel/label_rule_product')} (
        `id`                    int(11) unsigned NOT NULL auto_increment,
        `rule_id`               int(11) unsigned NOT NULL DEFAULT '0',
        `product_id`            int(11) unsigned NOT NULL DEFAULT '0',
        PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS `{$this->getTable('cataloglabel/label_display')}`;
    CREATE TABLE {$this->getTable('cataloglabel/label_display')} (
        `display_id`       int(11)      unsigned NOT NULL auto_increment,
        `list_image`       varchar(255) NULL,
        `list_title`       varchar(255) NULL,
        `list_description` text         NULL,
        `list_position`    varchar(255) NULL DEFAULT '',
        `view_image`       varchar(255) NULL,
        `view_title`       varchar(255) NULL,
        `view_description` text         NULL,
        `view_position`    varchar(255) NULL DEFAULT '',
        PRIMARY KEY  (`display_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";
    $helper->trySql($installer, $sql);
} else {
    $sql = "

    DROP TABLE IF EXISTS `{$this->getTable('cataloglabel/placeholder')}`;
    CREATE TABLE `{$this->getTable('cataloglabel/placeholder')}` (
        `placeholder_id`     int(11)      NOT NULL AUTO_INCREMENT,
        `name`               varchar(255) NULL DEFAULT '',
        `code`               varchar(255) NULL DEFAULT '',
        `is_active`          tinyint(1)   NULL DEFAULT '1',
        `is_positioned`      tinyint(1)   NULL DEFAULT '0',
        `output_type`        varchar(255) NULL,
        `image_type`         varchar(255) NULL,
        `created_at`         datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        `updated_at`         datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY (`placeholder_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    DROP TABLE IF EXISTS `{$this->getTable('cataloglabel/label')}`;
    CREATE TABLE `{$this->getTable('cataloglabel/label')}` (
        `label_id`           int(11)      NOT NULL AUTO_INCREMENT,
        `type`               varchar(255) NOT NULL,
        `attribute_id`       int(11)      NULL,
        `placeholder_id`     int(11)      NOT NULL,
        `name`               varchar(255) NULL DEFAULT '',
        `active_from`        datetime     NULL,
        `active_to`          datetime     NULL,
        `sort_order`         int(11)      NOT NULL DEFAULT '0',
        `is_active`          tinyint(1)   NULL DEFAULT '1',
        `created_at`         datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
        `updated_at`         datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY (`label_id`),
      CONSTRAINT `FK_CATALOGLABEL_PLACEHOLDER` FOREIGN KEY (`placeholder_id`) REFERENCES {$this->getTable('cataloglabel/placeholder')} (`placeholder_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    DROP TABLE IF EXISTS `{$this->getTable('cataloglabel/label_store')}`;
    CREATE TABLE `{$this->getTable('cataloglabel/label_store')}` (
        `id`                 int(11)     NOT NULL AUTO_INCREMENT COMMENT 'Id',
        `label_id`           int(11)     NOT NULL COMMENT 'Label Id',
        `store_id`           int(11)     unsigned NOT NULL COMMENT 'Store Id',
        PRIMARY KEY                   (`id`),
        KEY `FK_LABEL_STORE_ID`       (`store_id`),
        KEY `FK_LABEL_STORE_LABEL_ID` (`label_id`),
        CONSTRAINT `fk_label_store_id` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `fk_label_store_label_id` FOREIGN KEY (`label_id`) REFERENCES `{$this->getTable('cataloglabel/label')}` (`label_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;



    DROP TABLE IF EXISTS `{$this->getTable('cataloglabel/label_customer_group')}`;
    CREATE TABLE `{$this->getTable('cataloglabel/label_customer_group')}` (
        `id`                 int(11)    NOT NULL AUTO_INCREMENT COMMENT 'Id',
        `label_id`           int(11)    NOT NULL COMMENT 'label Id',
        `customer_group_id`  int(11)    unsigned NOT NULL COMMENT 'Store Id',
        PRIMARY KEY                      (`id`),
        KEY `FK_LABEL_CUSTOMER_GROUP_ID` (`customer_group_id`),
        KEY `FK_LABEL_ID`                (`label_id`),
        CONSTRAINT `FK_CGID_` FOREIGN KEY (`customer_group_id`) REFERENCES `{$this->getTable('customer/customer_group')}` (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `FK_LABEL_ID_LABEL_ID` FOREIGN KEY (`label_id`) REFERENCES `{$this->getTable('cataloglabel/label')}` (`label_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    DROP TABLE IF EXISTS `{$this->getTable('cataloglabel/label_attribute')}`;
    CREATE TABLE `{$this->getTable('cataloglabel/label_attribute')}` (
        `attribute_id`       int(11)      NOT NULL AUTO_INCREMENT,
        `label_id`           int(11)      NOT NULL,
        `display_id`         int(11)      NOT NULL,
        `option_id`          int(11)      NULL,
        `option_text`        varchar(255) NULL DEFAULT '',
        PRIMARY KEY (`attribute_id`),
        CONSTRAINT `FK_CATALOGLABEL_ATTRIBUTE_LABEL` FOREIGN KEY (`label_id`) REFERENCES {$this->getTable('cataloglabel/label')} (`label_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    DROP TABLE IF EXISTS `{$this->getTable('cataloglabel/label_rule')}`;
    CREATE TABLE {$this->getTable('cataloglabel/label_rule')} (
        `rule_id`               int(11)      unsigned NOT NULL auto_increment,
        `label_id`              int(11)      NOT NULL,
        `display_id`            int(11)      NOT NULL,
        `conditions_serialized` text         NOT NULL,
        `actions_serialized`    text         NOT NULL,
        `stop_rules_processing` tinyint(1)   NOT NULL DEFAULT '1',
        PRIMARY KEY  (`rule_id`),
        CONSTRAINT `FK_CATALOGLABEL_RULE_LABEL` FOREIGN KEY (`label_id`) REFERENCES {$this->getTable('cataloglabel/label')} (`label_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS `{$this->getTable('cataloglabel/label_rule_product')}`;
    CREATE TABLE {$this->getTable('cataloglabel/label_rule_product')} (
        `id`                    int(11) unsigned NOT NULL auto_increment,
        `rule_id`               int(11) unsigned NOT NULL DEFAULT '0',
        `product_id`            int(11) unsigned NOT NULL DEFAULT '0',
        PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS `{$this->getTable('cataloglabel/label_display')}`;
    CREATE TABLE {$this->getTable('cataloglabel/label_display')} (
        `display_id`       int(11)      unsigned NOT NULL auto_increment,
        `list_image`       varchar(255) NULL,
        `list_title`       varchar(255) NULL,
        `list_description` text         NULL,
        `list_position`    varchar(255) NULL DEFAULT '',
        `view_image`       varchar(255) NULL,
        `view_title`       varchar(255) NULL,
        `view_description` text         NULL,
        `view_position`    varchar(255) NULL DEFAULT '',
        PRIMARY KEY  (`display_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";
    $helper->trySql($installer, $sql);
}

$installer->endSetup();