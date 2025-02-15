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


class Mirasvit_CatalogLabel_Adminhtml_LabelController extends Mage_Adminhtml_Controller_Action
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
        $this->_title($this->__('Manage Labels'));
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('cataloglabel/adminhtml_label'));
        $this->renderLayout();
    }

    public function addAction()
    {
        $model = $this->getModel();

        $this->_title($this->__('New Label'));

        $this->_initAction();

        $this->_addBreadcrumb(Mage::helper('cataloglabel')->__('Manage Labels'), Mage::helper('cataloglabel')->__('Manage Labels'), $this->getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('cataloglabel')->__('New Label'), Mage::helper('cataloglabel')->__('New Label'));

        $this->_addContent($this->getLayout()->createBlock('cataloglabel/adminhtml_label_edit'))
            ->_addLeft($this->getLayout()->createBlock('cataloglabel/adminhtml_label_edit_tabs'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $model = $this->getModel();

        if ($model->getId()) {
            $this->_title($model->getName());

            $this->_initAction();
            $this->_addBreadcrumb(Mage::helper('cataloglabel')->__('Manage Labels'), Mage::helper('cataloglabel')->__('Manage Labels'), $this->getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('cataloglabel')->__('Edit Label'), Mage::helper('cataloglabel')->__('Edit Label'));

            $this->_addContent($this->getLayout()->createBlock('cataloglabel/adminhtml_label_edit'))
            ->_addLeft($this->getLayout()->createBlock('cataloglabel/adminhtml_label_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cataloglabel')->__('The label does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

            if (isset($data['rule'])) {
                $data['label']['rule'] = $data['rule'];
            }
            if (isset($data['attribute'])) {
                $data['label']['attribute'] = $data['attribute'];
            }

            $data = $data['label'];
            //delete data for attribute
            if (isset($data['attribute'])) {
                foreach ($data['attribute'] as $key => $value) {
                    if ($value['display']['view_is_delete'] == 1) {
                        if (isset($data['attribute'][$key]['display']['view_file'])) {
                            $data['attribute'][$key]['display']['view_file'] = '';
                        }
                        if (isset($data['attribute'][$key]['display']['view_title'])) {
                            $data['attribute'][$key]['display']['view_title'] = '';
                        }
                        if (isset($data['attribute'][$key]['display']['view_description'])) {
                            $data['attribute'][$key]['display']['view_description'] = '';
                        }
                        if (isset($data['attribute'][$key]['display']['view_url'])) {
                            $data['attribute'][$key]['display']['view_url'] = '';
                        }
                        if (isset($data['attribute'][$key]['display']['list_is_delete'])) {
                            $data['attribute'][$key]['display']['list_is_delete'] = '';
                        }
                        if (isset($data['attribute'][$key]['display']['list_file'])) {
                            $data['attribute'][$key]['display']['list_file'] = '';
                        }
                        if (isset($data['attribute'][$key]['display']['list_title'])) {
                            $data['attribute'][$key]['display']['list_title'] = '';
                        }
                        if (isset($data['attribute'][$key]['display']['list_description'])) {
                            $data['attribute'][$key]['display']['list_description'] = '';
                        }
                        if (isset($data['attribute'][$key]['display']['list_url'])) {
                            $data['attribute'][$key]['display']['list_url'] = '';
                        }
                    }
                }
            }

            // $magentoVer = Mage::getVersionInfo();
            // if (($magentoVer['major'] == 1)
            //     && ($magentoVer['minor'] == 4)) {
            //     if (($data['active_from'] != '')
            //         && ($data['active_to'])) {
            //         $fromDateFrom = str_replace('/', '-', $data['active_from']);
            //         $fromDateTo   = str_replace('/', '-', $data['active_to']);
            //         $toDateFormat = 'Y-m-d';
            //         $formattedDateFrom = Mage::getModel('core/date')->date($toDateFormat , strtotime($fromDateFrom)) . ' 00:00:00';
            //         $formattedDateTo = Mage::getModel('core/date')->date($toDateFormat , strtotime($fromDateTo))  . ' 00:00:00';
            //         $data['active_to'] = $formattedDateTo;
            //         $data['active_from'] = $formattedDateFrom;
            //     }
            // } else {
                if (isset($data['active_from']) && !empty($data['active_from'])) {
                    $fromDateFrom = str_replace('/', '-', $data['active_from']);
                    $data['active_from'] = $this->_filterDate($fromDateFrom);
                }

                if (isset($data['active_to']) && !empty($data['active_to'])) {
                    $fromDateTo   = str_replace('/', '-', $data['active_to']);
                    $data['active_to'] = $this->_filterDate($fromDateTo);
                }
            // }

            $model = $this->getModel();
            $model->addData($data);

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cataloglabel')->__('Label was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cataloglabel')->__('Unable to find label to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        $model = $this->getModel();
        if ($model->getId()) {
            try {
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cataloglabel')->__('Label was successfully deleted'));
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
        $ids = $this->getRequest()->getParam('label');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cataloglabel')->__('Please select label(s)'));
        } else {
            try {
                foreach ($ids as $itemId) {
                    $model = Mage::getModel('cataloglabel/label')->setIsMassDelete(true)
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
        $ids = $this->getRequest()->getParam('label');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cataloglabel')->__('Please select label(s)'));
        } else {
            try {
                $status = 0;
                if ((int)$this->getRequest()->getParam('status') == 1) {
                    $status = 1;
                }
                foreach ($ids as $itemId) {
                    $model = Mage::getSingleton('cataloglabel/label')
                        ->setIsMassStatus(true)
                        ->load($itemId)
                        ->setIsActive($this->getRequest()->getParam('is_active'))
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

    public function uploadAction()
    {
        $path = Mage::getSingleton('cataloglabel/config')->getBaseMediaPath().DS;

        $result = array();

        try {
            $uploader = new Mage_Core_Model_File_Uploader('file');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save($path);

            $result['url'] = Mage::getSingleton('cataloglabel/config')->getBaseMediaUrl().$result['file'];
            $result['cookie'] = array(
                'name'     => session_name(),
                'value'    => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path'     => $this->_getSession()->getCookiePath(),
                'domain'   => $this->_getSession()->getCookieDomain()
            );
        } catch (Exception $e) {
            $result = array('error' => $e->getMessage(), 'errorcode' => $e->getCode());
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function newConditionHtmlAction()
    {
        $id      = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type    = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('cataloglabel/label_rule'))
            ->setPrefix('conditions');

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    protected function getModel()
    {
        $model = Mage::getModel('cataloglabel/label');
        if ($id = $this->getRequest()->getParam('id')) {
            $model->load($id);
        }

        Mage::register('current_model', $model);

        return $model;
    }

    protected function _filterDate($dateValue)
    {
        $date = Mage::app()->getLocale()->date(
            $dateValue,
            Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            null,
            false
        );
        return Mage::getModel('core/date')->gmtDate(null,$date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
    }

}