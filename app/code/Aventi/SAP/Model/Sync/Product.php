<?php

namespace Aventi\SAP\Model\Sync;

use Aventi\SAP\Model\AbstractSync;
use Bcn\Component\Json\Reader;
use Symfony\Component\Console\Helper\ProgressBar;

class Product extends AbstractSync
{
    const DEFAULT_SOURCE = 'default';
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;
    /**
     * @var \Aventi\SAP\Helper\Data
     */
    private $data;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Tax\Model\TaxClass\Source\Product
     */
    private $productTaxClassSource;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var \Aventi\SAP\Helper\Attribute
     */
    private $attributeDate;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $product;
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;
    /**
     * @var \Aventi\SAP\Helper\SAP
     */
    private $helperSAP;

    private $destinationDirectory;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output = null;

    /**
     * @var \Magento\Catalog\Api\CategoryLinkManagementInterface
     */
    private $categoryLinkManagement;

    private $arrayOption = [];

    /**
    *
    * @var \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory
    */
    private $sourceItemInterfaceFactory;

    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    private $sourceRepository;

    /**
     * @var \Magento\InventoryApi\Api\SourceItemRepositoryInterface
     */
    private $sourceItemRepositoryInterface;

    /**
     * @var \Magento\InventoryApi\Api\SourceItemsSaveInterface
     */
    private $sourceItemSave;

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
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;
    /**
     * @var \Aventi\PriceByCity\Helper\Data
     */
    private $priceByCityHelper;
    /**
     * @var \Aventi\PriceByCity\Api\Data\PriceByCityInterfaceFactory
     */
    private $priceByCityInterfaceFactory;
    /**
     * @var \Aventi\PriceByCity\Api\PriceByCityRepositoryInterface
     */
    private $priceByCityRepository;
    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $_eventManager;
    /**
     * @var array
     */
    private $arrayItems = [];
    /**
     * @var string
     */
    private $lastItem;

    /**
     * Product constructor.
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Aventi\SAP\Helper\Data $data
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Tax\Model\TaxClass\Source\Product $productTaxClassSource
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Aventi\SAP\Helper\Attribute $attributeDate
     * @param \Magento\Catalog\Model\ProductFactory $product
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Aventi\SAP\Helper\SAP $helperSAP
     * @param \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagementInterface
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemInterfaceFactory
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     * @param \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepositoryInterface
     * @param \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Aventi\PriceByCity\Helper\Data $priceByCityHelper
     * @param \Aventi\PriceByCity\Api\Data\PriceByCityInterfaceFactory $priceByCityInterfaceFactory
     * @param \Aventi\PriceByCity\Api\PriceByCityRepositoryInterface $priceByCityRepository
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem $filesystem,
        \Aventi\SAP\Helper\Data $data,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Tax\Model\TaxClass\Source\Product $productTaxClassSource,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Aventi\SAP\Helper\Attribute $attributeDate,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Aventi\SAP\Helper\SAP $helperSAP,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagementInterface,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemInterfaceFactory,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository,
        \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepositoryInterface,
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Aventi\PriceByCity\Helper\Data $priceByCityHelper,
        \Aventi\PriceByCity\Api\Data\PriceByCityInterfaceFactory $priceByCityInterfaceFactory,
        \Aventi\PriceByCity\Api\PriceByCityRepositoryInterface $priceByCityRepository,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->directoryList = $directoryList;
        $this->filesystem = $filesystem;
        $this->data = $data;
        $this->logger = $logger;
        $this->productTaxClassSource = $productTaxClassSource;
        $this->productRepository = $productRepository;
        $this->attributeDate = $attributeDate;
        $this->product = $product;
        $this->stockRegistry = $stockRegistry;
        $this->helperSAP = $helperSAP;
        $this->categoryLinkManagement = $categoryLinkManagementInterface;
        $this->destinationDirectory = $this->filesystem->
        getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
        $this->resourceConnection = $resourceConnection;
        $this->sourceItemInterfaceFactory = $sourceItemInterfaceFactory;
        $this->sourceRepository = $sourceRepository;
        $this->sourceItemSave = $sourceItemSave;
        $this->sourceItemRepositoryInterface = $sourceItemRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->dateTime = $dateTime;
        $this->priceByCityHelper = $priceByCityHelper;
        $this->priceByCityInterfaceFactory = $priceByCityInterfaceFactory;
        $this->priceByCityRepository = $priceByCityRepository;
        $this->categoryRepository = $categoryRepository;
        $this->_eventManager = $eventManager;
    }
    /**
     * @return mixed
     */
    public function getLastItem()
    {
        return $this->lastItem;
    }

