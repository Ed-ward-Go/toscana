<?php


namespace Aventi\PickUpWithOffices\Observer\Sales;

class OrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Aventi\PickUpWithOffices\Helper\Data
     */
    private $data;
    /**
     * @var \Aventi\PickUpWithOffices\Model\OfficeRepository
     */
    private $officeRepository;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;


    public function __construct(
        \Aventi\PickUpWithOffices\Helper\Data $data,
        \Aventi\PickUpWithOffices\Model\OfficeRepository $officeRepository,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->data = $data;
        $this->officeRepository = $officeRepository;
        $this->logger = $logger;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    )
    {
        try {

            $officeId = $this->data->getValue();
            if (is_numeric($officeId) && $officeId > 0) {
                $order = $observer->getEvent()->getOrder();
                $office = $this->officeRepository->getById($officeId);
                if ($office) {
                    $this->logger->info(get_class($order));
                    $data = sprintf('%s<br>%s<br>%s', $office->getTitle(), $office->getAddress(), $office->getCity());
                    $order->setData('pick_up', $data);
                    $order->setData('pick_up_id', $officeId);
                    $order->save();
                    $this->data->unSetValue();
                }
            }
        } catch (\Exception $e) {
            $this->logger->info('Oficina::' . $e->getMessage());
        }
    }
}
