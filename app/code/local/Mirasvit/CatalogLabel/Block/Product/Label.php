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


class Mirasvit_CatalogLabel_Block_Product_Label extends Mage_Core_Block_Template
{
    protected $_labelObjectsArray;
    protected $_labelPosition = array('TL'  => 0,
                                    'TR'    => 0,
                                    'TC'    => 0,
                                    'ML'    => 0,
                                    'MR'    => 0,
                                    'MC'    => 0,
                                    'BL'    => 0,
                                    'BR'    => 0,
                                    'BC'    => 0,
                                    'EMPTY' => 0,
                                    );

    public function setLabelObjectsArray($displays) {
        $labelPosition = array('TL' => 0,
                            'TR'    => 0,
                            'TC'    => 0,
                            'ML'    => 0,
                            'MR'    => 0,
                            'MC'    => 0,
                            'BL'    => 0,
                            'BR'    => 0,
                            'BC'    => 0,
                            'EMPTY' => 0,
                            );

        $this->_labelObjectsArray = array(
                                        'TL'    => new Varien_Object(),
                                        'TR'    => new Varien_Object(),
                                        'TC'    => new Varien_Object(),
                                        'ML'    => new Varien_Object(),
                                        'MR'    => new Varien_Object(),
                                        'MC'    => new Varien_Object(),
                                        'BL'    => new Varien_Object(),
                                        'BR'    => new Varien_Object(),
                                        'BC'    => new Varien_Object(),
                                        'EMPTY' => new Varien_Object(),
                                        );

        foreach ($displays as $display) {
            if ($image = $display->getImage()) {
                if (file_exists(Mage::getSingleton('cataloglabel/config')->getBaseMediaPath().$image)) {
                    list($w, $h) = getimagesize(Mage::getSingleton('cataloglabel/config')->getBaseMediaPath().$image);
                }
                $this->_labelObjectsArray[$display->getPosition()]
                    ->setData($labelPosition[$display->getPosition()],
                                new Varien_Object(array(
                                    'image' => $display->getImage(),
                                    'width' => $w,
                                    'height' => $h,
                                ))
                            );
                $labelPosition[$display->getPosition()] += 1;
            }
        }
    }

    public function setLabelPositionCount($display)
    {
        $this->_labelPosition[$display->getPosition()] += 1;
    }

    public function getLabelPositionCount($display)
    {
        return $this->_labelPosition[$display->getPosition()];
    }

    public function setProduct($product)
    {
        parent::setProduct($product);

        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
        if ($stockItem){
            $this->setQty(intval($stockItem->getQty()));
            $this->setRf($product->getPrice() - $product->getFinalPrice());
            if ($product->getPrice() > 0) {
                $this->setRfc(ceil($product->getPrice() - $product->getFinalPrice()));
                $this->setRfp(intval(($product->getPrice() - $product->getFinalPrice()) / $product->getPrice() * 100));
            }
        }

        return $this;
    }

    public function getDisplays()
    {
        $result = array();

        $placeholder = Mage::getModel('cataloglabel/placeholder')->loadByCode($this->getPlaceholderCode());
        $labels = Mage::getModel('cataloglabel/label')->getCollection()
            ->addActiveFilter()
            ->addStoreFilter(Mage::app()->getStore())
            ->addFieldToFilter('placeholder_id', $placeholder->getId());

        $labels->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array('main_table.*', 'label_customer_group.*'))
            ->joinLeft(array('label_customer_group' => Mage::getSingleton('core/resource')->getTableName('cataloglabel/label_customer_group')),
                             'main_table.label_id = label_customer_group.label_id',
                             array())
            ->where('label_customer_group.customer_group_id = ?', Mage::getSingleton('customer/session')->getCustomerGroupId());

        $labels->getSelect()->order('sort_order ASC'); //it will be applied only if more than one labels are in the same position

        foreach ($labels as $label) {
            $result = array_merge($result, $label->getDisplays($this->getProduct()));
        }

        foreach ($result as $key => $display) {
            $display->setType($this->getType());
            $this->_prepareDisplay($display);
        }