    /**
     * @param $item
     */
    public function setLastItem($item)
    {
        $this->lastItem = $item;
    }

    /**
     * @return mixed
     */
    public function getArrayItems()
    {
        return $this->arrayItems;
    }

    /**
     * @param $arrayItems
     */
    public function setArrayItems($arrayItems)
    {
        $this->arrayItems[] = $arrayItems;
    }

    public function unsArrayItems()
    {
        $this->arrayItems = [];
    }
    /**
     * @param $sku
     * @param $name
     * @param $tax
     * @param $status
     * @param $brand
     * @param $web_group
     * @param $type
     * @param $class
     * @param $s_slow
     * @return array
     *
     *
     * Not Visible Individually = 1
     * Catalog, Search = 4
     * @author Carlos Hernan Aguilar Hurado <caguilar@aventi.co>
     * @date 28/04/20
     */
    public function managerProduct($param)
    {
        $sku = str_replace(' ', '', $param['sku']);
        $stock = $param['stock'];
        $found = $new = $price = $stock = 0;
        $result = ['found' => 0,'new' => 0,'empty' => 0,'check' => 0];

        //$url = $this->generateURL($url);
        if (empty($sku)) {
            $result['empty'] = 1;
            return $result;
        }

        try {
            if ($product = $this->productRepository->get($sku)) {
                $checkProduct = $this->checkProduct($param, $product);
                if ($checkProduct == 0) {
                    $result['check'] = 1;
                    return $result;
                }
                $result['found'] = 1;
                $this->_saveFields($product, $checkProduct);
                $this->saveCategories($param, $product);
                $this->_eventManager->dispatch(
                    'catalog_product_update_after_sync',
                    ['product' => $product]
                );
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) { // Product no found

            $result['new'] = 1;
            $stock = 0;
            $product = $this->product->create();
            $product->setSku($sku);
            $product->setStoreId(0);
            $product->setVisibility(4);
            $product->setName($param['name']);
            $product->setPrice($param['price']);
            $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
            $product->setAttributeSetId(4);
            $product->setCustomAttribute('tax_class_id', $param['tax']);
            $product->setStatus($param['status']);
            $product->setVisibility($param['visibility']);
            $product->setData('mgs_brand', $param['brand']);
            $product->setData('presentation', $param['presentation']);
            $product->setData('business_line', $param['business_line']);
            $product->setData('format', $param['format']);
            $product->setUrlKey($this->generateURL($param['name']));

            try {
                $this->productRepository->save($product);
                $stockItemFull = $this->stockRegistry->getStockItem($product->getId());
                $stockItemFull->setQty($stock);
                $stockItemFull->setIsInStock(($stock > -1) ? 1 : 0);
                $stockItemFull->save();

                $stockItem = $this->getSourceBySku($sku, self::DEFAULT_SOURCE);

                if (is_null($stockItem)) {
                    $stockItem = $this->sourceItemInterfaceFactory->create();
                }

                $stockItem->setSourceCode(self::DEFAULT_SOURCE);
                $stockItem->setSku($sku);
                $stockItem->setQuantity($stock);
                $stockItem->setStatus($stock > -1 ? 1 : 0);
                $this->sourceItemSave->execute([$stockItem]);
                $this->_assignProductToCategories($param, $product);
            } catch (\Exception $e) {
                $this->logger->error("El product {$sku} no actulizó " . $e->getMessage());
            }

        } catch (\Exception $e) {
            $this->logger->error($sku . '-->' . $e->getMessage());
        }
        return $result;
    }

    /**
     * @param $sku
     * @param $stock
     * @param string $source
     * @method
     * date 15/04/20/11:00 AM
     * @author Erich Hans Merz Diaz <emerz@aventi.com.co>
     * @return array
     */
    public function managerStock($sku, $stock, $source = 'default')
    {
        $found = $notFound = $check = 0;
        try {
            // $sku = $this->helperSAP->getSkuBySAP($sku);

            if ($sku == null) {
                return [
                    'found' => 0,
                    'notFound' => 1

                ];
            }
            if ($product = $this->productRepository->get($sku)) {
                if (!$this->sourceRepository->get($source)) {
                    return [
                        'found' => 0,
                        'notFound' => 1

                    ];
                    $this->logger->error("Source with code " . $source . "don't exist");
                }
                $stockItem = $this->getSourceBySku($sku, $source);

                if (is_null($stockItem)) {
                    $stockItem = $this->sourceItemInterfaceFactory->create();
                }
                $checkStock = $this->checkStock($stock, $stockItem);
                if ($checkStock) {
                    $check= 1;
                    return [
                        'found' => $found,
                        'notFound' => $notFound,
                        'check' => $check
                    ];
                }

                $stockItem->setSourceCode($source);
                $stockItem->setSku($sku);
                $stockItem->setQuantity($stock);
                $stockItem->setStatus($stock > 0 ? 1 : 0);
                $this->assignToSource($sku, $stockItem);

                $found = 1;
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $notFound = 1;
        }

        return [
            'found' => $found,
            'notFound' => $notFound,
            'check' => $check
        ];
    }

    /**
     * @param $sku
     * @param $price
     * @param $priceList
     * @return array
     * @method
     * date 15/04/20/14:25 PM
     * @author Erich Hans Merz Diaz <emerz@aventi.com.co>
     */
    public function managerPrice($sku, $price, $priceList)
    {
        $found = $notFound = $check = 0;
        try {
            if ($productFull = $this->productRepository->get($sku)) {
                $source = $this->priceByCityHelper->getSourceByPriceList($priceList);
                if ($source) {
                    $sourceCode = $source->getSourceCode();
                    $productId = $productFull->getId();
                    $priceBySource = $this->priceByCityHelper->getPriceByProductAndSource($productId, $sourceCode);
                    $checkPrice = [];
                    if (!$priceBySource) {
                        $priceBySource = $this->priceByCityInterfaceFactory->create();
                    } else {
                        $checkPrice = $this->checkPrice($priceBySource, $productFull);
                    }

                    if ($checkPrice == 0) {
                        $check= 1;
                        return [
                            'found' => $found,
                            'notFound' => $notFound,
                            'check' => $check
                        ];
                    }
                    $priceBySource->setSourceCode($sourceCode);
                    $priceBySource->setPrice($price);
                    $priceBySource->setProductId($productId);
                    $this->priceByCityRepository->save($priceBySource);
                    $this->_saveFields($productFull, $checkPrice);
                    /*$productFull->setPrice($price);
                    $this->productRepository->save($productFull);*/
                    $found = 1;
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $notFound = 1;
        }

        return [
            'found' => $found,
            'notFound' => $notFound,
            'check' => $check
        ];
    }

    /**
     * Get the tax_id
     *
     * @param $value
     * @return null
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 15/11/18
     */
    public function getTaxId($value)
    {
        $taxClassess = $this->productTaxClassSource->getAllOptions();

        foreach ($taxClassess as $tax) {
            if ($tax['label'] == $value) {
                return $tax['value'];
            }
        }
        return 0;
    }

    /**
     * Generate pretty url by products
     *
     * @param $name
     * @return string
     */
    public function generateURL($name)
    {
        $url = preg_replace('#[^0-9a-z]+#i', '-', $name);
        $url = strtolower($url);
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName('url_rewrite');
        $sql = <<<SQL
            SELECT count(*) from url_rewrite where request_path = '_URL_.html'
SQL;
        $sql = str_replace('_URL_', $url, $sql);
        $results = $this->resourceConnection->getConnection()->fetchOne($sql);

        if ($results > 0) {
            return $url . '-' . $this->generateRandomString(5);
        }
        return $url;
    }

    /**
     * Generate in string for the url product
     *
     * @param int $length
     * @return bool|string
     * @author Carlos Hernan Aguilar Hurado <caguilar@aventi.co>
     * @date 15/04/20
     */
    public function generateRandomString($length = 10)
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, $length);
    }

    /**
     * Update or Create the products
     *
     * @author Carlos Hernan Aguilar Hurado <caguilar@aventi.co>
     * @date 15/04/20
     */
    public function updateProduct($option = 0)
    {
        $start = $new = $total = $error = 0;
        $rows = 1000;
        $siguiente = true;
        $date = date('Y-m-d', strtotime($this->dateTime->date('Y-m-d')));
        if ($option != 0) {
            $date = "1900-01-01";
        }
        $progressBar = null;
        $method = 'api/Producto/%s/%s/%s';
        while ($siguiente) {
            $jsonPath = $this->data->getRecourse(sprintf($method, $start, $rows, $date));
            if ($jsonPath != false && $jsonPath != null) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter(Reader::TYPE_OBJECT);
                $total = $reader->read("total");
                $products = $reader->read("data");
                $progressBar = $this->startProgressBar($total);
                foreach ($products as $product) {
                    $status = isset($product['frozenFor']) ? $product['frozenFor'] : '';

                    if ($status == 'N') {
                        $status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
                    } else {
                        $status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED;
                    }

                    $stock  = 0;

                    $categoryId = $this->helperSAP->getLastCategory($product['U_GC_CATEGORIA']);
                    $categoryArray = [];
                    if ($categoryId) {
                        $parentCategory = $this->getParentCategory($categoryId);
                        $categoryArray = [$parentCategory->getParentId(), $categoryId];
                    }

                    $product = [
                        'sku' =>  isset($product['ItemCode']) ? str_replace(' ', '', $product['ItemCode']) : '',
                        'name' => isset($product['ItemName']) ? $product['ItemName'] : '',
                        'brand' => isset($product['Marca']) ? $this->getOptionId($product['Marca'], 'mgs_brand') : 0,
                        'business_line' => isset($product['U_GC_NEGOCIO']) ? $product['U_GC_NEGOCIO'] : '',
                        'format' => isset($product['Formato']) ? $this->getOptionId($product['Formato'], 'format') : 0,
                        'presentation' => isset($product['U_GC_PRESENTACION']) ? $this->getOptionId($product['U_GC_PRESENTACION'], 'presentation') : 0,
                        'tax' => isset($product['TaxCodeAR']) ? $this->getTaxId($product['TaxCodeAR']) : '',
                        'status' =>  $status,
                        'price' => 0,
                        'stock' => $stock,
                        'in_stock' => ($stock > 0) ? 1 : 0,
                        'categoryId' => $categoryArray,
                        'visibility'=> 4,
                        'store_id' => 0
                    ];

                    $response = $this->managerProduct(
                        $product
                    );
                    $this->advanceProgressBar($progressBar);
                    //$total--;
                }
                $start += $rows;
                $this->finishProgressBar($progressBar, $start, $rows);
                $progressBar = null;
                @unlink($jsonPath);

                if ($total <= 0) {
                    $siguiente = false;
                }
            } else {
                $siguiente = false;
            }
        }
    }

    /**
     * Update the stock product
     *
     * @author Erich Hans Merz Diaz <emerz@aventi.com.co>
     * @date 15/04/20
     */
    public function updateStock($option = 0)
    {
        $start =  $new = $updated =  $error= 0;
        $rows = 1000;
        $siguiente = true;
        //$output = $this->getOutput();
        $method = 'api/Producto/Stock/%s/%s';
        $sourceLm = '';
        $method = ($option == 0) ? 'api/Producto/Stock/%s/%s' : 'api/Producto/StockRapido/%s/%s';
        $progressBar = null;
        while ($siguiente) {
            $jsonPath =  $this->data->getRecourse(sprintf($method, $start, $rows));
            if ($jsonPath != false  && $jsonPath != null) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter(\Bcn\Component\Json\Reader::TYPE_OBJECT);
                $total = $reader->read("total");
                $products = $reader->read("data", \Bcn\Component\Json\Reader::TYPE_OBJECT);
                /*if ($output) {
                    $progressBar = new ProgressBar($output, $total);
                    $progressBar->start();
                }*/
                $progressBar = $this->startProgressBar($total);
                foreach ($products as $product) {
                    $stock = ($product['Stock'] < 0) ? 0 : $product['Stock'];
                    $source = (empty($sourceLm) ? $product['WhsCode'] : $sourceLm);
                    $response = $this->managerStock($product['ItemCode'], $stock, $source);
                    $new += $response['notFound'];
                    $updated += $response['found'];
                    /*if($output){
                        $progressBar->advance();
                    }*/
                    $this->advanceProgressBar($progressBar);
                    // $total--;
                }
                $start += $rows;
                /*if($output){
                    $progressBar->finish();
                    $output->writeln(sprintf("\nInteraction %s", ($start / $rows)));
                }*/
                $this->finishProgressBar($progressBar, $start, $rows);
                $progressBar = null;
                //$this->closeFile();
                @unlink($jsonPath);

                if ($total <= 0) {
                    $siguiente = false;
                }
            } else {
                $siguiente = false;
            }
        }
    }

