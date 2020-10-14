<?php

namespace Aventi\SAP\Model\Sync;

use Bcn\Component\Json\Reader;
use Symfony\Component\Console\Helper\ProgressBar;

class Product
{

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
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder
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
    }

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * Instance the class reader for json
     *
     * @param $filePath
     * @return Reader
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 14/11/18
     */
    public function getJsonReader($filePath)
    {
        if (file_exists($filePath)) {
            $this->file = fopen($filePath, "r");
            return new Reader($this->file);
        }
    }

    /**
     * Close the file
     *
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 15/11/18
     */
    public function closeFile()
    {
        @fclose($this->file);
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
        $found = $new = $price = $stock = 0;
        $result = array('found' => 0,'new' => 0,'empty' => 0,'check' => 0);

        //$url = $this->generateURL($url);
        if (empty($sku)) {
            $result['empty'] = 1;
            return $result;
        }

        try {
            if ($product = $this->productRepository->get($sku)) {

                $checkProduct = $this->checkProduct($param, $product);
                if($checkProduct){
                    $result['check'] = 1; return $result;
                }
                $result['found'] = 1;
                $product->setStoreId($param['store_id']);
                $product->setName($param['name']);
                $product->setCustomAttribute('tax_class_id', $param['tax']);
                $product->setStatus($param['status']);
                $product->setVisibility($param['visibility']);
                $product->setCustomAttribute('state_slow', $param['state_slow']);
                $product->setCustomAttribute('ref', $param['ref']);
                $product->setCustomAttribute('upc', $param['upc']);
                $product->setCustomAttribute('web_articule', $param['web_articule']);
                $product->setCustomAttribute('bodega_lm', $param['bodega_lm']);
                $product->setCustomAttribute('list_material', $param['list_material']);
                $product->setCustomAttribute('u_marca', $param['u_marca']);
                $product->setData('mgs_brand', $param['brand']);
                $product->setData('type', $param['type_p']);
                $product->setData('class', $param['class_p']);

                $product = $this->productRepository->save($product);
                try {
                    if ($param['category_id'] != null) {
                        $this->categoryLinkManagement->assignProductToCategories(
                            $product->getSku(),
                            [$param['category_id']['Grupo'], $param['category_id']['Tipo'], $param['category_id']['Clase']]
                        );
                    }
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
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
            $product->setCustomAttribute('state_slow', $param['state_slow']);
            $product->setCustomAttribute('ref', $param['ref']);
            $product->setCustomAttribute('upc', $param['upc']);
            $product->setCustomAttribute('web_articule', $param['web_articule']);
            $product->setCustomAttribute('bodega_lm', $param['bodega_lm']);
            $product->setCustomAttribute('list_material', $param['list_material']);
            $product->setCustomAttribute('u_marca', $param['u_marca']);
            $product->setData('mgs_brand', $param['brand']);
            $product->setData('type', $param['type_p']);
            $product->setData('class', $param['class_p']);
            $product->setUrlKey($this->generateURL($product['name']));

            try {
                $this->productRepository->save($product);
                $stockItemFull = $this->stockRegistry->getStockItem($product->getId());
                $stockItemFull->setQty($product['stock']);
                $stockItemFull->setIsInStock(($product['stock'] > 0) ? 1 : 0);
                $stockItemFull->save();
            } catch (\Exception $e) {
                $this->logger->error("El product {$sku} no creo " . $e->getMessage());
            }

            try {
                if ($param['category_id'] != null) {
                    $this->categoryLinkManagement->assignProductToCategories(
                        $product->getSku(),
                        [$param['category_id']['Grupo'], $param['category_id']['Tipo'], $param['category_id']['Clase']]
                    );
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
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
                if(!$this->sourceRepository->get($source)){
                    return [
                        'found' => 0,
                        'notFound' => 1

                    ];
                    $this->logger->error("Source with code ". $source . "don't exist");
                }
                $stockItem = $this->getSourceBySku($sku, $source);

                if(is_null($stockItem)){
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
                $this->sourceItemSave->execute([$stockItem]);

                $found = 1;
            }
            /*if ($stockItem = $this->stockRegistry->getStockItemBySku($sku)) {
                $stockItem->setQty($stock);
                $stockItem->setIsInStock($stock > 0 ? 1 : 0);
                $this->stockRegistry->updateStockItemBySku($sku, $stockItem);
                $found = 1;
            } else {
                $notFound = 1;
            }*/
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
     * @method
     * date 15/04/20/14:25 PM
     * @author Erich Hans Merz Diaz <emerz@aventi.com.co>
     * @return array
     */
    public function managerPrice($sku, $price)
    {
        $found = $notFound = $check = 0;
        try {
            if ($productFull = $this->productRepository->get($sku)) {
                $checkPrice = $this->checkPrice($price, $productFull);
                if ($checkPrice) {
                    $check= 1;
                    return [
                        'found' => $found,
                        'notFound' => $notFound,
                        'check' => $check
                    ];
                }

                $productFull->setPrice($price);
                $this->productRepository->save($productFull);
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
        $output = $this->getOutput();
        $progressBar = null;
        $method = ($option == 0) ? 'api/Producto/%s/%s' : 'api/Producto/Rapido/%s/%s';
        while ($siguiente) {
            $jsonPath = $this->data->getRecourse(sprintf($method, $start, $rows));
            if ($jsonPath != false && $jsonPath != null) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter(Reader::TYPE_OBJECT);
                $total = $reader->read("total");
                $products = $reader->read("data");
                if ($output) {
                    $progressBar = new ProgressBar($output, $total);
                    $progressBar->start();
                }
                foreach ($products as $product) {
                    $status = isset($product['frozenFor']) ? $product['frozenFor'] : '';

                    if ($status == 'N') {
                        $status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
                    } else {
                        $status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED;
                    }

                    $stock  = 0;
                    //$url = $this->generateURL($product['ItemName'].' '.$product['ItemCode'].' '.$product['U_NMarca']);
                    $parent = isset($product['U_GrupoWeb']) ? $product['U_GrupoWeb'] : '';
                    $subparent = isset($product['U_Tipo']) ? $product['U_Tipo'] : '';
                    $child = isset($product['U_Clase']) ? $product['U_Clase'] : '';
                    $categoryId = $this->helperSAP->getCategoryByName($parent, $subparent, $child);

                    $product = array(
                        'sku' =>  isset($product['ItemCode']) ? str_replace(' ', '', $product['ItemCode']) : '',
                        'name' => isset($product['ItemName']) ? $product['ItemName'] : '',
                        'brand' => isset($product['Marca']) ? $this->getOptionId($product['Marca'], 'mgs_brand') : 0,
                        'tax' => isset($product['TaxCodeAR']) ? $this->getTaxId($product['TaxCodeAR']) : '',
                        'status' =>  $status,
                        'state_slow' => ($product['U_Exx_Des_EstadoLento'] == 'S') ? 1 : 0,
                        'ref' => $product['SuppCatNum'],
                        'upc' => $product['CodeBars'],
                        'web_articule' => ($product['U_ArticuloWeb'] == 'Y') ? 1 : 0,
                        'bodega_lm' => $product['BodegaLM'],
                        'list_material' => $product['ListaMateriales'],
                        'u_marca' => $product['U_Marca'],
                        'type_p' => isset($product['Tipo']) ? $this->getOptionId($product['Tipo'], 'type') : 0,
                        'class_p' => isset($product['Clase']) ? $this->getOptionId($product['Clase'], 'class') : 0,
                        'parent' => $parent,
                        'subparent' => $subparent,
                        'child' => $child,
                        'price' => 0,
                        'stock' => $stock,
                        'in_stock' => ($stock > 0) ? 1 : 0,
                        'category_id' => $categoryId,
                        'visibility'=> 4,
                        'store_id' => 0
                    );

                    $response = $this->managerProduct(
                        $product
                    );
                    if ($output) {
                        $progressBar->advance();
                    }
                    //$total--;
                }
                $start += $rows;
                if ($output) {
                    $progressBar->finish();
                    $output->writeln(sprintf("\nInteraction %s", ($start / $rows)));
                }
                $progressBar = null;
                $this->closeFile();
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
        $output = $this->getOutput();
        $method = 'api/Producto/Stock/%s/%s';
        $sourceLm = '';
        switch ($option) {
            case 0:
                $method = 'api/Producto/Stock/%s/%s';
                break;
            case 1:
                $method = 'api/Producto/StockRapido/%s/%s';
                break;
            case 2:
                $method = 'api/Producto/StockPromociones/%s/%s';
                $sourceLm = 'CDLM';
                break;
            default:
                $method = 'api/Producto/Stock/%s/%s';
                break;
        }
        $progressBar = null;
        while ($siguiente) {
            $jsonPath =  $this->data->getRecourse(sprintf($method, $start, $rows));
            if ($jsonPath != false  && $jsonPath != null) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter(\Bcn\Component\Json\Reader::TYPE_OBJECT);
                $total = $reader->read("total");
                $products = $reader->read("data", \Bcn\Component\Json\Reader::TYPE_OBJECT);
                if ($output) {
                    $progressBar = new ProgressBar($output, $total);
                    $progressBar->start();
                }
                foreach ($products as $product) {
                    $stock = ($product['Quantity'] < 0) ? 0 : $product['Quantity'] ;
                    $source = (empty($sourceLm) ? $product['WhsCode'] : $sourceLm );
                    $response = $this->managerStock($product['ItemCode'], $stock, $source);
                    $new += $response['notFound'];
                    $updated += $response['found'];
                    if($output){
                        $progressBar->advance();
                    }
                    // $total--;
                }
                $start += $rows;
                if($output){
                    $progressBar->finish();
                    $output->writeln(sprintf("\nInteraction %s", ($start / $rows)));
                }
                $progressBar = null;
                $this->closeFile();
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
    public function updatePrice($option, $fast = 0)
    {
        $start =  $new = $updated =  $error= 0;
        $rows = 1000;
        $siguiente = true;
        $output = $this->getOutput();
        $method = ($fast == 0) ? 'api/Producto/PreciosPorIdListaPrecio/%s/%s/%s' : 'api/Producto/PreciosPorIdListaPrecioRapido/%s/%s/%s';
        while ($siguiente) {
            $jsonPath =  $this->data->getRecourse(sprintf($method, $start, $rows, $option));
            if ($jsonPath != false  && $jsonPath != null) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter(Reader::TYPE_OBJECT);
                $total = $reader->read("total");
                $products = $reader->read("data", Reader::TYPE_OBJECT);
                $output = $this->getOutput();
                if ($output) {
                    $progressBar = new ProgressBar($output, $total);
                    $progressBar->start();
                }
                $items = 0;
                foreach ($products as $product) {
                    $response =  $this->managerPrice($product['ItemCode'], $product['Price']);
                    $new += $response['notFound'];
                    $updated += $response['found'];
                    if ($output) {
                        $progressBar->advance();
                    }
                    $items++;
                }
                if ($output) {
                    $progressBar->finish();
                }
                $progressBar = null;
                $this->closeFile();
                @unlink($jsonPath);
                $start += $rows;
                if ($output) {
                    $output->writeln(sprintf("\nInteraction %s", ($start / $rows)));
                }
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

    public function getSourceBySku($sku, $source){
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
        foreach ($items as $item){
            $source = $item;
        }

        return $source;
    }

    public function checkProduct($data, $product){

        $arrayValues = [];
        if(is_array($data['category_id'])){
            $arrayValues = array_values($data['category_id']);
        }

        $currentProduct = array(
            'sku' => $data['sku'],
            'name' => $data['name'],
            'brand' => $data['brand'],
            'type_p' => $data['type_p'],
            'class_p' => $data['class_p'],
            'tax' => $data['tax'],
            'status' => $data['status'],
            'state_slow' => $data['state_slow'],
            'ref' => $data['ref'],
            'upc' => $data['upc'],
            'web_articule' => $data['web_articule'],
            'bodega_lm' => $product['bodega_lm'],
            'list_material' => $data['list_material'],
            'u_marca' => $data['u_marca']
        );

        $headProduct = array(
            'sku' =>  $product->getData('sku'),
            'name' =>  $product->getData('name'),
            'brand' => $product->getData('mgs_brand'),
            'type_p' => $product->getData('type'),
            'class_p' => $product->getData('class'),
            'tax' => $product->getData('tax_class_id'),
            'status' => $product->getData('status'),
            'state_slow' => $product->getData('state_slow'),
            'ref' =>  $product->getData('ref'),
            'upc' =>  $product->getData('upc'),
            'web_articule' =>  $product->getData('web_articule'),
            'bodega_lm' =>  $product->getData('bodega_lm'),
            'list_material' =>  $product->getData('list_material'),
            'u_marca' =>  $product->getData('u_marca')
        );

        $categoryDiff = array_diff($product->getCategoryIds(), $arrayValues);
        $checkProduct = array_diff($currentProduct, $headProduct);

        if(empty($checkProduct) && empty($categoryDiff)){
            return true;
        }
        return false;
    }

    public function checkPrice($data, $product)
    {
        $current = [
            'price' => $this->formatDecimalNumber($data)
        ];

        $head = [
            'price' => $product->getPrice()
        ];

        $checkPrice = array_diff($current, $head);
        if (empty($checkPrice)) {
            return true;
        }
        return false;
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

    public function formatDecimalNumber($number)
    {
        return number_format($number, 6, '.', '');
    }


}
