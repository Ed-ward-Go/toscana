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
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    private $sourceRepositoryInterface;
    /**
     * @var \Aventi\PriceByCity\Api\PriceByCityRepositoryInterface
     */
    private $priceByCityRepositoryInterface;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Aventi\LocationPopup\Helper\Data $helperLocation
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepository
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepositoryInterface
     * @param \Aventi\PriceByCity\Api\PriceByCityRepositoryInterface $priceByCityRepositoryInterface
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Aventi\LocationPopup\Helper\Data $helperLocation,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepository,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepositoryInterface,
        \Aventi\PriceByCity\Api\PriceByCityRepositoryInterface $priceByCityRepositoryInterface
    ) {
        parent::__construct($context);
        $this->helperLocation = $helperLocation;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cart = $cart;
        $this->sourceItemRepository = $sourceItemRepository;
        $this->sourceRepositoryInterface = $sourceRepositoryInterface;
        $this->priceByCityRepositoryInterface = $priceByCityRepositoryInterface;
    }

    public function calculatePriceBySource($product_id)
    {
        try {
            $source = $this->helperLocation->getValue();
            $priceFinal = $this->getPriceByProductAndSource($product_id, $source['id']);

            if (!$priceFinal) {
                return 0;
            }

            return $priceFinal->getPrice();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->error($e->getMessage());
            return 0;
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return 0;
        }
    }

    public function updateItemsInCart($quote)
    {
        $deleted = [];
        if ($quote) {
            foreach ($quote->getAllItems() as $item) {
                $price = $this->calculatePriceBySource($item->getProductId());

                if ($price == 0 || !$price) {
                    $deleted[] = [
                        'product' => $item->getName(),
                        'sku' => $item->getSku(),
                    ];
                    $this->cart->removeItem($item->getItemId())->save();
                    continue;
                }
                $this->logger->error("TEST FROM HELPER: ". $price);
                $item->setCustomPrice($price);
                $item->setOriginalCustomPrice($price);
                $item->getProduct()->setIsSuperMode(true);
                $item->save();
            }
            if($quote->getAllItems()){
                $quoteObject = $this->cartRepositoryInterface->get($quote->getId());
                $quoteObject->setTriggerRecollect(1);
                $quoteObject->setIsActive(true);
                $this->logger->error("TEST FROM HELPER 2: ". $quote->getId());
                $quoteObject->collectTotals()->save();
            }
        }

        return $deleted;
    }

    public function getSourceByPriceList($priceList)
    {
        $filter1 = $this->filterBuilder
            ->setField("fax")
            ->setValue($priceList)
            ->setConditionType("eq")->create();

        $filterGroup1 = $this->filterGroupBuilder
            ->addFilter($filter1)->create();

        $filter2 = $this->filterBuilder
            ->setField("enabled")
            ->setValue(1)
            ->setConditionType("eq")->create();

        $filterGroup2 = $this->filterGroupBuilder
            ->addFilter($filter2)->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->setFilterGroups([$filterGroup1, $filterGroup2])
            ->create();
        $items = $this->sourceRepositoryInterface->getList($searchCriteria)->getItems();

        $exist = null;
        foreach ($items as $key => $item) {
            $exist = $item;
        }

        return $exist;
    }

    /**
     * @param $productId
     * @param $source
     * @return \Aventi\PriceByCity\Api\Data\PriceByCityInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPriceByProductAndSource($productId, $source)
    {
        $filter1 = $this->filterBuilder
            ->setField("product_id")
            ->setValue($productId)
            ->setConditionType("eq")->create();

        $filterGroup1 = $this->filterGroupBuilder
            ->addFilter($filter1)->create();

        $filter2 = $this->filterBuilder
            ->setField("source_code")
            ->setValue($source)
            ->setConditionType("eq")->create();

        $filterGroup2 = $this->filterGroupBuilder
            ->addFilter($filter2)->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->setFilterGroups([$filterGroup1, $filterGroup2])
            ->create();
        $items = $this->priceByCityRepositoryInterface->getList($searchCriteria)->getItems();

        $exist = null;
        foreach ($items as $key => $item) {
            $exist = $item;
        }

        return $exist;
    }
}