    /**
     * Update the price product
     *
     * @author Erich Hans Merz Diaz <emerz@aventi.com.co>
     * @date 15/04/20
     */
    public function updatePrice($option = 0)
    {
        $start =  $new = $updated =  $error= 0;
        $rows = 1000;
        $siguiente = true;
        $date = date('Y-m-d', strtotime($this->dateTime->date('Y-m-d')));

        if ($option != 0) {
            $date = "1900-01-01";
        }
        $method = 'api/Producto/Precios/%s/%s/%s';
        while ($siguiente) {
            $jsonPath =  $this->data->getRecourse(sprintf($method, $start, $rows, $date));
            if ($jsonPath != false  && $jsonPath != null) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter(Reader::TYPE_OBJECT);
                $total = $reader->read("total");
                $products = $reader->read("data", Reader::TYPE_OBJECT);
                $progressBar = $this->startProgressBar($total);
                $items = 0;
                foreach ($products as $product) {
                    $response =  $this->managerPrice($product['ItemCode'], $product['Price'], $product['PriceList']);
                    $new += $response['notFound'];
                    $updated += $response['found'];
                    $this->advanceProgressBar($progressBar);
                    $items++;
                }
                $start += $rows;
                $this->finishProgressBar($progressBar, $start, $rows);
                $progressBar = null;
                @unlink($jsonPath);

                if ($total <= 0) {
                    $siguiente = false;
                }
            } else {
                $siguiente = false;
            }
        }
    }

    /**
     * Get or create the option by attributes and return the id
     *
     * @param string $label
     * @param string $attributeCode
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @return false|int|mixed
     */
    public function getOptionId($label = '', $attributeCode='marcas')
    {
        try {
            if (!empty($label)) {
                $brand = str_replace(' ', '', $label);
                $optionId = 0;
                if (!array_key_exists($brand, $this->arrayOption)) {
                    $optionId = $this->attributeDate->getOptionId($attributeCode, $label);
                    if (!$optionId) {
                        $optionId = $this->attributeDate->createOrGetId($attributeCode, $label);
                    }
                    $this->arrayOption[$brand] = $optionId;
                } else {
                    $optionId = $this->arrayOption[$brand];
                }
                return $optionId;
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return 0;
        }
    }

    public function getSourceBySku($sku, $source)
    {
        $filter1 = $this->filterBuilder
        ->setField("sku")
        ->setValue($sku)
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
        $items = $this->sourceItemRepositoryInterface->getList($searchCriteria)->getItems();

        $source = null;
        foreach ($items as $item) {
            $source = $item;
        }

        return $source;
    }

    public function checkProduct($data, $product)
    {
        $arrayValues = [];
        if (is_array($data['categoryId'])) {
            $arrayValues = array_values($data['categoryId']);
        }

        $currentProduct = [
            'name' => $data['name'],
            'mgs_brand' => $data['brand'],
            'business_line' => $data['business_line'],
            'format' => $data['format'],
            'presentation' => $data['presentation'],
            'tax' => $data['tax'],
            'status' => $data['status']
        ];

        $headProduct = [
            'name' =>  $product->getData('name'),
            'mgs_brand' => $product->getData('mgs_brand'),
            'business_line' => $product->getData('business_line'),
            'format' => $product->getData('format'),
            'presentation' => $product->getData('presentation'),
            'tax' => $product->getData('tax_class_id'),
            'status' => $product->getData('status')
        ];

        $categoryDiff = array_diff($product->getCategoryIds(), $arrayValues);
        $checkProduct = array_diff($currentProduct, $headProduct);

        if (empty($checkProduct) && empty($categoryDiff)) {
            return 0;
        }
        return $checkProduct;
    }

    public function checkPrice($data, $product)
    {
        $current = [
            'price' => $this->formatDecimalNumber($data->getPrice())
        ];

        $head = [
            'price' => $product->getPrice()
        ];

        $checkPrice = array_diff($current, $head);
        if (empty($checkPrice)) {
            return 0;
        }
        return $checkPrice;
    }

    public function checkStock($data, $stock)
    {
        $current = [
            'stock' => $data
        ];

        $head = [
            'stock' => $stock->getQty()
        ];

        $checkStock = array_diff($current, $head);

        if (empty($checkStock)) {
            return true;
        }
        return false;
    }

    public function getParentCategory($childCategory)
    {
        return $this->categoryRepository->get($childCategory);
    }

    public function formatDecimalNumber($number)
    {
        return number_format($number, 6, '.', '');
    }

    /**
     * @param $product
     * @param $fields
     */
    private function _saveFields($product, $fields)
    {
        foreach ($fields as $key => $field) {
            $product->setData($key, $field);
            try {
                $product->getResource()->saveAttribute($product, $key);
            } catch (\Exception $e) {
                echo $e->getMessage() . "\n";
            }
        }
    }

    /**
     * @param $sku
     * @param $stockItem
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function assignToSource($sku, $stockItem)
    {
        if (empty($this->getLastItem())) {
            $this->setLastItem($sku);
        }
        if ($sku != $this->getLastItem()) {
            $this->sourceItemSave->execute($this->getArrayItems());
            $this->unsArrayItems();
        }
        $this->setArrayItems($stockItem);
        $this->setLastItem($sku);
    }

    /**
     * check categories
     *
     * @param $data
     * @param $product
     * @return true
     * @author <adria.olave@gmail.com>
     * @date 15/04/20
     *
     * */

    public function saveCategories($data, $product)
    {
        $categoriesDiff = array_diff($product->getCategoryIds(), $data['categoryId']);
        if (!empty($categoriesDiff)) {
            $this->_assignProductToCategories($data, $product);
        }
    }

    /**
     * @param $data
     * @param $product
     * @return void
     * @author by aventi <adrian.oalve@gmail.com>
     */
    private function _assignProductToCategories($data, $product)
    {
        try {
            if ($data['categoryId'] != null) {
                $this->categoryLinkManagement->assignProductToCategories(
                    $product->getSku(),
                    $data['categoryId']
                );
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
