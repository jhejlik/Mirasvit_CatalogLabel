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


class Mirasvit_CatalogLabel_Block_Adminhtml_Label_Edit_Tab_Attribute extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mst_cataloglabel/label/edit/tab/attribute.phtml');
    }

    protected function _toHtml()
    {
        $accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
            ->setId('attributeInfo');

        $accordion->addItem('attribute', array(
            'title'   => Mage::helper('cataloglabel')->__('Gallery'),
            'content' => $this->getLayout()->createBlock('cataloglabel/adminhtml_label_edit_tab_attribute_gallery')->toHtml(),
            'open'    => true,
        ));

        $this->setChild('accordion', $accordion);

        return parent::_toHtml();
    }

    public function getTabLabel()
    {
        return Mage::helper('cataloglabel')->__('Gallery');
    }

    public function getTabTitle()
    {
        return Mage::helper('cataloglabel')->__('Gallery');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
