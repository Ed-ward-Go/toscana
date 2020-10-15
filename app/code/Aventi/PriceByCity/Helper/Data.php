<?php
declare(strict_types=1);

namespace Aventi\PriceByCity\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    
    /**
     * \Aventi\LocationPopup\Helper\Data
     *
     * @var mixed
     */
    private $helperLocation;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    private $filterGroupBuilder;
    /**
     * @var \Aventi\PriceByCity\Model\StorePriceRepository
     */
    private $storePriceRepository;              

    /**          
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepositoryInterface;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $cart;

    /**
     * @var  \Magento\InventoryApi\Api\SourceItemRepositoryInterface
     */
    private $sourceItemRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Aventi\LocationPopup\Helper\Data $helperLocation,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,        
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Aventi\PriceByCity\Model\StorePriceRepository $storePriceRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepository        
    ) {
        parent::__construct($context);
        $this->helperLocation = $helperLocation;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;           
        $this->storePriceRepository = $storePriceRepository;
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cart = $cart;
        $this->sourceItemRepository = $sourceItemRepository;        
    }

    public function calculatePriceByRegion($product_id){

        try {
            $region = $this->helperLocation->getValue()['region'];            
            $postcode = $this->helperLocation->getValue()['postcode'];
            if( $postcode == "11001"){
                $region = $postcode;
            }
            $priceFinal = $this->getPriceByProductAndRegion($region, $product_id);            
            
            if(!$priceFinal){
                return 0;
            } 
                       
            return $priceFinal;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->error($e->getMessage());
            return 0;
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return 0;
        }        

    }

    public function getPriceByProductAndRegion($region, $product_id){
        $connection = $this->resourceConnection->getConnection();
        $sql = <<<SQL
        SELECT a.price
        FROM aventi_pricebycity_storeprice a
        WHERE a.region_id = '__REGION__' AND a.product_id = '__PRODUCT__'
SQL;

        $sql = str_replace(['__REGION__', '__PRODUCT__'], [$region, $product_id], $sql);        
        $id = $connection->fetchOne($sql);
        return (is_numeric($id)) ? $id : null;
    }

    public function updateItemsInCart($quote){
           
        $deleted = [];
        if($quote){

            foreach ($quote->getAllItems() as $item ) {
            
                $price = $this->calculatePriceByregion($item->getProductId());
    
                if($price == 0){
                    
                    $deleted[] = [
                        'product' => $item->getName(),
                        'sku' => $item->getSku(),
                    ];                     
                    $this->cart->removeItem($item->getItemId())->save();
                    continue;
                }
    
                $item->setCustomPrice($price);
                $item->setOriginalCustomPrice($price);                
                $item->getProduct()->setIsSuperMode(true);
                $item->save();
            }
    
            $quoteObject = $this->cartRepositoryInterface->get($quote->getId());
            $quoteObject->setTriggerRecollect(1);
            $quoteObject->setIsActive(true);
            $quoteObject->collectTotals()->save();

        }        

        return $deleted;
    }

    public function getIsAvailable($product_id, $sku, $option = 'available'){
        try {
            $region = $this->helperLocation->getValue()['region'];            
            
            $sourceCode = $this->getSourceIdByProductAndRegion($region, $product_id);            
            $available = $this->getQtyAvailable($sku, $sourceCode, $option);            
                       
            return $available;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->error($e->getMessage());
            return 0;
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return 0;
        }        
    }

    public function getSourceIdByProductAndRegion($region, $product_id){
        $connection = $this->resourceConnection->getConnection();
        $sql = <<<SQL
        SELECT a.source_id
        FROM aventi_city_inventory_equivalence a
        WHERE a.region_id = '__REGION__' AND a.product_id = '__PRODUCT__'
SQL;

        $sql = str_replace(['__REGION__', '__PRODUCT__'], [$region, $product_id], $sql);        
        $store = $connection->fetchOne($sql);
        return $store;
    }

    public function getQtyAvailable($sku, $sourceCode, $option = 'stock'){
        /*$filterGroup = $this->filterGroupBuilder;
        $filterGroup->addFilter(
            $this->filterBuilder
                ->setField('source_code')
                ->setConditionType('eq')
                ->setValue($sourceCode)                    
                ->create()
        );*/
        $filter1 = $this->filterBuilder
            ->setField("sku")
            ->setValue($sku)
            ->setConditionType("eq")->create();

        $filterGroup1 = $this->filterGroupBuilder
            ->addFilter($filter1)->create();            

        $filter2 = $this->filterBuilder
            ->setField("source_code")
            ->setValue($sourceCode)
            ->setConditionType("eq")->create();

        $filterGroup2 = $this->filterGroupBuilder
            ->addFilter($filter2)->create();            
            
        $searchCriteria = $this->searchCriteriaBuilder
                ->setFilterGroups([$filterGroup1, $filterGroup2])
                ->create();
        $items = $this->sourceItemRepository->getList($searchCriteria)->getItems();
        
        $isAvailable = false;
        $stock = 0;
        $exist = null;
        foreach ($items as $key => $item) {
            $exist = $item;
            if($item->getStatus()){
                
                $isAvailable = (($item->getQuantity() > 0) ? true : false );
                $stock = $item->getQuantity();
            }            

        }
        $response = '';
        if($option == 'available'){
            $response = $isAvailable;
        }else if($option == 'stock'){
            $response = $stock;
        }else if($option == 'exist'){
            $response = $exist;
        }

        return $response;

    }

}
