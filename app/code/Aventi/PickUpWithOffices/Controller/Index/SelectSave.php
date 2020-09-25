<?php


namespace Aventi\PickUpWithOffices\Controller\Index;

class SelectSave extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $jsonHelper;
    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formKey;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;
    /**
     * @var \Aventi\PickUpWithOffices\Helper\Data
     */
    private $data;
    /**
     * @var \Aventi\PickUpWithOffices\Model\OfficeRepository
     */
    private $officeRepository;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Aventi\PickUpWithOffices\Helper\Data $data,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\Request\Http $request,
        \Aventi\PickUpWithOffices\Model\OfficeRepository $officeRepository

    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
        $this->formKey = $formKey;
        $this->request = $request;
        $this->request->setParam('form_key', $this->formKey->getFormKey());
        $this->data = $data;
        $this->officeRepository = $officeRepository;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        try {
            $officeId = (int)$this->getRequest()->getParam('office_id');
            $this->data->setValue($officeId);

            /*if($officeId > 0){

            }else{
                $this->data->unSetValue();
            }*/
            return $this->jsonResponse($officeId);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}