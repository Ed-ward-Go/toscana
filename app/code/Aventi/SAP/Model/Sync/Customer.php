<?php

namespace Aventi\SAP\Model\Sync;

use Aventi\Credit\Api\CreditRepositoryInterface;
use Aventi\Credit\Api\Data\CreditInterfaceFactory;
use Aventi\Credit\Api\Data\CreditInterfaceFactoryCreditInterfaceFactory;
use Aventi\Credit\Model\CreditRepository;
use Aventi\SAP\Helper\Attribute;
use Aventi\SAP\Helper\Data;
use Aventi\SAP\Helper\DataEmail;
use Aventi\SAP\Helper\SAP;
use Aventi\SAP\Model\AbstractSync;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressExtensionFactory;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\AccountManagement;
use Magento\Directory\Model\Region;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Helper\Table;

class Customer extends AbstractSync
{
    /**
         * @var DirectoryList
         */
    private $directoryList;
    /**
     * @var Magento\Framework\Filesystem
     */
    private $filesystem;
    /**
     * @var Data
     */
    private $data;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customer;
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $destinationDirectory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterfac
     */
    private $encryptorInterface;
    /**
     * @var \Magento\Tax\Model\TaxClass\Source\Product
     */
    private $productTaxClassSource;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var Attribute
     */
    private $attributeDate;
    /**
     * @var ProductFactory
     */
    private $product;
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    protected $file;
    /**
     * @var \Aventi\SAP\Api\Data\CustomerPortfolioInterfaceFactory
     */
    private $customerPortfolioInterfaceFactory;
    /**
     * @var ResourceModel\CustomerPortfolio
     */
    private $customerPortfolio;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var CustomerPortfolioRepository
     */
    private $customerPortfolioRepository;
    /**
     * @var CustomerPortfolioFactory
     */
    private $customerPortfolioFactory;

    private $brandsOption = [];

    const APLICATION = 1;
    /**
     * @var SourceItemInterfaceFactory
     */
    private $sourceItemInterfaceFactory;
    /**
     * @var SourceItemsSaveInterface
     */
    private $sourceItemsSaveInterface;
    /**
     * @var AddressInterfaceFactory
     */
    private $dataAddressFactory;
    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;
    /**
     * @var SAP
     */
    private $helperSAP;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var CustomerInterfaceFactory
     */
    private $customerInterfaceFactory;
    /**
     * @var \Aventi\ManagerPrice\Api\PropertyProductRepositoryInterface
     */
    private $propertyProductRepository;
    /**
     * @var \Aventi\ManagerPrice\Api\PropertiesProductsRepositoryInterface
     */
    private $propertiesProductsRepository;
    /**
     * @var CategoryLinkManagementInterface
     */
    private $categoryLinkManagement;
    /**
     * @var \Aventi\ManagerPrice\Api\BrandProductRepositoryInterface
     */
    private $brandProductRepository;
    /**
     * @var DataEmail
     */
    private $dataEmail;

    private $regionFactory;

    /**
     * @var DateTime
     */
    private $_timezone;

    /**
     * @var  AccountManagementInterface
     */
    private $customerAccountManagement;

    /**
     * @var  AddressExtensionFactory
     */
    private $addressExtensionFactory;
    /**
     * @var Aventi\Credit\Model\CreditRepository
     */
    private $creditRepository;
    /**
     * @var CreditInterfaceFactoryCreditInterfaceFactory
     */
    private $creditInterfaceFactory;
    /**
     * @var CreditRepositoryInterface
     */
    private $creditRepositoryInterface;

