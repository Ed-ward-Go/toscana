<?php

namespace Aventi\SAP\Model\Sync;

use Symfony\Component\Console\Helper\ProgressBar;

class SendToSAP
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory
     */
    private $historyCollectionFactory;
    /**
     * @var \Aventi\SAP\Logger\Logger
     */
    private $logger;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    private $orderCollectionFactory;
    /**
     * @var \Aventi\SAP\Helper\Data
     */
    private $data;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    private $dataToSAP;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    private $regionFactory;
    /**
     * @var \Magento\SalesRule\Model\Coupon
     */
    private $coupon;
    /**
     * @var \Magento\SalesRule\Model\RuleRepository
     */
    private $ruleRepository;
    /**
     * @var \Aventi\SAP\Helper\SAP
     */
    private $sap;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Aventi\PickUpWithOffices\Api\OfficeRepositoryInterface
     */
    private $officeRepository;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output = false;

    /**
     * @var  \Aventi\SAP\Helper\DataEmail
     */
    private $dataEmail;

    protected $_orderFactory;

    private $orderManagement;
    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    private $sourceRepository;

    /**
     * SendToSAP constructor.
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory
     * @param \Aventi\SAP\Logger\Logger $logger
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Aventi\SAP\Helper\Data $data
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Aventi\SAP\Helper\Data $data,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\SalesRule\Model\Coupon $coupon,
        \Magento\SalesRule\Model\RuleRepository $ruleRepository,
        \Aventi\SAP\Helper\SAP $sap,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Aventi\PickUpWithOffices\Api\OfficeRepositoryInterface $officeRepository,
        \Aventi\SAP\Helper\DataEmail $dataEmail,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->logger = $logger;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->data = $data;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->regionFactory = $regionFactory;
        $this->coupon = $coupon;
        $this->ruleRepository = $ruleRepository;
        $this->sap = $sap;
        $this->dateTime = $dateTime;
        $this->resourceConnection = $resourceConnection;
        $this->productRepository = $productRepository;
        $this->officeRepository = $officeRepository;
        $this->dataEmail = $dataEmail;
        $this->_orderFactory = $orderFactory;
        $this->orderManagement = $orderManagement;
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Out in console
     */
    public function write($paramns)
    {
        $output = $this->getOutput();
        if ($output) {
            if (is_array($paramns)) {
                $output->writeln(print_r($paramns, true));
            } else {
                $output->writeln($paramns);
            }
        }
    }

    /**
     * Send the order to SAP
     *
     * @return array
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 29/11/18
     */
    public function completedOrderToSAP()
    {
        return $this->processOrder(['complete', 'syncing', 'pending', 'paid_tucompra']);
    }

    /**
     * @return array
     * @author Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 22/08/2019
     */
    public function errorOrderToSAP()
    {
        return $this->processOrder(['error_creacion']);
    }

    /**
     * Delete the registers of interations with SAP and return the number of interations
     *
     * @param string $status
     * @param int $orderId
     * @return int
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 26/11/18
     */
    public function getNumberInteration($status = 'pending', $orderId = 123456)
    {
        $iterations = 0;
        $historiesModel = $this->historyCollectionFactory->create();
        $historiesModel->addFieldToFilter('entity_name', 'order');
        $historiesModel->addFieldToFilter('status', $status);
        $historiesModel->addFieldToFilter('parent_id', $orderId);
        $historiesModel->addFieldToFilter('comment', ['like' => '%Sincronizando%']);
        $historiesModel->load();

        foreach ($historiesModel as $history) {
            $iterations = intval(preg_replace('/[^0-9]+/i', '', $history->getData('comment')));
            $history->delete();
        }
        $historiesModel->save();
        return ++$iterations;
    }

    /**
     * Validate if the error description
     *
     * @param string $description
     * @param int $orderId
     * @return int|void
     */
    public function validateError($description = 'pending', $orderId = 123456)
    {
        $iterations = 0;
        $historiesModel = $this->historyCollectionFactory->create();
        $historiesModel->addFieldToFilter('parent_id', $orderId);
        $historiesModel->addFieldToFilter('comment', ['like' => '%' . $description . '%']);
        $historiesModel->load();
        return count($historiesModel->getItems());
    }

    /**
     * Send the params to SAP
     *
     * @param array $product
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param int $OrderException
     * @param int $interation
     * @return mixed
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 26/11/18
     */
    public function createOrderSAP($product = [], \Magento\Sales\Api\Data\OrderInterface $order, $OrderException = 0, $interation = 0)
    {
        $idOrderSAP = $order->getData('sap_id');
        if (is_numeric($idOrderSAP) && $idOrderSAP > 0) {
            return [
                'status' => 200,
                'body' => '"err": "ErrorCode -> -1116, ErrorDesc -> (1) ' . $idOrderSAP . '"'
            ];
        }

        try {
            $cardCode = $this->data->getCardCode();
            /*if(empty($cardCode) || $cardCode == null){
                return [
                    'status' => 999,
                    'body' => '"err": "ErrorCode -> -1116, ErrorDesc -> (1)  El cardCode no esta configurado"'
                ];
            }*/

            $serie = $this->data->getSerie();
            /*if(empty($serie) || $serie == null){
                return [
                    'status' => 999,
                    'body' => '"err": "ErrorCode -> -1116, ErrorDesc -> (1) La serie  no esta definida"'
                ];
            }*/

            $regionName = $order->getBillingAddress()->getRegion();
            $region = $this->regionFactory->create();
            $region->loadByName($regionName, $order->getBillingAddress()->getCountryId());

            $idMagento = $order->getIncrementId();

            if ($region->getCode() != null) {
                $regionCodeBilling = str_pad($region->getCode(), 3, "0", STR_PAD_LEFT);
            } else {
                return [
                    'status' => 500,
                    'body' => '"err": "ErrorCode -> -1116, ErrorDesc -> (1) El departamento de facturación ' . $regionName . ' no esta definido en la orden ' . $idMagento . '"'
                ];
            }

            $numeroAutorizacion = '';
            $incrementId = $this->validateNumberOrderMagento($idMagento);
            if ($idMagento != $incrementId) {
                $idMagento = $incrementId;
                $order->setIncrementId($idMagento);
                $this->orderRepository->save($order);
            }
            $subTotal = $order->getSubtotal();
            $attributes = [];

            if ($order->getCustomerId() != null) {
                $customer = $this->customerRepository->getById($order->getCustomerId());
                foreach (['sap_customer_id', 'slp_code', 'owner_code', 'user_code'] as $attribute) {
                    $attributeObject = $customer->getCustomAttribute($attribute);
                    $attributes[$attribute] = $attributeObject ? $attributeObject->getValue() : null;
                }
            }

            $data = $order->getData();

            $fullName = $data['customer_firstname'] . ' ' . $data['customer_middlename'] . ' ' . $data['customer_lastname'];

            $shippingMethod = $order->getShippingMethod();

            $paymenTitle = $order->getPayment()->getMethodInstance()->getTitle();
            $PaymentCode = $order->getPayment()->getMethodInstance()->getCode();
            $Authorization = 1;

            if ((int)$data['credit_exceeded'] == 1) {
                $Authorization = 0;
            }
            if ($PaymentCode == 'banktransfer') {
                $Authorization = 2;
            }
            $source = $this->sourceRepository->get($order->getData('source_code'));
            $comments = 'eCommerce #%s pago:%s, Sucursal:%s ';
            $comments = sprintf($comments, $idMagento, $paymenTitle, $source->getName());

            $discount = 0;
            $baseDiscount = (($order->getBaseDiscountAmount()) < 0) ? $order->getBaseDiscountAmount() * -1 : 0;
            if ($baseDiscount > 0 && is_numeric($baseDiscount)) {
                $typeCoupon = $order->getCouponCode();
                $coupon = $this->coupon->loadByCode($typeCoupon);
                $rule = $this->ruleRepository->getById($coupon->getRuleId());
                switch ($rule->getSimpleAction()) {
                    case 'cart_fixed':
                        $discount = round(($baseDiscount / $order->getSubtotal()) * 100, 6);
                        break;
                    default:
                        break;
                }
            }
            $pickup = (($shippingMethod == "pickup_pickup") ? 'V' : 'P');

            if ($shippingMethod == "pickup_pickup") {
                $sapCode = $this->officeRepository->getById((int)$data['pick_up_id']);
                $officeSAP = trim($sapCode->getSap());
            }

            $seller = $source->getPostcode();
            $prefixIncrement = $this->data->getIncrement();
            $orderWeb = $prefixIncrement . $idMagento;
            $userFields = [
                "U_OS_COBRADOR~$seller",
                "U_GC_NUM_PEDIDO_WEB~$orderWeb",
                "U_FORMA_PAGO~01"
            ];
            $userFields = trim(implode("|", $userFields));
            $customerIdentification = trim($attributes['sap_customer_id']);

            $docDueDate = date('Y-m-d', strtotime($this->dateTime->date('Y-m-d') . ' + ' . $this->data->getDocDueDate() . ' days'));

            $shipToCode = $this->sap->getAddressSAP($order->getBillingAddress()->getCustomerAddressId());

            $this->dataToSAP = [
                'CardCode' => $customerIdentification,
                "DocDueDate" => $docDueDate,
                "Series" => $this->data->getSerie(),
                //"SlpCode" => $seller,
                "SlpCode" => $attributes['slp_code'],
                'CamposUsuario' => $userFields,
                'Detalles' => $product,
                'ShipToCode' => $shipToCode,
                "Comments" => $comments,
                "Descuento" => 0
            ];
            $this->write($this->dataToSAP);
            $response = $this->data->postRecourse('api/Documento/Create', $this->dataToSAP);
            return $response;
        } catch (\Exception $e) {
            return ['status' => 5001, 'body' => $e->getMessage()];
        }
    }

    /**
     * Validate the incremendId
     *
     * @param $str string of magento
     * @return bool|mixed
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 9/11/18
     */
    public function validateNumberOrderMagento($incrementID)
    {
        if (strlen($incrementID) > 10) {
            $incrementID = 'U' . substr($incrementID, 1, 9);
        }
        return $incrementID;
    }

    /**
     * @param $str string of sap
     * @return bool|mixed
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 9/11/18
     */
    public function validateNumberOrder($str)
    {
        $re = '/("DocNum": ?\d{1,10})/m';
        $numberOrder = false;
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        if (is_array($matches) && !empty($matches)) {
            $numberOrder = str_replace(['"DocNum":', ' '], '', $matches[0][0]);
        }

        return ($numberOrder == false) ? $str : $numberOrder;
    }

    /**
     * @param $str string of sap
     * @return bool|mixed
     * @author  Erich Hans Merz
     */
    public function validateDocEntry($str)
    {
        $re = '/("DocEntry": ?\d{1,10})/m';
        $numberOrder = false;
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        if (is_array($matches) && !empty($matches)) {
            $numberOrder = str_replace(['"DocEntry":', ' '], '', $matches[0][0]);
        }

        return ($numberOrder == false) ? $str : $numberOrder;
    }

    /**
     * @param $str string of sap
     * @return bool|mixed
     * @author  Erich Hans Merz
     */
    public function validateDocType($str)
    {
        $re = '/("DocType": ?\d{1,10})/m';
        $numberOrder = false;
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        if (is_array($matches) && !empty($matches)) {
            $numberOrder = str_replace(['"DocType":', ' '], '', $matches[0][0]);
        }

        return ($numberOrder == false) ? $str : $numberOrder;
    }

    /**
     * Get error description
     *
     * @param $str
     * @return mixed
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 22/02/19
     */
    public function getErrorDescription($str)
    {
        try {
            $description = str_replace(['"err":"', '"', "{", "}"], '', $str);
            return $description;
        } catch (\Exception $e) {
            return $str;
        }
    }
    /**
     * Get time process
     *
     * @return float
     */
    public function microtime_float()
    {
        list($useg, $seg) = explode(" ", microtime());
        return ((float)$useg + (float)$seg);
    }

    /**
     * Return all time execute
     *
     * @param $start
     * @return string
     */
    public function getTotalMicroTime($start)
    {
        $end = $this->microtime_float();
        $totalTime = ($end - $start) / 60;
        return number_format($totalTime, 2, ',', ' ') . ' Min ';
    }

    /**
     * Generate the structure of product for SAP
     *
     * @param $orderEntity
     * @return array
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 26/11/18
     */
    public function getStringProductForSAP($orderEntity)
    {
        $products = [];
        $items = $orderEntity->getAllItems();
        $totalItems = count($items);
        if ($orderEntity->getBaseDiscountAmount() < 0) {
            $totalDiscount = $orderEntity->getBaseDiscountAmount();
        } else {
            $totalDiscount = 0;
        }
        $source = $this->sourceRepository->get($orderEntity->getData('source_code'));
        foreach ($items as $item) {
            $product = $this->productRepository->getById($item->getProductId());

            $businessLine = $product->getCustomAttribute('business_line');
            if ($businessLine) {
                $businessLine = $businessLine->getValue();
            }

            if ($item->getPrice() > 0) {
                $tax = $this->getTax((int)$item->getTaxPercent());
                $products[] = [
                    'ItemCode' => $item->getSku(),
                    'Quantity' => intval($item->getQtyOrdered()),
                    'WhsCode' => $source->getSourceCode(),
                    'Price' => $item->getPrice(),
                    'DiscountPercent' => $totalDiscount,
                    'UoMEntry' => $this->data->getUomentry(),
                    'COGSCostingCode' => $source->getPhone(),
                    'COGSCostingCode2' => $this->data->getOcrCode2(),
                    'COGSCostingCode4' => $businessLine,
                    'CamposUsuario' =>  ""
                ];
            }
        }

        return $products;
    }

    public function processOrder($status = [])
    {
        $this->write($status);
        $start = $this->microtime_float();
        $orders = $this->orderCollectionFactory->create()
            ->addAttributeToFilter('status', $status)
            ->setOrder('created_at', 'ASC');
        $totalOrders = count($orders);
        $totalOrderSentSAP = $totalOrderError = 0;

        $this->write("Numero de ordenes  $totalOrders");
        $i = 0;
        foreach ($orders as $orderData) {
            try {
                $order = $this->orderRepository->get($orderData['entity_id']);
                $iteration = $this->getNumberInteration(['syncing', 'error_creacion'], $order->getId());
                if ($iteration == 10) {
                    $order->addStatusToHistory('syncing', 'Número de intentos máximos superados');
                    $this->orderRepository->save($order);
                    continue;
                } elseif ($iteration > 10) {
                    continue;
                }
                $products = $this->getStringProductForSAP($order);
                $order->addStatusToHistory('syncing', sprintf('Sincronizando pedido con SAP Server (%s intento)', $iteration));
                $this->orderRepository->save($order);
                $response = $this->createOrderSAP($products, $order, 0, $iteration);
                $this->write($response);

                switch ($response['status']) {
                    case 201:
                        $numberOrder = $this->validateNumberOrder($response['body']);
                        $docEntry = $this->validateDocEntry($response['body']);
                        $orderStatus = 'processing';
                        $orderState = 'processing';
                        $order->setState($orderState);
                        $order->addStatusToHistory(
                            $orderStatus,
                            sprintf('El pedido <strong>%s</strong> fué ingresado en SAP <strong>%s</strong>', $order->getIncrementId(), $numberOrder)
                        )->save();
                        $order->setData('sap_id', $numberOrder);
                        $order->setData('sap_doc_entry', $docEntry);
                        $this->updateSaleOrderGrid($order->getIncrementId(), $numberOrder);
                        $totalOrderSentSAP++;
                        break;
                    case 100:
                        if (strpos($response['body'], 'VENCIMIENTO') !== false) {
                            try {
                                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                                $model = $objectManager->create(\Aventi\SAP\Model\Order::class);
                                $model->setIncrementId($order->getIncrementId());
                                $model->save();
                            } catch (\Exception $e) {
                            }
                        }
                        $numberOrder = $this->validateNumberOrder($response['body']);
                        $docType = $this->validateDocType($response['body']);
                        if (is_numeric($numberOrder)) {
                            $order->setData('sap_id', $numberOrder);
                            $this->updateSaleOrderGrid($order->getIncrementId(), $numberOrder);
                            $this->updateSaleOrderGridType($order->getIncrementId(), $docType);
                            $order->addStatusToHistory(
                                'processing',
                                sprintf('El pedido <strong>%s</strong> fué ingresado en SAP <strong>%s</strong>', $order->getIncrementId(), $numberOrder)
                            )->save();
                            $totalOrderSentSAP++;
                            break;
                        } else {
                            $errorDescription = sprintf('<strong>Error de creación</strong><br>%s', $this->getErrorDescription($response['body']));
                            if ($this->validateError($errorDescription, $order->getId()) <= 0) {
                                $order->addStatusToHistory('error_creacion', $errorDescription)->save();
                            }

                            $totalOrderError++;
                            $this->logger->error(json_encode($this->dataToSAP));
                        }
                        // no break
                    default:

                        $errorDescription = sprintf('<strong>Error de creación</strong><br>%s', $this->getErrorDescription($response['body']));
                        if ($this->validateError($errorDescription, $order->getId()) <= 0) {
                            $order->addStatusToHistory('error_creacion', $errorDescription)->save();
                        }
                        $totalOrderError++;
                        $this->logger->error(json_encode($this->dataToSAP));
                        break;

                }
                $this->orderRepository->save($order);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                //throw new \Exception($e->getMessage(), 6);
            }
        }
        $totalTime = $this->getTotalMicroTime($start);
        $reports = [
            'title' => [_('Total orders'), _('Total error'), _('Total completed'), _('Time process')],
            'body' => [
                number_format($totalOrders, 0, ',', '.'),
                number_format($totalOrderError, 0, ',', '.'),
                number_format($totalOrderSentSAP, 0, ',', '.'),
                $totalTime,
            ]
        ];
        return $reports;
    }

    /**
     * Set the sap order id in the sale order grid
     *
     * @param $incrementId
     * @param $orderSapId
     */
    public function updateSaleOrderGrid($incrementId, $orderSapId)
    {
        $table = $this->resourceConnection->getConnection()->getTableName('sales_order_grid');
        $sql = 'UPDATE  __TABLE__ SET sap_id = "__ORDER__" WHERE increment_id  = "__ID__"';
        $search = ['__TABLE__', '__ID__', '__ORDER__'];
        $replace = [$table, $incrementId, $orderSapId];
        $sql = str_replace($search, $replace, $sql);
        $this->resourceConnection->getConnection()->query($sql);
    }

    /**
     * Set the sap type in the sale order grid
     *
     * @param $incrementId
     * @param $orderSapId
     */
    public function updateSaleOrderGridType($incrementId, $docType)
    {
        $table = $this->resourceConnection->getConnection()->getTableName('sales_order_grid');
        $sql = 'UPDATE  __TABLE__ SET sap_type = "__TYPE__" WHERE increment_id  = "__ID__"';
        $search = ['__TABLE__', '__ID__', '__TYPE__'];
        $replace = [$table, $incrementId, $docType];
        $sql = str_replace($search, $replace, $sql);
        $this->resourceConnection->getConnection()->query($sql);
    }

    public function orderSent()
    {
        $start = 0;
        $row = 1000;
        $date = date('Y-m-d', strtotime($this->dateTime->date('Y-m-d') . ' - 1 days'));
        $method = sprintf('api/Documento/Pedido/Estados/%s/%s/%s', $date, $start, $row);
        $response = $this->data->getRecourseSelf($method);
        $output = $this->getOutput();
        if ($response != false  && $response != null) {
            $data = json_decode($response, true);
            if ($data['total'] > 0) {
                if ($output) {
                    $progressBar = new ProgressBar($output, $data['total']);
                    $progressBar->start();
                }
                foreach ($data['data'] as   $order) {
                    $sapID = $order['DocNum'];
                    $status = $order['Estado'];
                    $orderCollection =  $this->orderCollectionFactory->create()
                        ->addFieldToFilter('sap_id', $sapID)
                        ->addFieldToFilter('sap_notification_send', 0)
                        ->addFieldToFilter('sap_type', 17);
                    foreach ($orderCollection as $orden) {
                        $shippingMethod = $orden->getShippingMethod();

                        if ($status == 'Entregado') {
                            $addressBilling = '';
                            $cityShipping = '';
                            $state = 'Listo para despachar';
                            $stateStatus = 'complete';
                            $response = 'ha sido despachado';

                            if ($shippingMethod == 'pickup_pickup') {
                                $state = 'Listo para recoger';
                                $response = 'está listo para recoger en el almacén ';
                                $stateStatus = 'pick_it_up';

                                if ($orden->getData('pick_up_id') > 0) {
                                    try {
                                        $office = $this->officeRepository->getById($orden->getData('pick_up_id'));
                                        $addressBilling = $office->getAddress();
                                        $cityShipping = $office->getCity();
                                        $response .= $office->getTitle();
                                    } catch (\Exception $e) {
                                        $addressBilling =  $orden->getBillingAddress()->getStreet()[0];
                                        $cityShipping = $orden->getShippingAddress()->getCity();
                                    }
                                } else {
                                    $addressBilling =  $orden->getBillingAddress()->getStreet()[0];
                                    $cityShipping = $orden->getShippingAddress()->getCity();
                                }
                            } else {
                                $addressBilling =  $orden->getBillingAddress()->getStreet()[0];
                                $cityShipping = $orden->getShippingAddress()->getCity();
                            }

                            $order= $this->orderRepository->get($orden->getId());
                            $order->setData('sap_notification_send', 1);
                            $order->setState('complete');
                            $order->setStatus($stateStatus);
                            $order->addStatusHistoryComment($state);
                            $this->orderRepository->save($order);

                            $response =  $this->dataEmail->sendOrderEmail(
                                $orden->getCustomerEmail(),
                                $orden->getCustomerFirstname() . ' ' . $orden->getCustomerLastname(),
                                $orden->getIncrementId(),
                                $response,
                                $addressBilling,
                                $cityShipping,
                                $orden
                            );
                        }
                        if ($output) {
                            $progressBar->advance();
                        }
                    }
                    if ($output) {
                        $progressBar->finish();
                    }
                    $progressBar = null;
                }
            }
        }
    }

    public function managerOrderSent()
    {
    }

    public function draftStatus()
    {
        $start = 0;
        $row = 1000;
        $date = date('Y-m-d', strtotime($this->dateTime->date('Y-m-d') . ' - 1 days'));
        $method = sprintf('api/Documento/Drafts/Estados/%s/%s/%s', $date, $start, $row);
        $response = $this->data->getRecourseSelf($method);
        $output = $this->getOutput();
        if ($response != false  && $response != null) {
            $data = json_decode($response, true);
            if ($data['total'] > 0) {
                if ($output) {
                    $progressBar = new ProgressBar($output, $data['total']);
                    $progressBar->start();
                }
                foreach ($data['data'] as   $order) {
                    $sapID = $order['DocNum'];
                    $docEntry = $order['DocEntry'];
                    $status = $order['WddStatus'];
                    $newSapID = $order['DocNum1'];
                    $newDocEntry = $order['DocEntry1'];
                    $orderCollection =  $this->orderCollectionFactory->create()
                        ->addAttributeToFilter('status', ['pending'])
                        ->addFieldToFilter('sap_id', $sapID)
                        ->addFieldToFilter('sap_doc_entry', $docEntry)
                        ->addFieldToFilter('sap_type', 112);
                    foreach ($orderCollection as $orden) {
                        if ($status == 'N' || $status == '-') {
                            $stateComment = '';
                            $order= $this->orderRepository->get($orden->getId());
                            if ($status == 'N') {
                                $stateComment = 'Draft no autorizado: <b>' . $sapID . '</b>';
                                $this->orderManagement->cancel($orden->getId());
                            } else {
                                if (!is_null($newSapID) && !is_null($newDocEntry)) {
                                    $stateComment = 'Draft: <b>' . $sapID . '</b> pasado a Pedido: <b>' . $newSapID . '<b>';
                                    $order->setStatus('processing');
                                    $order->setData('sap_id', $newSapID);
                                    $order->setData('sap_doc_entry', $newDocEntry);
                                    $order->setData('sap_type', 17);
                                    $this->updateSaleOrderGrid($order->getIncrementId(), $newSapID);
                                    $this->updateSaleOrderGridType($order->getIncrementId(), 17);
                                }
                            }

                            $order->addStatusToHistory($order->getStatus(), $stateComment);
                            $this->orderRepository->save($order);
                        }
                    }
                    if ($output) {
                        $progressBar->advance();
                    }
                }
                if ($output) {
                    $progressBar->finish();
                }
                $progressBar = null;
            }
        }
    }

    public function testSendMail($option = 0)
    {
        $order = $this->_orderFactory->create()->loadByIncrementId("000000076");

        switch ($option) {
            case 0:
                $this->dataEmail->sendOrderCancelEmail(
                    'emerz@aventi.com.co',
                    'Hans Merz',
                    "000000076",
                    'Credit Limit',
                    $order
                );
                break;
            case 1:
                $this->dataEmail->sendOrderEmail(
                    $order->getCustomerEmail(),
                    $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
                    $order->getIncrementId(),
                    'está listo para recoger en el almacén',
                    'direccion',
                    'QUITO',
                    $order
                );
                // no break
            default:

                break;
        }
    }

    public function getTax($percent)
    {
        $taxClass = 'IVA_EXE';
        if ($percent > 0 && $percent > 12) {
            $taxClass = sprintf('IVA_%s', intval($percent));
        } elseif ($percent == 12) {
            $taxClass = 'IVA';
        }

        return $taxClass;
    }

    public function validateListMaterial($products)
    {
        $isListMaterial = false;
        if (is_array($products)) {
            foreach ($products as $product) {
                if ($product['EsLM'] == 1) {
                    $isListMaterial = true;
                }
            }
        }

        return $isListMaterial;
    }
}
