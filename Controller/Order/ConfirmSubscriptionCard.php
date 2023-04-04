<?php

namespace Dibs\EasyCheckout\Controller\Order;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\Context;
use \Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class ConfirmSubscriptionCard extends AbstractAccount {

    /**
     * @var string
     */
    private $paymentId;

    /**
     * @var string
     */
    private $orderId;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    
    /** @var StoreManagerInterface */
    protected $storeManager;
    
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
    }
    
    public function execute() {
        $data = $this->getRequest()->getParams();
        $this->paymentId = $this->getRequest()->getParam('paymentid', false);
        $this->orderId = $this->getRequest()->getParam('orderid', false);
        if($this->paymentId && $this->orderId) {
            //$this->_redirect($this->storeManager->getStore()->getBaseUrl() . 'sales/order/view/order_id/' . $this->orderId);
            $this->_redirect($this->storeManager->getStore()->getBaseUrl() . 'easycheckout/customer/Index');
            $message = __('Payment details updated successfully'); 
            $this->messageManager->addSuccessMessage($message);
            
        }
        
    }
}
