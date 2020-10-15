<?php
 
namespace Aventi\PriceByCity\Plugin\Model;
 
class Product
{
    private $logger;

    private $helper;    

    function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Aventi\PriceByCity\Helper\Data $helper        
    )
    {
        $this->logger = $logger;
        $this->helper = $helper;        
    }

    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {                       
        $result = $this->helper->calculatePriceByRegion($subject->getId());        
        return $result;
    }
    
    public function aftergetVisibility(\Magento\Catalog\Model\Product $subject, $result)
    {                       
        $price = $this->helper->calculatePriceByRegion($subject->getId());
        $result = \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH;
        if($price == 0){
            $result = \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE;
        }
        
        return $result;
    }

    public function afterisAvailable(\Magento\Catalog\Model\Product $subject, $result)
    {                                           
        /*$result = true;
        if($subject->getTypeId() == 'simple'){
            $result = $this->helper->getIsAvailable($subject->getId(), $subject->getSku(), 'available');        
        }
        else if($subject->getTypeId() == 'configurable'){
            $_children = $subject->getTypeInstance()->getUsedProducts($subject);
            $i = 0;
            $j = 0;
            $response = false;
            foreach ($_children as $child){
                
                $response = $this->helper->getIsAvailable($child->getID(), $child->getSku(), 'available');   
                if(!$response){
                    $j++;
                }
                $i++;
            }
            if($j == $i){
                $result = false;
            }
        } */   
        return $result;
    }

    
}