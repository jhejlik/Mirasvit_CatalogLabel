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


class Mirasvit_CatalogLabel_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getProductHtml($placeholderCode, $product, $type, $template, $width = null, $height = null)
    {
        return Mage::getSingleton('core/layout')
                ->createBlock('cataloglabel/product_label')
                ->setType($type)
                ->setTemplate('mst_cataloglabel/product/'.$template.'.phtml')
                ->setPlaceholderCode($placeholderCode)
                ->setProduct($product)
                ->setWidth($width)
                ->setHeight($height)
                ->toHtml();
    }

    public function getProductListHtml($placeholderCode, $product, $width = null, $height = null)
    {
        return $this->getProductHtml($placeholderCode, $product, 'list', 'list', $width, $height);
    }

    public function getProductViewHtml($placeholderCode, $product, $width = null, $height = null)
    {
        return $this->getProductHtml($placeholderCode, $product, 'view', 'view', $width, $height);
    }

    public function getFullActionCode()
    {
        $request = Mage::app()->getRequest();
        return strtolower($request->getModuleName().'_'.$request->getControllerName().'_'.$request->getActionName());
    }
}