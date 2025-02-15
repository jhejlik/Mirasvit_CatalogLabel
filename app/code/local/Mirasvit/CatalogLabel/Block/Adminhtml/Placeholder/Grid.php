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


class Mirasvit_CatalogLabel_Block_Adminhtml_Placeholder_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('cataloglabelPlaceholderGrid');
        $this->setDefaultSort('placeholder_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('cataloglabel/placeholder')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('placeholder_id', array(
            'header' => Mage::helper('cataloglabel')->__('ID'),
            'align'  => 'right',
            'width'  => '50px',
            'index'  => 'placeholder_id',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('cataloglabel')->__('Name'),
            'align'  => 'left',
            'index'  => 'name',
        ));

        $this->addColumn('code', array(
            'header' => Mage::helper('cataloglabel')->__('Identifier'),
            'align'  => 'left',
            'index'  => 'code',
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
        $this->setMassactionIdField('placeholder_id');
        $this->getMassactionBlock()->setFormFieldName('placeholder');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'   => Mage::helper('cataloglabel')->__('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('cataloglabel')->__('Are you sure?')
        ));

        $statuses = array(
            1 => Mage::helper('cataloglabel')->__('Enabled'),
            0 => Mage::helper('cataloglabel')->__('Disabled')
        );

        array_unshift($statuses, array('label' => '', 'value' => ''));

        $this->getMassactionBlock()->addItem('status', array(
            'label'      => Mage::helper('cataloglabel')->__('Change status'),
            'url'        => $this->getUrl('*/*/massStatus', array('_current'=> true)),
            'additional' => array(
            'visibility' => array(
                    'name'   => 'status',
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