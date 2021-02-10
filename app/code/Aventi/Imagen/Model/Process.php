<?php

namespace Aventi\Imagen\Model;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Symfony\Component\Console\Helper\Table;

class Process
{

    /**
     * @var \Aventi\Imagen\Helper\Data
     */
    private $data;
    /**
     * @var ImagenRepository
     */
    private $imagenRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var \Magento\Catalog\Model\Product\Gallery\ReadHandler
     */
    private $readHandler;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Gallery
     */
    private $gallery;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output = null;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var Imagen
     */
    private $imagen;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem $filesystem,
        \Aventi\Imagen\Helper\Data $data,
        \Aventi\Imagen\Model\ImagenRepository $imagenRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\Product\Gallery\ReadHandler $readHandler,
        \Magento\Catalog\Model\ResourceModel\Product\Gallery $gallery,
        \Psr\Log\LoggerInterface $logger,
        \Aventi\Imagen\Model\Imagen $imagen,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->data = $data;
        $this->imagenRepository = $imagenRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->readHandler = $readHandler;
        $this->gallery = $gallery;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->imagen = $imagen;
        $this->directoryList = $directoryList;
        $this->filesystem = $filesystem;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @return null
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param null $output
     */
    public function setOutput(\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->output = $output;
    }
    /**
     * Process the imagen products
     *
     * @author Carlos Hernan Aguilar Hurado <caguilar@aventi.co>
     * @date 28/04/20
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function update()
    {
        $pathImages = $this->data->getPathImage();

        if (empty($pathImages)) {
            return true;
        }

        $this->writeIn(__('Path origin the data ') . $pathImages);
        $resume = [
            'total' => 0,
            'completed' => 0 ,
            'noFound' => 0 ,
            'NoProcessing' => 0
        ];

        /*$directoryTemporal =  $this->directoryList->getPath('media') . sprintf('/import/');

        if (!is_dir($directoryTemporal)) {
            mkdir($directoryTemporal);
        }*/

        /**
         * Assumed images are named [sku].[ext]
         */
        foreach (glob($pathImages . "*.{jpg,png,gif}", GLOB_BRACE) as $image) {
            $resume['total'] += 1;
            $imageFileName = trim(pathinfo($image)['filename']);
            $imagenExtension = trim(pathinfo($image)['extension']);
            $imageBase = trim(pathinfo($image)['basename']);
            if (!$this->imageRegister($imageBase) && in_array($imagenExtension, ['png','jpg'])) {
                try {
                    $this->logger->error($imageFileName);
                    $imageFileName = explode('_', $imageFileName);
                    $collection = $this->productCollectionFactory->create();
                    $collection = $collection->addAttributeToSelect(['id', 'sku'])
                    ->addAttributeToSort('created_at', 'desc')
                    ->addAttributeToFilter('sku', ["eq" => (isset($imageFileName[0]) ? $imageFileName[0] : $imageFileName)]);
                    foreach ($collection as $item) {
                        $product = $this->productRepository->get($item->getSku());
                        if ($product) {
                            if (file_exists($image)) {
                                /** Add image */
                                if (isset($imageFileName[1])) {
                                    if ($imageFileName[1] == "1") {
                                        $product->addImageToMediaGallery($image, [
                                          'image',
                                          'small_image',
                                          'thumbnail'
                                        ], false, false);
                                    } else {
                                        $product->addImageToMediaGallery($image, null, false, false);
                                    }
                                    $this->productRepository->save($product);
                                }

                                $this->imagen->setData(['image' =>$imageBase]);
                                $this->imagen->save();
                                $this->imagen->setId(null);
                                $resume['completed'] += 1;
                                //\Magento\Framework\Backup\unlink($directoryTemporal.$imageBase);
                            }
                        }
                    }
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $resume['noFound'] += 1;
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                    $this->writeIn($e->getMessage());
                }
            } else {
                $resume['NoProcessing'] += 1;
            }
        }

        $this->resumen(array_values($resume));
        return $resume;
    }
    /**
     * print data
     *
     * @author Carlos Hernan Aguilar Hurado <caguilar@aventi.co>
     * @date 28/04/20
     * @param $message
     */
    public function writeIn($message)
    {
        $output = $this->getOutput();
        if ($output) {
            $output->writeln($message);
        }
    }
    /**
     * Find the imagen
     *
     * @author Carlos Hernan Aguilar Hurado <caguilar@aventi.co>
     * @date 28/04/20
     * @param $name
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function imageRegister($name)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            'image',
            $name,
            'eq'
        )->create();
        $items = $this->imagenRepository->getList($searchCriteria);
        return ($items->getTotalCount() > 0) ? true : false;
    }
    /**
     * Print the resume
     *
     * @author Carlos Hernan Aguilar Hurado <caguilar@aventi.co>
     * @date 28/04/20
     * @param array $data
     */
    private function resumen($data=[])
    {
        $output = $this->getOutput();
        if ($output) {
            $table = new table($output);
            $table
                ->setHeaders(['Total', 'Complete', 'Product no found', 'No processing'])
                ->setRows([$data]);
            $table->render();
        }
    }
}
