<?php

namespace MGS\QuickView\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    const XML_PATH_QUICKVIEW_ENABLED = 'mgs_quickview/general/enabled';

	public function aroundQuickViewHtml(
    \Magento\Catalog\Model\Product $product
    ) {
        $result = '';
        $isEnabled = $this->scopeConfig->getValue(self::XML_PATH_QUICKVIEW_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($isEnabled) {
            $productUrl = $this->_urlBuilder->getUrl('mgs_quickview/catalog_product/view', array('id' => $product->getId()));
            return $result . '<button data-title="'. __("Quick View") .'" class="action mgs-quickview" data-quickview-url=' . $productUrl . ' title="' . __("Quick View") . '">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7.91892 14.8378C11.7401 14.8378 14.8378 11.7401 14.8378 7.91892C14.8378 4.09771 11.7401 1 7.91892 1C4.09771 1 1 4.09771 1 7.91892C1 11.7401 4.09771 14.8378 7.91892 14.8378Z" stroke="#333333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16.5676 16.5678L12.8054 12.8057" stroke="#333333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            
            </span></button>';
        }
        return $result;
    }

}
