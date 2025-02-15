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


class Mirasvit_CatalogLabel_Block_Adminhtml_Placeholder_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('placeholder_tabs')
            ->setDestElementId('edit_form')
            ->setTitle(Mage::helper('cataloglabel')->__('Placeholder Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label'   => Mage::helper('cataloglabel')->__('General Information'),
            'title'   => Mage::helper('cataloglabel')->__('General Information'),
            'content' => $this->getLayout()->createBlock('cataloglabel/adminhtml_placeholder_edit_tab_general')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}