<?php

namespace Aventi\PriceByCity\Plugin\Quote\Model;

use Magento\Checkout\Model\Cart;

class Quote
{
    private $logger;

    private $helper;
    

    public function __construct(        
        \Psr\Log\LoggerInterface $logger,
        \Aventi\PriceByCity\Helper\Data $helper                   
    )
    {        
        $this->logger = $logger;
        $this->helper = $helper;        
    }

    
    public function beforeAddProduct(\Magento\Quote\Model\Quote $quote, 
        \Magento\Catalog\Model\Product $product, 
        $request = null,
        $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL)
    {           

        /*$qty = !is_null($request) ? $request->getQty() : 1 ;                        
        
        if($product){
            $productId = $product->getId();
            $stock = $this->helper->getIsAvailable($productId, $product->getSku(), 'stock');    
            
            if($qty > $stock){
                
                $errorMsg = __('The requested qty is not available');                                                          
                throw new \Magento\Framework\Exception\LocalizedException($errorMsg);                                  
                
            }
                    
        }    */                     
                
        return [$product, $request, $processMode];
    }
}