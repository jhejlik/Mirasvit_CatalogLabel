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


class Mirasvit_CatalogLabel_Adminhtml_Cataloglabel_PlaceholderController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/cataloglabel');
    }

    public function preDispatch()
    {
        parent::preDispatch();

        return $this;
    }

    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('catalog');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Manage Placeholders'));
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('cataloglabel/adminhtml_placeholder'));
        $this->renderLayout();
    }

    public function addAction()
    {
        $model = $this->getModel();

        $this->_title($this->__('New Placeholder'));

        $this->_initAction();

        $this->_addBreadcrumb(Mage::helper('cataloglabel')->__('Manage Placeholders'), Mage::helper('cataloglabel')->__('Manage Placeholders'), $this->getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('cataloglabel')->__('New Placeholder'), Mage::helper('cataloglabel')->__('New Placeholder'));

        $this->_addContent($this->getLayout()->createBlock('cataloglabel/adminhtml_placeholder_edit'))
            ->_addLeft($this->getLayout()->createBlock('cataloglabel/adminhtml_placeholder_edit_tabs'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $model = $this->getModel();

        if ($model->getId()) {
            $this->_title($model->getName());

            $this->_initAction();
            $this->_addBreadcrumb(Mage::helper('cataloglabel')->__('Manage Placeholders'), Mage::helper('cataloglabel')->__('Manage Placeholders'), $this->getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('cataloglabel')->__('Edit Placeholder'), Mage::helper('cataloglabel')->__('Edit Placeholder'));

            $this->_addContent($this->getLayout()->createBlock('cataloglabel/adminhtml_placeholder_edit'))
            ->_addLeft($this->getLayout()->createBlock('cataloglabel/adminhtml_placeholder_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cataloglabel')->__('The placeholder does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = $this->getModel();

            $data['image_type'] = implode(',', $data['image_type']);

            $model->addData($data);

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cataloglabel')->__('Placeholder was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cataloglabel')->__('Unable to find placeholder to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        $model = $this->getModel();
        if ($model->getId()) {
            try {
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cataloglabel')->__('Placeholder was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('placeholder');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cataloglabel')->__('Please select placeholder(s)'));
        } else {
            try {
                foreach ($ids as $itemId) {
                    $model = Mage::getModel('cataloglabel/placeholder')->setIsMassDelete(true)
                        ->load($itemId);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('cataloglabel')->__('Total of %d record(s) were successfully deleted', count($ids))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $ids = $this->getRequest()->getParam('placeholder');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cataloglabel')->__('Please select placeholder(s)'));
        } else {
            try {
                foreach ($ids as $itemId) {
                    $model = Mage::getSingleton('cataloglabel/placeholder')
                        ->setIsMassStatus(true)
                        ->load($itemId)
                        ->setIsActive($this->getRequest()->getParam('status'))
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('cataloglabel')->__('Total of %d record(s) were successfully updated', count($ids))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function getModel()
    {
        $model = Mage::getModel('cataloglabel/placeholder');
        if ($id = $this->getRequest()->getParam('id')) {
            $model->load($id);
        }

        Mage::register('current_model', $model);

        return $model;
    }

}