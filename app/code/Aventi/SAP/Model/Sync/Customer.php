<?php

namespace Aventi\SAP\Model\Sync;

use Bcn\Component\Json\Reader;
use Magento\Customer\Model\AccountManagement;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;

class Customer
{
    /**
         * @var \Magento\Framework\App\Filesystem\DirectoryList
         */
    private $directoryList;
    /**
     * @var Magento\Framework\Filesystem
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
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customer;
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $destinationDirectory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
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

    private $file;
    /**
     * @var \Aventi\SAP\Api\Data\CustomerPortfolioInterfaceFactory
     */
    private $customerPortfolioInterfaceFactory;
    /**
     * @var ResourceModel\CustomerPortfolio
     */
    private $customerPortfolio;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
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
     * @var \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory
     */
    private $sourceItemInterfaceFactory;
    /**
     * @var \Magento\InventoryApi\Api\SourceItemsSaveInterface
     */
    private $sourceItemsSaveInterface;
    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
     */
    private $dataAddressFactory;
    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $addressRepository;
    /**
     * @var \Aventi\SAP\Helper\SAP
     */
    private $helperSAP;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
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
     * @var \Magento\Catalog\Api\CategoryLinkManagementInterface
     */
    private $categoryLinkManagement;
    /**
     * @var \Aventi\ManagerPrice\Api\BrandProductRepositoryInterface
     */
    private $brandProductRepository;
    /**
     * @var \Aventi\SAP\Helper\DataEmail
     */
    private $dataEmail;

    private $regionFactory;

    /**
     * @var \Aheadworks\Ca\Model\CompanyRepository
     */
    private $companyRepository;

    /**
     * @var \Aheadworks\Ca\Api\Data\CompanyInterface
     */
    private $companyInterface;

    /**
     * @var \Aheadworks\Ca\Model\Company\CompanyManagement
     */
    private $companyManagement;

    /**
     * @var \Aheadworks\Ca\Model\Service\CompanyUserService
     */
    private $companyUserService;

    /**
     * @var \Aheadworks\Ca\Api\Data\CompanyInterfaceFactory
     */
    private $companyInterfaceFactory;

    /**
     * @var \Aheadworks\CreditLimit\Model\Transaction\CreditSummaryManagement
     */
    private $summaryManagment;

    /**
     * @var \Aheadworks\CreditLimit\Api\SummaryRepositoryInterface
     */
    private $summaryRepository;

    /**
     * @var \Aheadworks\CreditLimit\Api\Data\SummaryInterfaceFactory
     */
    private $summaryInterfaceFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $_timezone;

    /**
     * @var  \Magento\Customer\Api\AccountManagementInterface
     */
    private $customerAccountManagement;

    /**
     * @var  \Magento\Customer\Api\Data\AddressExtensionFactory
     */
    private $addressExtensionFactory;

