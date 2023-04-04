<?php
namespace Dibs\EasyCheckout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Dibs\EasyCheckout\Model\Dibs\Items;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;

class CronCreateOrder extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_logger;
    protected $dibsCrontHelper;
  
    /**
     * @var Items $items
     */
    protected $items;

    protected $_orderRepository;

     /**
     * @var string
     */
    protected $paymentMethod;

    /** @var \Magento\Tax\Model\Calculation */
    protected $_calculationTool;

    protected $cartItemFactory;

    /**
     * Tax configuration model
     *
     * @var \Magento\Tax\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
    */
   //protected $orderItemFactory;

   protected $orderItemRepo;
  
   protected $itemFactory;

    protected $_paymentMethod = 'dibseasycheckout';
     /**
    * @param Magento\Framework\App\Helper\Context $context
    * @param Magento\Store\Model\StoreManagerInterface $storeManager
    * @param Magento\Catalog\Model\Product $product
    * @param Magento\Framework\Data\Form\FormKey $formKey $formkey,
    * @param Magento\Quote\Model\Quote $quote,
    * @param Magento\Customer\Model\CustomerFactory $customerFactory,
    * @param Magento\Sales\Model\Service\OrderService $orderService
    */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Data\Form\FormKey $formkey,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService,
        Items $itemsHandler,
        \Dibs\EasyCheckout\Model\Client\Api\Payment $paymentApi,
        \Magento\Sales\Model\Order $orderRepository,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Tax\Model\Calculation $taxCalculationTool,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemFactory,
       // \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \Magento\Sales\Model\Order\ItemFactory $itemFactory,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItem
        
    ) {
        $this->_storeManager = $storeManager;
        $this->_product = $product;
        $this->_formkey = $formkey;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
        $this->items = $itemsHandler;
        $this->_orderRepository = $orderRepository;
        $this->paymentApi = $paymentApi;
        $this->_config = $taxConfig;
        $this->_calculationTool = $taxCalculationTool;
        $this->cartItemFactory = $cartItemFactory;
        $this->itemFactory = $itemFactory;
        $this->orderItemRepo = $orderItem;
        parent::__construct($context);
        //$this->orderItemFactory = $orderItemFactory;
        
    }
 
    /**
     * Create Order On Your Store
     * 
     * @param array $orderData
     * @return array
     * 
    */
    public function createMageOrder($orderId,$dibsPaymentId) {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('Magento\Sales\Api\Data\OrderInterface')->load($orderId);
        $orders = $objectManager->create('\Magento\Sales\Model\OrderRepository')->get($orderId);
        $store = $orders->getStore();
        $storeId = $orders->getStoreId();
        $websiteid = $orders->getStore()->getWebsiteId();
        // $items = $this->items->fromOrder($orders);

        $taxAmount = $order->getTaxAmount();
        $vat = 0;
        $itemsRow = $order->getAllVisibleItems();
        foreach ($itemsRow as $itemEach) {
            $vat = $itemEach->getTaxPercent();
        }

        $feeTax = $this->_calculationTool->calcTaxAmount($order->getNetsSubscriptionSignupFee(), $vat, true);

        $subscriptionFee = $order->getNetsSubscriptionSignupFee();

        $additionalInformation = $order->getPayment()->getAdditionalInformation();
        $this->paymentMethod = $additionalInformation['dibs_payment_method'];
      
         $OrdershippingData=array();

         $shippingaddress = $orders->getShippingAddress();
         $OrdershippingData['firstname'] = $shippingaddress->getFirstname();
         $OrdershippingData['lastname'] =$shippingaddress->getLastname();
         $OrdershippingData['street'] = $shippingaddress->getStreet();
         $OrdershippingData['city'] = $shippingaddress->getCity();
         $OrdershippingData['country_id'] =$shippingaddress->getCountryId();
         $OrdershippingData['region'] = $shippingaddress->getRegion();
         $OrdershippingData['region_id'] =$shippingaddress->getRegionId();
         $OrdershippingData['region_code'] = $shippingaddress->getRegionCode();
         $OrdershippingData['postcode'] = $shippingaddress->getPostcode();
         $OrdershippingData['telephone'] =$shippingaddress->getTelephone();
         $OrdershippingData['fax'] = $shippingaddress->getFax();
         $OrdershippingData['save_in_address_book'] =1;
        
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteid);
        $customer->loadByEmail($shippingaddress->getEmail());// load customet by email address
        
        
        // if you have allready buyer id then you can load customer directly 
        $customerID = $order->getCustomerId();

        if(!$customer->getEntityId()){
            //If not avilable then create this customer 
            $customer->setWebsiteId($websiteid)
                    ->setStore($store)
                    ->setFirstname($shippingaddress->getFirstname())
                    ->setLastname($shippingaddress->getLastname())
                    ->setCustomerIsGuest(1)
                    ->setEmail($shippingaddress->getEmail());
                    // ->setPassword($orderData['email']);
            $customer->save();
        }

        $quote = $this->quote->create(); //Create object of quote
        $quote->setStore($store); //set store for which you create quote
        $customer= $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();
        $quote->setStoreId($storeId);

        // This will be saved in order
        $quote->setDibsPaymentId($dibsPaymentId);
        $quote->assignCustomer($customer); //Assign quote to customer

        //add items in quote
        foreach($order->getAllItems() as $item){
          $product = $this->_product->load($item->getProductId());
            $product->setPrice($item->getPrice());
            $quote->addProduct(
                $product,
                intval($item->getQtyOrdered())
            );

        }
      
        //Set Address to quote
        $quote->getBillingAddress()->addData($OrdershippingData);
        $quote->getShippingAddress()->addData($OrdershippingData);
 
        // Collect Rates and Set Shipping & Payment Method
        $shippingAddress=$quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates()
                        ->setShippingMethod($order->getShippingMethod()); //shipping method
       // $quote->setPaymentMethod('checkmo'); //payment method

       $quote->setPaymentMethod($this->_paymentMethod);
    //    $quote->setCustomerTaxClassId(3);
       $quote->setInventoryProcessed(false); //not effetc inventory
       $quote->save(); //Now Save quote and your quote is ready


        // Set Sales Order Payment
        $quote->getPayment()->importData(['method' => $this->_paymentMethod]);
        $paymentData = (new DataObject())
                ->setDibsPaymentId($dibsPaymentId)
                ->setCountryId($shippingaddress->getCountryId())
                ->setDibsPaymentMethod($this->paymentMethod);

        
        $quote->getPayment()->getMethodInstance()->assignData($paymentData);
        $quote->setDibsPaymentId($dibsPaymentId); //this is used by pushAction
        $quote->setCustomerTaxClassId(3);
  
        // Collect Totals & Save Quote
        $quote->setTotalsCollectedFlag(false); 
        $quote->collectTotals()->save();
 
        // Create Order From Quote
        $ChargeOrder = $this->quoteManagement->submit($quote);
     
        // $order->setEmailSent(0);
        $increment_id = $ChargeOrder->getRealOrderId();

        if($ChargeOrder->getEntityId()){
            
            if ($ChargeOrder->getState() === 'pending_payment') {

                $additionalInformation = $ChargeOrder->getPayment()->getAdditionalInformation();
                $additionalInformation['dibs_payment_status'] = "Charged";
                $status='processing';
                $comment = "Nets Easy charge completed for payment ID: " . $dibsPaymentId;

                $_order = $this->_orderRepository->load($ChargeOrder->getEntityId()); // it order is not order increment id
                $_order->setState('processing');
                $_order->setStatus('processing');
                $_order->getPayment()->setAdditionalInformation($additionalInformation);
                $_order->addCommentToStatusHistory($comment, $status);
                $_order->setTaxAmount($taxAmount);
                $_order->setBaseTaxAmount($taxAmount);
                $_order->setBaseGrandTotal($order->getBaseGrandTotal() - $subscriptionFee);
                $_order->setGrandTotal($order->getGrandTotal()- $subscriptionFee);
                $_order->setBaseSubtotalInclTax($order->getBaseSubtotalInclTax());
                $_order->setBaseTotalDue($order->getBaseTotalDue() - $subscriptionFee);
                $_order->setSubTotalInclTax($order->getSubTotalInclTax());
                $_order->setTotalDue($order->getTotalDue() - $subscriptionFee );
                $_order->save();
                
            }
                
            $result['order_id']= $ChargeOrder->getRealOrderId();
                /* Add Order Item Start */
            $childorderId = $ChargeOrder->getEntityId();
            $orderchild = $this->itemFactory->create()->getCollection()->addFieldToFilter('order_id', $childorderId);
          
            foreach ($orderchild as $itemsch) {
                $itemId =  $itemsch->getItemId();
                $orderchild = $this->orderItemRepo->get($itemId);
                $orderchild->setTaxPercent($vat);
                $orderchild->setTaxAmount($taxAmount);
                $orderchild->setBaseTaxAmount($taxAmount);
                // $orderchild->setBaseTaxAmount($taxAmount);
                $orderchild->save();
            }
            
        
        }
        return $result['order_id'];
    }

    
}
 