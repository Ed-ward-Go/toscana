<?php

namespace Aventi\SAP\Helper;

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
    const XML_PATH_PTK_SAP_PASSWORD_CUSTOMER = 'sap/setting/customer_password';
    const XML_PATH_PTK_SAP_PATH = 'sap/setting/url';
    const XML_PATH_PTK_SAP_USERNAME = 'sap/setting/username';
    const XML_PATH_PTK_SAP_PASSWORD = 'sap/setting/password';

    const PATH_TOKEN = 'token';
    const PATH_API_CUSTOMER = '/api/customer';
    const PATH_API_PRODUCT = '/api/Producto';
    const PATH_API_CARTERA = '/api/Cliente/Cartera';
    const PATH_API_STOCK = '/api/Producto/Stock';

    const PATH_EMAIL = 'sap/customer/sendemail';
    const PATH_CC = 'sap/customer/copy';

    const PATH_SERIE = 'sap/document/serie';
    const PATH_WHSCODE = 'sap/document/whscode';
    const PATH_CARDCODE = 'sap/document/cardcode';
    const PATH_SHIPPINGCODE = 'sap/document/shipping';
    const PATH_DOCDUEDATE = 'sap/document/docduedate';
    const PATH_TEST = 'sap/setting/test';

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $curl;
    /**
     * @var
     */
    private $token=null;

    private $logger;

    private $destinationDirectory;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->curl = $curl;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
        $this->directoryList = $directoryList;
        $this->destinationDirectory = $this->filesystem->
        getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
    }

    public function sendEmail($store = null)
    {
        return (int)$this->scopeConfig->getValue(self::PATH_EMAIL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    public function copyEmail($store = null)
    {
        return (string)$this->scopeConfig->getValue(self::PATH_CC, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Return the default serie to create document in SAP
     * @param null $store
     * @return string
     */
    public function getSerie($store = null)
    {
        return (string)$this->scopeConfig->getValue(self::PATH_SERIE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Return the default warehouse code to items to create document in SAP
     * @param null $store
     * @return string
     */
    public function getWhsCode($store = null)
    {
        return (string)$this->scopeConfig->getValue(self::PATH_WHSCODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Return the default CardCode create document in SAP
     * @param null $store
     * @return string
     */
    public function getCardCode($store = null)
    {
        return (string)$this->scopeConfig->getValue(self::PATH_CARDCODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Return the Shipping Product Code to create document in SAP
     * @param null $store
     * @return string
     */
    public function getShippingCode($store = null)
    {
        return (string)$this->scopeConfig->getValue(self::PATH_SHIPPINGCODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Return the DocDueDate days to create document in SAP
     * @param null $store
     * @return int
     */
    public function getDocDueDate($store = null)
    {
        return (int)$this->scopeConfig->getValue(self::PATH_DOCDUEDATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    /**
     * @param null $store
     * @author Carlos Hernan Aguilar Hurtado <caguilar@aventi.co>
     * @return string
     */
    public function getPassword($store = null)
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_PTK_SAP_PASSWORD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null $store
     * @author Carlos Hernan Aguilar Hurtado <caguilar@aventi.co>
     * @return string
     */
    public function getUsername($store = null)
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_PTK_SAP_USERNAME, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null $store
     * @author Carlos Hernan Aguilar Hurtado <caguilar@aventi.co>
     * @return string
     */
    public function getPath($store = null)
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_PTK_SAP_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null $store
     * @author Carlos Hernan Aguilar Hurtado <caguilar@aventi.co>
     * @return string
     */
    public function getPasswordForCustomer($store = null)
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_PTK_SAP_PASSWORD_CUSTOMER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null $store
     * @return bool
     */
    public function getIsTest($store = null)
    {
        return (boolean)$this->scopeConfig->getValue(self::PATH_TEST, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Generate token
     * @author Carlos Hernan Aguilar Hurtado <caguilar@aventi.co>
     *
     * @return bool
     */
    public function generateToken()
    {
        $url = $this->getPath();
        if (!empty($url)) {
            $url = $url . '/' . self::PATH_TOKEN;
            $this->curl->post($url, [
                'Username' => $this->getUsername(),
                'Password' => $this->getPassword(),
                'grant_type' => 'password'
            ]);
            $response = $this->curl->getBody();

            if ($this->curl->getStatus()  == 400) {
            } elseif ($this->curl->getStatus() == 200) {
                $responseArray = json_decode($response, true);
                $this->setToken($responseArray['access_token']);
                return true;
            } else {
                $this->setToken(null);
                return false;
            }
        } else {
            $this->logger->error('Modulo Aventi::SAP url indefinida');
        }
        return false;
    }

    /**
     * @param string $path
     * @param int $tries
     * @return bool|string
     */
    public function getRecourseSelf($path = '', $tries=0)
    {
        try {
            if (!empty($this->getPath())) {
                $headers = [
                "Content-Type" => "application/json"
              ];

                if (!$this->getIsTest()) {
                    if ($this->getToken() == null) {
                        $this->generateToken();
                    }
                    $headers = [
                      "Content-Type" => "application/json",
                      "Authorization" =>  "Bearer {$this->getToken()}"
                  ];
                }
                $this->curl->setHeaders($headers);
                $url = $this->getPath() . '/' . $path;
                $this->logger->error("TST: ". $url);
                $this->curl->get($url);
                if ($this->curl->getStatus() == 200) {
                    return $this->curl->getBody();
                } elseif ($this->curl->getStatus() == 401) {
                    $this->generateToken();
                    if ($tries == 0) {
                        return $this->getRecourseSelf($path, 1);
                    }
                } else {
                    $this->_logger->error($this->curl->getBody() . ' ' . $this->curl->getStatus());
                    return false;
                }
            }
        } catch (\Exception $e) {
            $this->_logger->error($e->getCode() . $e->getMessage());
        }
    }

    public function postRecourse($path = '', $params)
    {
        try {
            if (!empty($this->getPath())) {
                $headers = [

              ];
                if (!$this->getIsTest()) {
                    if ($this->getToken() == null) {
                        $this->generateToken();
                    }
                    $headers = [
                      "Authorization" => "Bearer {$this->getToken()}"
                  ];
                }
                $this->curl->setHeaders($headers);
                $this->curl->post($this->getPath() . '/' . $path, $params);
                return [
                  'status' => $this->curl->getStatus(),
                  'body' => $this->curl->getBody()
              ];
            } else {
                return [
                    'status' => 500,
                    'body' => _('Server not configured')
                ];
            }
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }
    }

    /**
     *
     *
     * @param $data string
     * @return mixed
     */
    public function getRecourse($data)
    {
        try {
            $fileName = sprintf('%s%s.json', str_replace('/', '_', $data), date("YmdHis"));
            $jsonPath = $this->directoryList->getPath('var') . sprintf('/Aventi/%s', $fileName);
            $json = $this->getRecourseSelf($data);
            if ($json != false) {
                try {
                    $this->destinationDirectory->writeFile("Aventi/{$fileName}", $json);
                } catch (\Exception $e) {
                    $this->logger->error('writeFile::' . $e->getMessage());
                }
                if (file_exists($jsonPath) && filesize($jsonPath) > 10) {
                    return $jsonPath;
                } else {
                    return false;
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('getRecourse::' . $e->getMessage());
        }
    }
}
