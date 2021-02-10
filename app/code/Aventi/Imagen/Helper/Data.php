<?php


namespace Aventi\Imagen\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 *
 * @package Aventi\SAP\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Definition of consts
     */
    const XML_PATH_IMAGE = 'imagen/options/path';



    public function getPathImage($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_IMAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }


}
