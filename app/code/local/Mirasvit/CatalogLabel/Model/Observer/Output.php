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


class Mirasvit_CatalogLabel_Model_Observer_Output
{
    public function afterOutput($obj)
    {
        $block = $obj->getEvent()->getBlock();
        $transport = $obj->getEvent()->getTransport();
        if(empty($transport)) { //it does not work for magento 1.4 and older
            return $this;
        }        
        $this->appendListLabels($block, $transport);
        $this->appendViewLabels($block, $transport);

        return $this;
    }

    protected function appendListLabels($block, $transport)
    {
        if (!$block instanceof Mage_Catalog_Block_Product_List) {
            return $this;
        }
        $productCollection  = $block->getLoadedProductCollection();
        $html               = $transport->getHtml();
        $listPlaceholders   = Mage::getModel('cataloglabel/placeholder')->getCollection()
            ->addFieldTofilter('is_active', 1)
            ->addFieldToFilter('is_auto_for_list', 1)
            ->addFieldTofilter('image_type', array('like'=>'%list%'));

        $html = $this->addLabelHtmlToList($productCollection, $listPlaceholders, $html, $block);
        $transport->setHtml($html);

        return $this;
    }

    protected function appendViewLabels($block, $transport)
    {
        if (!$block instanceof Mage_Catalog_Block_Product_View_Media) {
            return $this;
        }
        $product            = $block->getProduct();
        $html               = $transport->getHtml();
        $veiwPlaceholders   = Mage::getModel('cataloglabel/placeholder')->getCollection()
            ->addFieldTofilter('is_active', 1)
            ->addFieldToFilter('is_auto_for_view', 1)
            ->addFieldToFilter('image_type', array('like'=>'%view%'));

        $html = $this->addLabelHtmlToView($product, $veiwPlaceholders, $html, $block);
        $transport->setHtml($html);

        return $this;
    }

    protected function addLabelHtmlToView($product, $placeholders, $html, $block)
    {
        foreach($placeholders as $p) {
            $ourhtml = Mage::helper('cataloglabel')->getProductHtml($p->getCode(), $product, 'view', 'badge');
            if (!$ourhtml) {
                continue;
            }

            $resultHtml     = null;
            $pattern        = '/<[a-z]{1,3}[^>]class="product-image product-image-zoom[a-zA-Z0-9\s\-_]?">(.*)<\/[a-z]{1,3}>/isU';
            $replacement    = '<div class="catalog-product-view-media">'.$ourhtml.'${0}</div>';
            $resultHtml     = preg_replace($pattern, $replacement, $html);

            if ($resultHtml) {
                $html = $resultHtml;
            }
        }

        return $html;
    }

    protected function addLabelHtmlToList($productCollection, $placeholders, $html, $block)
    {
        foreach($productCollection as $product) {
            foreach($placeholders as $p) {
                $ourhtml = Mage::helper('cataloglabel')->getProductHtml($p->getCode(), $product, 'list', 'badge');
                if (!$ourhtml) {
                    continue;
                }

                $resultHtml     = null;
                $productUrl     = preg_quote($product->getProductUrl());
                $productUrl     = str_replace("/", "\/", $productUrl);
                $pattern        = '/(<a href="'.$productUrl.'[^>]*class="product-image">)((.*)(<\/a>))/isU';
                $replacement    = '${1}'.$ourhtml.'${2}';
                $resultHtml     = preg_replace($pattern, $replacement, $html);

                if ($resultHtml) {
                    $html = $resultHtml;
                }
            }
        }

        return $html;
    }

}