    /**
     * Customer constructor.
     * @param DirectoryList $directoryList
     * @param Filesystem $filesystem
     * @param Data $data
     * @param LoggerInterface $logger
     * @param CustomerRepositoryInterface $customer
     * @param StoreManagerInterface $storeManager
     * @param EncryptorInterface $encryptorInterface
     * @param \Magento\Tax\Model\TaxClass\Source\Product $productTaxClassSource
     * @param ProductRepositoryInterface $productRepository
     * @param Attribute $attributeDate
     * @param ProductFactory $product
     * @param StockRegistryInterface $stockRegistry
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SourceItemInterfaceFactory $sourceItemInterfaceFactory
     * @param SourceItemsSaveInterface $sourceItemsSaveInterface
     * @param AddressInterfaceFactory $dataAddressFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param SAP $helperSAP
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterfaceFactory $customerInterfaceFactory
     * @param CategoryLinkManagementInterface $categoryLinkManagement
     * @param DataEmail $dataEmail
     * @param Region $region
     * @param DateTime $timezone
     * @param AccountManagementInterface $customerAccountManagement
     * @param AddressExtensionFactory $addressExtensionFactory
     * @param CreditRepository $creditRepository
     * @param CreditInterfaceFactory $creditInterfaceFactory
     * @param CreditRepositoryInterface $creditRepositoryInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        DirectoryList $directoryList,
        Filesystem $filesystem,
        Data $data,
        LoggerInterface $logger,
        CustomerRepositoryInterface $customer,
        StoreManagerInterface $storeManager,
        EncryptorInterface $encryptorInterface,
        \Magento\Tax\Model\TaxClass\Source\Product $productTaxClassSource,
        ProductRepositoryInterface $productRepository,
        Attribute $attributeDate,
        ProductFactory $product,
        StockRegistryInterface $stockRegistry,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceItemInterfaceFactory $sourceItemInterfaceFactory,
        SourceItemsSaveInterface $sourceItemsSaveInterface,
        AddressInterfaceFactory $dataAddressFactory,
        AddressRepositoryInterface $addressRepository,
        SAP $helperSAP,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterfaceFactory $customerInterfaceFactory,
        CategoryLinkManagementInterface $categoryLinkManagement,
        DataEmail $dataEmail,
        Region $region,
        DateTime $timezone,
        AccountManagementInterface $customerAccountManagement,
        AddressExtensionFactory $addressExtensionFactory,
        CreditRepository $creditRepository,
        CreditInterfaceFactory $creditInterfaceFactory,
        CreditRepositoryInterface $creditRepositoryInterface
    ) {
        $this->directoryList = $directoryList;
        $this->filesystem = $filesystem;
        $this->data = $data;
        $this->logger = $logger;
        $this->customer = $customer;
        $this->destinationDirectory = $this->filesystem->
        getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->storeManager = $storeManager;
        $this->encryptorInterface = $encryptorInterface;
        $this->productTaxClassSource = $productTaxClassSource;
        $this->productRepository = $productRepository;
        $this->attributeDate = $attributeDate;
        $this->product = $product;
        $this->stockRegistry = $stockRegistry;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceItemInterfaceFactory = $sourceItemInterfaceFactory;
        $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
        $this->dataAddressFactory = $dataAddressFactory;
        $this->addressRepository = $addressRepository;
        $this->helperSAP = $helperSAP;
        $this->customerRepository = $customerRepository;
        $this->customerInterfaceFactory = $customerInterfaceFactory;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->regionFactory = $region;
        $this->dataEmail = $dataEmail;
        $this->_timezone = $timezone;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->addressExtensionFactory = $addressExtensionFactory;
        $this->creditRepository = $creditRepository;
        $this->creditInterfaceFactory = $creditInterfaceFactory;
        $this->creditRepositoryInterface = $creditRepositoryInterface;
    }

    public function managerCustomerAddress($option = 1)
    {
        $start =  $new = $updated =  $error= 0;
        $rows = 500;
        $siguiente = true;
        $date = date('Y-m-d', strtotime($this->_timezone->date('Y-m-d')));
        if ($option != 0) {
            $date = "1900-01-01";
        }
        $method = 'api/SocioNegocio/Direcciones/%s/%s/%s';
        while ($siguiente) {
            $total = 0;
            $jsonPath = $this->data->getRecourse(sprintf($method, $start, $rows, $date));
            if (is_string($jsonPath) and !empty($jsonPath)) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter(\Bcn\Component\Json\Reader::TYPE_OBJECT);
                $total = $reader->read("total");
                $customersAddress = $reader->read("data", \Bcn\Component\Json\Reader::TYPE_OBJECT);
                $progressBar = $this->startProgressBar($total);
                $new = $updated = $error = 0;
                foreach ($customersAddress as $address) {
                    $response = $this->managerAddress(
                        $address['CardCode'],
                        $address['Address'],
                        $address['Street'],
                        $address['AdresType'],
                        $address['State'],
                        $address['City'],
                        $address['Phone1']
                    );

                    $new += $response['new'];
                    $error += $response['error'];
                    $updated += $response['updated'];
                    $this->advanceProgressBar($progressBar);
                }

                @unlink($jsonPath);
                $start += $rows;
                $this->finishProgressBar($progressBar, $start, $rows);
                $progressBar = null;
            }
            if ($total <= 0) {
                $siguiente = false;
            }
        }
        /*if ($output) {
            $table = new Table($output);
            $table
                ->setRows([
                    ['Customers New', $new],
                    ['Customers Updated', $updated],
                    ['Errors', $error],
                ]);
            $table->render();
        }*/
    }

    /**
     * Manager of customer address
     *
     * @param $CardCode
     * @param $address
     * @param $street
     * @param $region
     * @param $city
     * @param $telefono
     * @method
     * date 20/06/19/02:06 PM
     * @author Carlos Hernan Aguilar Hurtado <caguilar@aventi.co>
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function managerAddress($CardCode, $address, $street, $addressType, $region, $city, $telefono)
    {
        $city = (is_null($city) ? 'QUITO' : $city);
        $street = (is_null($street) ? 'SIN DIRECCIÓN' : $street);
        $postalCode = $this->helperSAP->getPostalCodeByCity($city);
        $customerId = $this->helperSAP->getCustomerId($CardCode);
        $telefono = (is_null($telefono) ? 'SN' : $telefono);
        $new = $update = $error = 0;
        if (is_numeric($customerId) && $postalCode) {
            $customer = $this->customer->getById($customerId);
            $name = $customer->getFirstname();
            $addressId = $this->helperSAP->managerCustomerAddressSAP($address, $customerId);
            if (is_numeric($addressId)) {
                $customerAddress = $this->addressRepository->getById($addressId);
                $update++;
            } else {
                $customerAddress = $this->dataAddressFactory->create();
                $new++;
            }
            $regionId = 0;
            $regionParent = $region;
            if (!is_null($region)) {
                $region = $this->regionFactory->loadByCode($region, 'EC');
                $regionId = $region->getId();
            } else {
                $region = $this->helperSAP->getRegionByCity($city);
                $region = $this->regionFactory->load($region);
                $regionId = 0;
                if ($region) {
                    $regionId = $region->getId();
                }
            }
            // load region on the
            // basis of state name and country id

            $customerAddress->setCustomerId($customerId)
                ->setFirstname($name)
                ->setLastname('.')
                ->setCountryId('EC')
                //->setRegion($region)
                ->setRegionId($regionId)
                ->setPostcode($postalCode)
                ->setCity($city)
                ->setTelephone($telefono)
                ->setIsDefaultShipping(1)
                ->setIsDefaultBilling(1)
                ->setStreet(['0' => $street]);
            try {
                $this->addressRepository->save($customerAddress);
                $this->helperSAP->managerCustomerAddressSAP($address, null, $customerAddress->getId());
            } catch (\Exception $e) {
                $this->logger->error(print_r(func_get_args(), true) . $e->getMessage());
            }
        } else {
            $error++;
        }
        return [
            'new' => $new,
            'updated' => $update,
            'error' => $error

        ];
    }

    public function customer($option = 0)
    {
        $start =  $new = $check =  $error= 0;
        $rows = 1000;
        $siguiente = true;
        $date = date('Y-m-d', strtotime($this->_timezone->date('Y-m-d')));
        if ($option != 0) {
            $date = "1900-01-01";
        }
        while ($siguiente) {
            $jsonPath = $this->data->getRecourse(sprintf('api/SocioNegocio/%s/%s/%s', $start, $rows, $date));
            $total = 0;
            if (is_string($jsonPath) and !empty($jsonPath)) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter(\Bcn\Component\Json\Reader::TYPE_OBJECT);
                $total = $reader->read("total");
                $customers = $reader->read("data");
                $progressBar = $this->startProgressBar($total);
                $new = $check = $error = 0;
                foreach ($customers as $customer) {
                    $data = [
                        "code" => $customer['CardCode'],
                        "email" => $customer['E_Mail'],
                        "name" => $customer['CardName'],
                        "slpCode" => $customer['SlpCode'],
                        "status" => (($customer['frozenFor'] == 'Y') ? 1 : 0),
                        "creditLine" => $customer['CreditLine'],
                        "orderTotal" => $customer['TOTALPEDIDO'],
                        "toPurchase" => $customer['PORCOBRAR'],
                        "identification" => $customer['LicTradNum'],
                        "source" => $customer['U_GC_SUCURSAL']
                    ];
                    $response = $this->managerCustomer(
                        $data
                    );
                    $new += $response['new'];
                    $error += $response['error'];
                    $check += $response['check'];
                    $this->advanceProgressBar($progressBar);
                }

                @unlink($jsonPath);
                $start += $rows;
                $this->finishProgressBar($progressBar, $start, $rows);
                $progressBar = null;
            }
            if ($total <= 0) {
                $siguiente = false;
            }
        }
    }

    public function managerCustomer($data)
    {
        $new = $found = $check = $error = 0;
        $customer = '';
        $lastName = '.';

        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            try {
                $customer = $this->customer->get($data['email']);
                $checkCustomer = $this->checkCustomer($data, $customer);
                if ($checkCustomer) {
                    $check = 1;
                    return [
                        'new' => $new,
                        'found' => $found,
                        'check' => $check,
                        'error' => $error
                    ];
                }
                $customer->setStoreId(1);
                $customer->setWebsiteId(1);
                $customer->setEmail($data['email']);
                $customer->setFirstname($data['name']);
                $customer->setLastName($lastName);
                $customer->setCustomAttribute('sap_customer_id', $data['code']);
                $customer->setCustomAttribute('slp_code', $data['slpCode']);
                $customer->setCustomAttribute('identification_customer', $data['identification']);
                $customer->setCustomAttribute('warehouse_group', $data['source']);
                $customer->setCustomAttribute('is_blocked', $data['status']);
                /*$customer->setCustomAttribute('owner_code', $owner_code);
                $customer->setCustomAttribute('user_code', $user_code);*/
                //$customer->setCustomAttribute('type_customer', $typeCustomer);
                $this->customerRepository->save($customer);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                try {
                    if($data['status'] == 0){

                        $new = 1;
                        $customer = $this->customerInterfaceFactory->create();
                        $customer->setStoreId(1);
                        $customer->setWebsiteId(1);
                        $customer->setEmail($data['email']);
                        $customer->setFirstname($data['name']);
                        $customer->setLastName($lastName);
                        $customer->setCustomAttribute('sap_customer_id', $data['code']);
                        $customer->setCustomAttribute('slp_code', $data['slpCode']);
                        $customer->setCustomAttribute('identification_customer', $data['identification']);
                        $customer->setCustomAttribute('warehouse_group', $data['source']);
                        $this->customerAccountManagement->createAccount($customer);
                        /*$customer->setCustomAttribute('owner_code', $owner_code);
                        $customer->setCustomAttribute('user_code', $user_code);*/
                        //$this->customerRepository->save($customer);
                        /*$this->customerAccountManagement->initiatePasswordReset(
                            $data['email'],
                            AccountManagement::EMAIL_RESET
                        );*/
                    }
                } catch (\Exception $e) {
                    $error = 1;
                    $this->logger->error($e->getMessage());
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                $error = 1;
            }
            if ($customer) {
                try {
                    $customer = $this->customer->get($data['email']);
                    $this->managerSummary($customer->getId(), $data);
                } catch (NoSuchEntityException $e) {
                    $error = 1;
                    $this->logger->error($e->getMessage());
                } catch (LocalizedException $e) {
                    $error = 1;
                    $this->logger->error($e->getMessage());
                }
            }
        }

        return [
            'new' => $new,
            'found' => $found,
            'check' => $check,
            'error' => $error
        ];
    }

    public function managerSummary($customerId, $data)
    {
        $balance = $data['creditLine'] - $data['toPurchase'] - $data['orderTotal'];
        $balance = $balance - $data['creditLine'];
        try {
            $summary = $this->creditRepositoryInterface->getByCustomerId($customerId);
            $summary->setCredit($data['creditLine']);
            $summary->setBalance($balance);
            $this->creditRepository->save($summary);
        } catch (\Exception $e) {
            $summary = $this->creditInterfaceFactory->create();
            $summary->setCustomerId($customerId);
            $summary->setCredit($data['creditLine']);
            $summary->setBalance($balance);
            try {
                $this->creditRepository->save($summary);
            } catch (LocalizedException $e) {
                $this->logger->error("Error credit: " . $e->getMessage());
            }
        }
    }

    /**
     * @param $customerId
     * @param $shippingDefault
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setDefaultShipping($customerId, $shippingDefault)
    {
        $addressId = $this->helperSAP->managerCustomerAddressSAP($shippingDefault, $customerId);
        if ($addressId) {
            $customerAddress = $this->addressRepository->getById($addressId);
            $customerAddress->setIsDefaultShipping(true);
            try {
                $this->addressRepository->save($customerAddress);
            } catch (\Exception $e) {
                $this->logger->error(print_r(func_get_args(), true) . $e->getMessage());
            }
        }
    }

    public function checkCustomer($data, $customer)
    {
        $summaryCredit = 0;
        $current = [
            "code" => $data['code'],
            "email" => $data['email'],
            "name" => $data['name'],
            "slpCode" => $data['slpCode'],
            "creditLine" => $data['creditLine'],
            "identification" => $data['identification'],
            "source" => $data['source']
        ];
        try {
            $summary = $this->creditRepository->getByCustomerId($customer->getId());
            $summaryCredit = $summary->getCredit();
        } catch (NoSuchEntityException $e) {
            $summaryCredit = 0;
        }
        $head = [
            "code" => ($customer->getCustomAttribute('sap_customer_id')) ? $customer->getCustomAttribute('sap_customer_id')->getValue() : 0 ,
            "email" => $customer->getEmail(),
            "name" => $customer->getFirstName(),
            "slpCode" => ($customer->getCustomAttribute('slp_code')) ? $customer->getCustomAttribute('slp_code')->getValue() : 0 ,
            "identification" => ($customer->getCustomAttribute('identification_customer')) ? $customer->getCustomAttribute('identification_customer')->getValue() : 0 ,
            "source" => ($customer->getCustomAttribute('warehouse_group')) ? $customer->getCustomAttribute('warehouse_group')->getValue() : 0 ,
            "creditLine" => $summaryCredit
        ];

        $checkStock = array_diff($current, $head);

        if (empty($checkStock)) {
            return true;
        }
        return false;
    }
}
