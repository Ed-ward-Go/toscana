<?php


namespace Aventi\SAP\Controller\SendData;

use Magento\Framework\Filesystem\Io\File;

class Index extends \Magento\Framework\App\Action\Action
{

    const PATH_STORE = 'general/store_information/name';
    const PATH_URL = 'web/secure/base_url';
    const PATH_EMAIL = 'trans_email/ident_general/email';

    protected $resultPageFactory;

    /**
    *   @var \Aventi\SAP\Helper\DataEmail
    *
    */
    protected $helperMail;

    protected $jsonHelper;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    private $_file;

    protected $_transportBuilder;


    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Aventi\SAP\Helper\DataEmail $helperMail,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->_file = $file;
        $this->jsonHelper = $jsonHelper;
        $this->helperMail = $helperMail;
        parent::__construct($context);
        $this->logger = $logger;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
    	$data = '';
        $data = $this->getRequest()->getParams();
        $files = $this->getRequest()->getFiles();
        $sender = [
            'name' => 'No Reply S21',
            'email' =>  'no-reply@magento.com'
        ];
        $receiver = $this->helperMail->getCustomEmail();
        $transport = $this->_transportBuilder->setTemplateIdentifier('register_form') // put Email Template Name
              ->setTemplateOptions(['area' => 'frontend', 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
              ->setTemplateVars($data)
              ->setFrom($sender)
              ->addTo($receiver);           
        foreach ($files as $file) {
            $this->_transportBuilder->addAttachment($this->_file->read($file['tmp_name']), $file['name'], $file['type']);
        }
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();

        //return $this->jsonResponse($sender);
        
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