    /**
     * Task constructor.
     *
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param Magento\Framework\Filesystem $filesystem
     * @param \Aventi\SAP\Helper\Data $data
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Customer\Model\CustomerFactory $customer
     */
    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem $filesystem,
        \Aventi\SAP\Helper\Data $data,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Api\CustomerRepositoryInterface $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Encryption\EncryptorInterface $encryptorInterface,
        \Magento\Tax\Model\TaxClass\Source\Product $productTaxClassSource,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Aventi\SAP\Helper\Attribute $attributeDate,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemInterfaceFactory,
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSaveInterface,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $dataAddressFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Aventi\SAP\Helper\SAP $helperSAP,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerInterfaceFactory,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement,
        \Aventi\SAP\Helper\DataEmail $dataEmail,
        \Magento\Directory\Model\Region $region,
        \Magento\Framework\Stdlib\DateTime\DateTime $timezone,
        \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
        \Magento\Customer\Api\Data\AddressExtensionFactory $addressExtensionFactory
    ) {
        $this->directoryList = $directoryList;
        $this->filesystem = $filesystem;
        $this->data = $data;
        $this->logger = $logger;
        $this->customer = $customer;
        $this->destinationDirectory = $this->filesystem->
        getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
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

    public function managerCustomerAddress($option = 1)
    {
        $start =  $new = $updated =  $error= 0;
        $rows = 500;
        $siguiente = true;
        $output = $this->getOutput();
        $method = ($option == 1) ? 'api/SocioNegocio/Direcciones/%s/%s' : 'api/SocioNegocio/DireccionesRapido/%s/%s';
        while ($siguiente) {
            $jsonPath = $this->data->getRecourse(sprintf($method, $start, $rows));
            if (is_string($jsonPath) and !empty($jsonPath)) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter(\Bcn\Component\Json\Reader::TYPE_OBJECT);
                $total = $reader->read("total");
                $customersAddress = $reader->read("data", \Bcn\Component\Json\Reader::TYPE_OBJECT);
                if ($output) {
                    $progressBar = new ProgressBar($output, $total);
                    $progressBar->start();
                }
                $new = $updated = $error = 0;
                foreach ($customersAddress as $address) {
                    $response = $this->managerAddress(
                        $address['CardCode'],
                        $address['Address'],
                        $address['Street'],
                        $address['AdresType'],
                        $address['State'],
                        $address['City'],
                        $address['Phone1'],
                        true,
                        false,
                        $address['Serie'],
                        $address['U_G_Bodega']
                    );

                    $new += $response['new'];
                    $error += $response['error'];
                    $updated += $response['updated'];
                    if ($output) {
                        $progressBar->advance();
                    }
                }
                $this->closeFile();
                @unlink($jsonPath);
                $start += $rows;
                if ($output) {
                    $output->writeln(sprintf("\nInteraction %s", ($start/$rows)));
                    $progressBar->finish();
                }
                $progressBar = null;
            } else {
                if ($output) {
                    $output->writeln('Archivo invalido');
                }
            }
            if ($total <= 0) {
                $siguiente = false;
            }
        }
        if ($output) {
            $table = new Table($output);
            $table
                ->setRows([
                    ['Customers New', $new],
                    ['Customers Updated', $updated],
                    ['Errors', $error],
                ]);
            $table->render();
        }
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
    public function managerAddress($CardCode, $address, $street, $addressType, $region, $city, $telefono, $option = true, $customerChild = false, $serie, $group)
    {
        $city = (is_null($city) ? 'QUITO' : $city);
        $street = (is_null($street) ? 'SIN DIRECCIÓN' : $street);
        $postalCode = $this->helperSAP->getPostalCodeByCity($city);
        $customerId = ($customerChild) ? $customerChild : $this->helperSAP->getCustomerId($CardCode);
        $telefono = (is_null($telefono) ? 'SN' : $telefono);
        $new = $update = $error = 0;
        $groupAndSerie = (is_null($serie) || is_null($group)) ? false : true;
        if (is_numeric($customerId) && $postalCode && $groupAndSerie) {
            $customer = $this->customer->getById($customerId);
            $name = $customer->getFirstname();
            if (!$option) {
                $customerParent = $this->customer->getById($this->helperSAP->getCustomerId($CardCode));
                $name = $customerParent->getFirstname();
            }
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
            /*$customerAddressExtensionAttributes = $customerAddress->getExtensionAttributes();
            $extensionAttributes = $customerAddressExtensionAttributes ? $customerAddressExtensionAttributes : $this->addressExtensionFactory->create();
            $extensionAttributes->setSerie($serie);
            $extensionAttributes->setWarehouseGroup($group);
            $customerAddress->setExtensionAttributes($extensionAttributes);*/

            //$this->logger->error("Extensions: ". $customerAddress->getExtensionAttributes()->getSerie() .' : ' . $customerAddress->getExtensionAttributes()->getWarehouseGroup() . ' ' . $customerId);
            $customerAddress->setCustomAttribute('warehouse_group', $group);
            $customerAddress->setCustomAttribute('serie', $serie);
            try {
                $this->addressRepository->save($customerAddress);
                $this->helperSAP->managerCustomerAddressSAP($address, null, $customerAddress->getId());
            } catch (\Exception $e) {
                $this->logger->error(print_r(func_get_args(), true) . $e->getMessage());
            }
            if ($option) {
                $customerChilds = $this->companyUserService->getChildUsers($customerId);

                if ($customerChilds) {
                    foreach ($customerChilds as $childs) {
                        $this->managerAddress($CardCode, $address, $street, $addressType, $regionParent, $city, $telefono, false, $childs->getId(), $serie, $group);
                    }
                }
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

    public function customer()
    {
        $start =  $new = $updated =  $error= 0;
        $rows = 1000;
        $siguiente = true;
        while ($siguiente) {
            $jsonPath = $this->data->getRecourse(sprintf('api/Cliente/%s/%s', $start, $rows));
            $total = 0;
            if (is_string($jsonPath) and !empty($jsonPath)) {
                $reader = $this->task->getJsonReader($jsonPath);
                $reader->enter(\Bcn\Component\Json\Reader::TYPE_OBJECT);
                $total = $reader->read("total");
                $customers = $reader->read("data", \Bcn\Component\Json\Reader::TYPE_OBJECT);
                $new = $updated = $error = 0;
                foreach ($customers as $customer) {
                    $response = $this->managerCustomer(
                        $customer['CardCode'],
                        $customer['Mail'],
                        $customer['CardName'],
                        $customer['GroupCode'],
                        $customer['GroupName'],
                        $customer['Telefono'],
                        $customer['U_clas_clientes1']
                    );
                    $new += $response['new'];
                    $error += $response['error'];
                    $updated += $response['updated'];
                }
                $this->task->closeFile();
                @unlink($jsonPath);
                $start += $rows;
                $progressBar = null;
            }
            if ($total <= 0) {
                $siguiente = false;
            }
        }
    }

    public function company($option = 1)
    {
        $start =  $new = $updated =  $error= 0;
        $rows = 500;
        $siguiente = true;
        $output = $this->getOutput();
        $method = ($option == 1) ? 'api/SocioNegocio/%s/%s' : 'api/SocioNegocio/Rapido/%s/%s';
        while ($siguiente) {
            $jsonPath = $this->data->getRecourse(sprintf($method, $start, $rows));
            $total = 0;
            if (is_string($jsonPath) and !empty($jsonPath)) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter(\Bcn\Component\Json\Reader::TYPE_OBJECT);
                $total = $reader->read("total");
                $customers = $reader->read("data", \Bcn\Component\Json\Reader::TYPE_OBJECT);
                if ($output) {
                    $progressBar = new ProgressBar($output, $total);
                    $progressBar->start();
                }
                $new = $updated = $error = 0;
                foreach ($customers as $customer) {
                    $status = ($customer['frozenFor'] == 'N') ? 'approved' : 'pending_approval';
                    $email = $customer['E_Mail'];
                    if (strpos($email, ';') !== false) {
                        $email = substr($email, 0, strpos($email, ";"));
                    }
                    if (strpos($email, ',') !== false) {
                        $email = substr($email, 0, strpos($email, ","));
                    }
                    if (is_null($email)) {
                        $email = 'siglo21@siglo21.com';
                    }
                    $response = $this->managerCompany(
                        $customer['CardCode'],
                        $status,
                        $customer['CardName'],
                        $customer['CardFName'],
                        $customer['CardName'],
                        $customer['LicTradNum'],
                        $email,
                        null,
                        null,
                        $customer['Street'],
                        $customer['City'],
                        $customer['State'],
                        $customer['Phone1'],
                        $customer['GroupCode'],
                        $customer['Balance'],
                        $customer['CreditLine'],
                        $customer['SlpCode'],
                        $customer['empID'],
                        $customer['User_Code'],
                        $customer['ShipToDef']
                    );
                    $new += $response['new'];
                    $error += $response['error'];
                    $updated += $response['updated'];
                    if ($output) {
                        $progressBar->advance();
                    }
                }
                $start += $rows;
                if ($output) {
                    $output->writeln(sprintf("\nInteraction %s", ($start / $rows)));
                    $progressBar->finish();
                }
                $this->closeFile();
                @unlink($jsonPath);
                $progressBar = null;
                if ($total <= 0) {
                    $siguiente = false;
                }
            }
        }
    }
    /**
     * Register and Updated customer
     *
     * @param string $cn
     * @param string $email
     * @param string $fullName
     * @param string $groupCode
     * @param string $groupName
     * @param $telefono
     * @method
     * date 20/06/19/09:51 AM
     * @author Carlos Hernan Aguilar Hurtado <caguilar@aventi.co>
     * @return array
     */
    public function managerCustomer($cn = '', $email = '', $fullName = '', $groupCode = '', $groupName = '', $telefono='', $typeCustomer='', $output=false)
    {
        if ($output) {
            $output->writeln("Gestionado el cliente " . $email);
        }

        $update = $new = $error = 0;
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                $customer = $this->customer->get($email);
                $customer->setStoreId(0);
                $customer->setFirstname($fullName);
                $customer->setCustomAttribute('sap_customer_id', $cn);
                $customer->setCustomAttribute('type_customer', $typeCustomer);
                $this->customerRepository->save($customer);
                $update = 1;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                try {
                    $customer = $this->customerInterfaceFactory->create();
                    $customer->setStoreId(0);
                    $customer->setEmail($email);
                    $customer->setFirstname($fullName);
                    $customer->setCustomAttribute('sap_customer_id', $cn);
                    $customer->setCustomAttribute('type_customer', $typeCustomer);
                    $password = $this->randomPassword();
                    $this->customerRepository->save($customer, $this->encryptorInterface->getHash($password, true));
                    $this->dataEmail->sendEmail($email, $fullName, $password);
                    $new = 1;
                } catch (\Exception $e) {
                    $error = 1;
                    $this->logger->error($e->getMessage());
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                $error = 1;
            }
        }
        return ['error' => $error, 'new' => $new, 'updated' => $update];
    }

    public function managerCompany(
        $id,
        $status,
        $name,
        $lastName,
        $legalName,
        $licTradNum,
        $email,
        $tax = null,
        $reseller = null,
        $address,
        $city,
        $region,
        $telephone = null,
        $typeCustomer = '',
        $balance,
        $creditLimit,
        $slp_code,
        $owner_code,
        $user_code,
        $defaultShipping
    ) {
        $error = $update = $new = 0;
        try {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $companyId = $this->helperSAP->managerCompanySAP($id);
                $company = '';
                $city = (strpos($city, '?') !== false) ? 'QUITO' : $city;
                $city = (is_null($city)) ? "QUITO" : $city;
                if (!is_null($region)) {
                    $region = $this->regionFactory->loadByCode($region, 'EC');
                } else {
                    $region = $this->helperSAP->getRegionByCity($city);
                    $region = $this->regionFactory->load($region);
                }

                $postalCode = $this->helperSAP->getPostalCode($region->getName(), $city);

                $adminCustomer = $this->createAdminCustomer($id, $licTradNum, $name, $lastName, $email, $typeCustomer, $slp_code, $owner_code, $user_code);
                try {
                    $company = $this->companyRepository->get($companyId);
                    $company->setRootGroupId(1);
                    $company->setStatus($status);
                    $company->setName($name);
                    $company->setLegalName($legalName);
                    $company->setEmail($email);
                    $company->setTaxId($tax);
                    $company->setReSellerId($reseller);
                    $company->setStreet($address);
                    $company->setCity($city);
                    $company->setCountryId("EC");
                    $company->setRegion($region->getName());
                    $company->setRegionId($region->getId());
                    $company->setPostcode($postalCode);
                    $company->setTelephone($telephone);
                    $company->setIsAllowedToQuote(1);
                    $company->setAllowedPaymentMethods([]);
                    $company = $this->companyManagement->updateCompany($company, $adminCustomer);
                    $update++;
                } catch (\Exception $e) {
                    $company = $this->companyInterfaceFactory->create();
                    $company->setRootGroupId(1);
                    $company->setStatus($status);
                    $company->setName($name);
                    $company->setLegalName($legalName);
                    $company->setEmail($email);
                    $company->setTaxId($tax);
                    $company->setReSellerId($reseller);
                    $company->setStreet($address);
                    $company->setCity($city);
                    $company->setCountryId("EC");
                    $company->setRegion($region->getName());
                    $company->setRegionId($region->getId());
                    $company->setPostcode($postalCode);
                    $company->setTelephone($telephone);
                    $company->setIsAllowedToQuote(1);
                    $company->setAllowedPaymentMethods([]);
                    $company = $this->companyManagement->createCompany($company, $adminCustomer);
                    $new++;
                }
                $this->setDefaultShipping($adminCustomer->getId(), $defaultShipping);
                $this->helperSAP->managerCompanySAP($id, $company->getId());
                $this->managerSummary($adminCustomer->getId(), $company->getId(), $balance, $creditLimit);
            }
        } catch (\Exception $e) {
            $error = 1;
            $this->logger->error($e->getMessage());
        }
        return ['error' => $error, 'new' => $new, 'updated' => $update];
    }

    public function createAdminCustomer($cn, $licTradNum, $firstName, $lastName, $email, $typeCustomer, $slp_code, $owner_code, $user_code)
    {
        $customer = '';
        $lastName = (is_null($lastName) ? '. ' : $lastName . ' .');
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                $customer = $this->customer->get($email);
                $customer->setStoreId(1);
                $customer->setEmail($email);
                $customer->setFirstname($firstName);
                $customer->setLastName($lastName);
                $customer->setCustomAttribute('sap_customer_id', $cn);
                $customer->setCustomAttribute('slp_code', $slp_code);
                $customer->setCustomAttribute('identification_customer', $licTradNum);
                $customer->setCustomAttribute('owner_code', $owner_code);
                $customer->setCustomAttribute('user_code', $user_code);
                //$customer->setCustomAttribute('type_customer', $typeCustomer);
                //$customer = $this->customerRepository->save($customer);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                try {
                    $customer = $this->customerInterfaceFactory->create();
                    $customer->setStoreId(1);
                    $customer->setEmail($email);
                    $customer->setFirstname($firstName);
                    $customer->setLastName($lastName);
                    $customer->setCustomAttribute('sap_customer_id', $cn);
                    $customer->setCustomAttribute('slp_code', $slp_code);
                    $customer->setCustomAttribute('identification_customer', $licTradNum);
                    $customer->setCustomAttribute('owner_code', $owner_code);
                    $customer->setCustomAttribute('user_code', $user_code);
                    //$customer->setCustomAttribute('type_customer', $typeCustomer);
                    //$password = "Adm1n001";
                    //$customer = $this->customerRepository->save($customer /*$this->encryptorInterface->getHash($password, true)*/);

                    /*if($status == 'approved'){
                        $this->customerAccountManagement->initiatePasswordReset(
                            'emerz@aventi.com.co',
                            AccountManagement::EMAIL_RESET
                        );
                    }*/
                } catch (\Exception $e) {
                    $error = 1;
                    $this->logger->error($e->getMessage());
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                $error = 1;
            }
        }

        return $customer;
    }

    public function managerSummary($customerId, $companyId, $creditBalance, $creditLimit)
    {
        $date = $this->_timezone->gmtDate();
        $availableCredit = (($creditLimit) - ($creditBalance));
        $creditBalance = $creditBalance * -1;

        try {
            $summary = $this->summaryRepository->getByCustomerId($customerId);
            $summary->setCustomerId($customerId);
            $summary->setCompanyId($companyId);
            $summary->setCreditLimit($creditLimit);
            $summary->setWebsiteId(1);
            $summary->setCreditBalance($creditBalance);
            $summary->setCreditAvailable($availableCredit);
            $summary->setLastPaymentDate($date);
            $summary->setCurrency('USD');
            $this->summaryManagment->saveCreditSummary($summary);
        } catch (\Exception $e) {
            $summary = $this->summaryInterfaceFactory->create();
            $summary->setCustomerId($customerId);
            $summary->setCompanyId($companyId);
            $summary->setCreditLimit($creditLimit);
            $summary->setWebsiteId(1);
            $summary->setCreditBalance($creditBalance);
            $summary->setCreditAvailable($availableCredit);
            $summary->setLastPaymentDate($date);
            $summary->setCurrency('USD');
            $this->summaryManagment->saveCreditSummary($summary);
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
}