        return $result;
    }

    public function getImageSizeHtml($image, $position, $positionCount = false)
    {
        $currentPositionObject      = $this->_labelObjectsArray[$position];
        $currentPositionObjectArray = $currentPositionObject->toArray();
        $currentLabelObject         = $currentPositionObject->getData($positionCount);

        if (!is_object($currentLabelObject)) {
            return;
        }

        $w = $currentLabelObject->getWidth();
        $h = $currentLabelObject->getHeight();

        $baseStyle = 'width: ' . $w . 'px; height: ' . $h . 'px;';
        $halfW     = 'margin-left: ' . -$w/2 . 'px;';
        $halfH     = 'margin-top: ' . -$h/2 . 'px;';

        $imageSpace = 10; //space between images if we use more then one image in the same position
        if ($positionCount) {
            $width  = array();
            foreach ($currentPositionObjectArray as $positionKey => $positionData) {
                if ($positionKey == $positionCount) {
                    break;
                }
                $width[]  = $positionData->getWidth();
            }

            $leftShiftW  = 'margin-left: ' . (($imageSpace*$positionCount) + array_sum($width)) . 'px;';
            $rightShiftW = 'margin-right: ' . (($imageSpace*$positionCount) + array_sum($width)) . 'px;';

            if (in_array($position, array('MC', 'TC', 'BC'))) {
                $widthArray = $this->_getWidthArray($currentPositionObjectArray);
                $leftShiftCenterW = 'margin-left: ' . ((-(array_sum($widthArray)/2) - ($imageSpace*(count($widthArray)-1))) + $widthArray[0] + (array_sum($width) - $widthArray[0]) + ($imageSpace*$positionCount)) . 'px;';
            }

            switch ($position) {
                case 'MC':
                    return $baseStyle. $halfH . $leftShiftCenterW;
                    break;

                case 'TC':
                case 'BC':
                    return $baseStyle. $leftShiftCenterW;
                    break;

                case 'ML':
                    return $baseStyle . $halfH . $leftShiftW;
                    break;

                case 'MR':
                    return $baseStyle . $halfH . $rightShiftW;
                    break;

                case 'TR':
                case 'BR':
                    return $baseStyle . $rightShiftW;
                    break;

                case 'TL':
                case 'BL':
                    return $baseStyle . $leftShiftW;
                    break;

                default:
                    return $baseStyle;
                    break;
            }
        } else {
            if (count($currentPositionObjectArray) > 1 && in_array($position, array('MC', 'TC', 'BC'))) {
                $widthArray = $this->_getWidthArray($currentPositionObjectArray);
                $leftShiftFirstLabelCenterW = 'margin-left: ' . (-(array_sum($widthArray)/2) - ($imageSpace*(count($widthArray)-1))) . 'px;';
            }

            switch ($position) {
                case 'MC':
                    if (count($currentPositionObjectArray) > 1) {
                        return $baseStyle. $halfH .$leftShiftFirstLabelCenterW;
                    } else {
                        return $baseStyle . $halfH . $halfW;
                    }
                    break;

                case 'TC':
                case 'BC':
                    if (count($currentPositionObjectArray) > 1) {
                        return $baseStyle. $leftShiftFirstLabelCenterW;
                    } else {
                        return $baseStyle . $halfW;
                    }
                    break;

                case 'MR':
                case 'ML':
                    return $baseStyle . $halfH;
                    break;

                default:
                    return $baseStyle;
                    break;
            }
        }
    }

    protected function _getWidthArray($currentPositionObjectArray)
    {
        $widthArray = array();
        foreach ($currentPositionObjectArray as $positionKey => $positionData) {
            $widthArray[]  = $positionData->getWidth();
        }

        return $widthArray;
    }

    protected function _prepareDisplay($display)
    {
        $display->setData($this->getType().'_title', $this->_formatTxt($display->getTitle()));

        return $display;
    }

    protected function _formatTxt($txt)
    {
        $product = $this->getProduct();
        //need if [var_dsc] > 0 && < 1
        if ($this->getRfp() == 0
            && (strpos($txt, '[var_dsc]') !== false
                || strpos($txt, '[var_rfp]') !== false) ) {
                    $dsc = $this->_getSpecPriceMinusPricePercent($product);
                    $txt = str_replace('[var_dsc]', $dsc, $txt);
                    $txt = str_replace('[var_rfp]', $dsc, $txt);
        }

        $vars = new Varien_Object();
        $vars->setData(
            array(
                'qty'      => $this->getQty(),
                'nl'       => '<br>',
                'rf'       => $this->getRf(),
                'rfc'      => $this->getRfc(),
                'dsc'      => $this->getRfp(),
                'rfp'      => $this->getRfp(),
                'price'    => $this->_getActualPrice(),
                'category' => $this->_getParentCategory(),
            )
        );

        return Mage::helper('mstcore/parsevariables')->parse($txt, array('product' => $product, 'var' => $vars));
    }

    protected function _getSpecPriceMinusPricePercent($product)
    {
        $price = $product->getPrice();
        $specPrice = $product->getSpecialPrice();
        $specPriceMinusPricePercent = 0;

        $specialPriceFromDate = $product->getSpecialFromDate();
        $specialPriceToDate   = $product->getSpecialToDate();
        $today =  time();
        $inDateInterval = false;
        if ($specPrice) {
            if (($today >= strtotime($specialPriceFromDate) && $today <= strtotime($specialPriceToDate))
                || ($today >= strtotime($specialPriceFromDate) && is_null($specialPriceToDate))) {

                $inDateInterval = true;
            }
        }

        if (!$inDateInterval) {
            return '';
        }

        if ($specPrice
            && $specPrice != 0
            && $specPrice < $price) {
                $specPriceMinusPricePercent = 100 - (($specPrice*100)/$price);
        }

        if ($specPriceMinusPricePercent > 0
            && $specPriceMinusPricePercent < 1) {
                $specPriceMinusPricePercent = 1;
        }

        if ($specPriceMinusPricePercent == 0)  {
            return '';
        }

        return $specPriceMinusPricePercent;
    }

    protected function _getActualPrice()
    {
        $product = $this->getProduct();
        $price = $product->getFinalPrice();
        $formattedSpecialPrice = Mage::helper('core')->currency($price,true,false);
        if  ($price == 0)  {

            return '';
        }

        return $formattedSpecialPrice;
    }

    protected function _getParentCategory()
    {
        $category = Mage::registry('current_category');
        if ($category) {
            $thisCategory = $category->getName();

            return $thisCategory;
        }
    }

    public function isProductList() {
        $fullActionCode = Mage::helper('cataloglabel')->getFullActionCode();
        $productListActions = array('catalog_category_view',
                                    'catalogsearch_result_index',
                                    'catalogsearch_advanced_result',
                                    'amshopby_index_index',
                                    'autocomplete_result_index',
                                    'cms_index_index');

        return in_array($fullActionCode, $productListActions) ? true : false;
    }

    public function isProductView() {
        $fullActionCode = Mage::helper('cataloglabel')->getFullActionCode();

        return (($fullActionCode == 'catalog_product_view') || ($fullActionCode == 'amconf_ajax_index')) ? true : false;
    }
}

