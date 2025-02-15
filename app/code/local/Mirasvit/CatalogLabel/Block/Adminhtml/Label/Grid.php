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


class Mirasvit_CatalogLabel_Block_Adminhtml_Label_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('cataloglabelLabelGrid');
        $this->setDefaultSort('label_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('cataloglabel/label')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('label_id', array(
            'header' => Mage::helper('cataloglabel')->__('ID'),
            'align'  => 'right',
            'width'  => '50px',
            'index'  => 'label_id',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('cataloglabel')->__('Title'),
            'align'  => 'left',
            'index'  => 'name',
        ));

        $placeholders = array();
        foreach (Mage::getModel('cataloglabel/placeholder')->getCollection() as $placeholder) {
            $placeholders[$placeholder->getId()] = $placeholder->getName();
        }
        $this->addColumn('placeholder_id', array(
            'header'  => Mage::helper('cataloglabel')->__('Placeholder'),
            'align'   => 'left',
            'index'   => 'placeholder_id',
            'type'    => 'options',
            'options' => $placeholders,
        ));

        $this->addColumn('type', array(
            'header'  => Mage::helper('cataloglabel')->__('Type'),
            'align'   => 'left',
            'index'   => 'type',
            'type'    => 'options',
            'options' => array(
                Mirasvit_CatalogLabel_Model_Label::TYPE_ATTRIBUTE => Mage::helper('cataloglabel')->__('Attribute'),
                Mirasvit_CatalogLabel_Model_Label::TYPE_RULE      => Mage::helper('cataloglabel')->__('Rule'),
            ),
        ));

        $this->addColumn('is_active', array(
            'header'  => Mage::helper('cataloglabel')->__('Status'),
            'align'   => 'left',
            'width'   => '80px',
            'index'   => 'is_active',
            'type'    => 'options',
            'options' => array(
                1 => Mage::helper('cataloglabel')->__('Enabled'),
                0 => Mage::helper('cataloglabel')->__('Disabled'),
            ),
        ));

        $this->addColumn('action',
            array(
                'header'  => Mage::helper('cataloglabel')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('cataloglabel')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    ),
                    array(
                        'caption' => Mage::helper('cataloglabel')->__('Delete'),
                        'url'     => array('base'=> '*/*/delete'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            )
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('label_id');
        $this->getMassactionBlock()->setFormFieldName('label');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'   => Mage::helper('cataloglabel')->__('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('cataloglabel')->__('Are you sure?')
        ));

        $statuses = array(
            1 => Mage::helper('cataloglabel')->__('Enabled'),
            0 => Mage::helper('cataloglabel')->__('Disabled')
        );

        $this->getMassactionBlock()->addItem('is_active', array(
            'label'      => Mage::helper('cataloglabel')->__('Change status'),
            'url'        => $this->getUrl('*/*/massStatus', array('_current'=> true)),
            'additional' => array(
            'is_active' => array(
                    'name'   => 'is_active',
                    'type'   => 'select',
                    'class'  => 'required-entry',
                    'label'  => Mage::helper('cataloglabel')->__('Status'),
                    'values' => $statuses
                )
            )
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}