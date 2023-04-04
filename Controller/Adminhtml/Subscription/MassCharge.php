<?php
namespace Dibs\EasyCheckout\Controller\Adminhtml\Subscription;

class MassCharge extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        parent::__construct($context);
        $this->resultFactory = $resultJsonFactory;
    }
    public function execute(){
        
       \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info('Yuvraj SQL work:- ');
       
    }
}