<?php
namespace Dibs\EasyCheckout\Controller\Order;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\Context;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\CreatePaymentCheckout;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\CreatePaymentOrder;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\CreateUnscheduledSubscriptionOrder;
use Magento\Store\Model\StoreManagerInterface;
use Dibs\EasyCheckout\Model\Dibs\Items;
use Dibs\EasyCheckout\Model\Client\DTO\CreatePayment;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\OrderItem;

// use Magento\Framework\Controller\ResultFactory;

class UpdateSubscriptionCard extends AbstractAccount
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    protected $_orderRepository;
    
    /**
     * @var \Dibs\EasyCheckout\Helper\Data $helper
     */
    protected $helper;

    /** @var StoreManagerInterface */
    protected $storeManager;
    
    /**
     * @var \Dibs\EasyCheckout\Model\Client\Api\Payment $paymentApi
     */
    protected $paymentApi;
    
    /**
     * @var Items $items
     */
    protected $items;
  
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    
    public function __construct(
        Context $context,
        \Magento\Sales\Model\Order $orderRepository,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Dibs\EasyCheckout\Helper\Data $helper,
        StoreManagerInterface $storeManager,
        Items $itemsHandler,
        \Dibs\EasyCheckout\Model\Client\Api\Payment $paymentApi,
        \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultJsonFactory; 
        $this->_orderRepository = $orderRepository; 
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->items = $itemsHandler;
        $this->paymentApi = $paymentApi;
        $this->_customerSession = $customerSession;
    }
    
    public function execute()
    {
        if($this->_customerSession->getCustomerId()) {
            $data = $this->getRequest()->getParams();
            $_order = $this->_orderRepository->load($data['id']);
            
            if($_order && $data['subId'] === $_order->getNetsSubscriptionId()) {
                $dibsPayment = $this->createUpdateCardPayment($_order);
                $this->_redirect($dibsPayment->getCheckoutUrl());
            } else {
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('customer/account');
            }
        } else {
            return false;
        }
    }
    
    protected function createUpdateCardPayment($order) {
        $paymentCheckout = new CreatePaymentCheckout();
        
        $unscheduledSubscriptionId = $order->getNetsSubscriptionId();
        $webhooks = [];
        
        $termsUrl = $this->helper->getTermsUrl();
        if (strlen($termsUrl) > 128) {
            $termsUrl = substr($termsUrl, 0, 128);
        }
        
        $privacyUrl = $this->helper->getPrivacyUrl();
        if (strlen($privacyUrl) > 128) {
            $privacyUrl = substr($privacyUrl, 0, 128);
        }
        
        $items[] = $this->generateItems($unscheduledSubscriptionId);
        
        $paymentCheckout->setTermsUrl($termsUrl);
        $paymentCheckout->setPrivacyUrl($privacyUrl);
        $paymentCheckout->setIntegrationType("HostedPaymentPage");
        $paymentCheckout->setMerchantHandlesConsumerData(true);
        $paymentCheckout->setReturnUrl($this->storeManager->getStore()->getBaseUrl() . 'easycheckout/Order/ConfirmSubscriptionCard?orderid=' . $order->getId());
        
        // we generate the order here, amount and items
        $paymentOrder = new CreatePaymentOrder();
        $paymentOrder->setCurrency($order->getOrderCurrencyCode());
        $paymentOrder->setReference($this->generateReferenceBySubscriptionId($unscheduledSubscriptionId));
        $paymentOrder->setAmount(0);
        $paymentOrder->setItems($items);
        
        //subscription data
        $unscheduledSubscriptionOrder = new CreateUnscheduledSubscriptionOrder();
        $unscheduledSubscriptionOrder->setUnscheduledSubscriptionId($unscheduledSubscriptionId);
        
        // create payment object
        $createPaymentRequest = new CreatePayment();
        $createPaymentRequest->setCheckout($paymentCheckout);
        $createPaymentRequest->setOrder($paymentOrder);
        $createPaymentRequest->setWebHooks($webhooks);
        $createPaymentRequest->setUnscheduledSubscription($unscheduledSubscriptionOrder);
        
        return $this->paymentApi->createNewPayment($createPaymentRequest);
    }
    
    public function generateItems($unscheduledSubscriptionId) {
        $orderItem = new OrderItem();
        $orderItem
                ->setReference($this->generateReferenceBySubscriptionId($unscheduledSubscriptionId))
                ->setName('Update Card')
                ->setUnit("pcs")
                ->setQuantity(1)
                ->setTaxRate(0)
                ->setTaxAmount(0)
                ->setUnitPrice(0)
                ->setNetTotalAmount(0)
                ->setGrossTotalAmount(0);
        return $orderItem;
    }
    
    public function generateReferenceBySubscriptionId($unscheduledSubscriptionId) {
        return substr($unscheduledSubscriptionId, 10, 26) . "_" . strtotime("now");
    }
}


?>