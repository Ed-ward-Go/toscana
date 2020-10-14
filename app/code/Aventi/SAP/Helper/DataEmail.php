<?php


namespace Aventi\SAP\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Filesystem\Io\File;
use Zend_Mime;
use Zend\Mime\Part;

/**
 * Class Data
 *
 * @package Aventi\SAP\Helper
 */
class DataEmail extends AbstractHelper{



    const PATH_STORE = 'general/store_information/name';
    const PATH_URL = 'web/secure/base_url';
    const PATH_EMAIL = 'trans_email/ident_general/email';
    const PATH_CUSTOM_EMAIL_1 = 'trans_email/ident_custom1/email';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;
    /**
     * @var Data
     */
    private $data;
    /**
    *  @var File 
    */
    private $file;


    /**
     * DataEmail constructor.
     * @param Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param Data $data
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Aventi\SAP\Helper\Data $data,
        \Magento\Framework\Filesystem\Io\File $file
        )
    {
        parent::__construct($context);
        $this->logger = $logger;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->data = $data;
        $this->file = $file;
    }

    /**
     * @param null $store
     * @return string
     */
    public function getNameStore($store = null)
    {
        return (string)$this->scopeConfig->getValue(self::PATH_STORE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getUrlStore($store = null)
    {
        return (string)$this->scopeConfig->getValue(self::PATH_URL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getEmail($store = null)
    {
        return (string)$this->scopeConfig->getValue(self::PATH_EMAIL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCustomEmail($store = null)
    {
        return (string)$this->scopeConfig->getValue(self::PATH_CUSTOM_EMAIL_1, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }


    /**
     * @param $email
     * @param $name
     * @param $password
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendEmail($email,$name,$password){

        $sender = [
            'name' => $this->getNameStore(),
            'email' =>  $this->getEmail()
        ];
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('sap_register') // this code we have mentioned in the email_templates.xml
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars(
                [
                    'email' => $email,
                    'name' => $name,
                    'password' => $password
                ]
            )
            ->setFrom($sender);
        if($this->data->copyEmail() != ''){
            if (filter_var($this->data->copyEmail(), FILTER_VALIDATE_EMAIL)) {
                $transport->addCc($this->data->copyEmail());
                if($this->data->sendEmail() == 0){
                   $email = $this->data->copyEmail();
                }
            }
        }
        $transport = $transport->addTo($email)
                               ->getTransport();
        if($this->data->sendEmail() == 1){
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        }else if($this->data->copyEmail() != ''){
            $transport->sendMessage();
           $this->inlineTranslation->resume();
        }
    }
    /**
     * Send the email  order in coming
     *
     * @param $email
     * @param $name
     * @param $order
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendOrderEmail($email,$name,$orderId,$response,$address,$city,$order){

        $sender = [
            'name' => $this->getNameStore(),
            'email' =>  $this->getEmail()
        ];
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('order_sent') // this code we have mentioned in the email_templates.xml
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars(
                [
                    'email' => $email,
                    'name' => $name,
                    'orderId' => $orderId,
                    'response' => $response,
                    'address' => $address,
                    'city' => $city,
                    'order' => $order
                ]
            )
            ->setFrom($sender)
            ->addTo($email)
            ->getTransport();
         $transport->sendMessage();
         $this->inlineTranslation->resume();
    }

    /**
     * Send the email cancel order
     *
     * @param $email
     * @param $name
     * @param $order
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendOrderCancelEmail($email,$name,$orderId,$payment, $order){

        $sender = [
            'name' => $this->getNameStore(),
            'email' =>  $this->getEmail()
        ];
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('order_cancel') // this code we have mentioned in the email_templates.xml
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars(
                [
                    'email' => $email,
                    'name' => $name,
                    'orderId' => $orderId,
                    'payment' => $payment,                    
                    'order' => $order                  
                ]
            )
            ->setFrom($sender)
            ->addTo($email)
            ->getTransport();
         $transport->sendMessage();
         $this->inlineTranslation->resume();
    }

    public function sendEmailBase($data=[],$templateId,$email, $files = []){

        $sender = [
            'name' => $this->getNameStore(),
            'email' =>  $this->getEmail()
        ];
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $transport = $this->transportBuilder
            ->setTemplateIdentifier($templateId) // this code we have mentioned in the email_templates.xml
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars($data)
            ->setFrom($sender)
            ->addTo($email)
            ->getTransport();

        foreach ($files as $file) {
            $transport = $this->addAttachment($transport, $file);
        }

        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }

    /**
     * Add an attachment to the message inside the transport builder.
     *
     * @param TransportInterface $transportBuilder
     * @param array $file Sanitized index from $_FILES
     * @return TransportInterface
     */
    protected function addAttachment(TransportInterface $transport, $file = [])
    {
        $part = $this->createAttachment($file);
        $transport->getMessage()->getContent()->addPart($part);

        return $transport;
    }

    /**
     * Create an zend mime part that is an attachment to attach to the email.
     * 
     * This was my usecase, you'll need to edit this to your own needs.
     *
     * @param array $file Sanitized index from $_FILES
     * @return Part
     */
    protected function createAttachment($file = [])
    {
        $ext =  '.' . explode('/', $file['type'])[1];
        $fileName = md5(uniqid(microtime()), true) . $ext;

        $attachment = new Part($this->file->read($file['tmp_name']));
        $attachment->type = Zend_Mime::MULTIPART_MIXED;
        $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
        $attachment->encoding = Zend_Mime::ENCODING_BASE64;
        $attachment->filename = $file['name'];

        return $attachment;
    }

}