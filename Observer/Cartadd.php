<?php
namespace Dibs\EasyCheckout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Http\Context as customerSession;
use Dibs\EasyCheckout\Helper\Data as DibsDataHelper;


class Cartadd implements ObserverInterface {

    protected $cart;
    protected $messageManager;
    protected $redirect;
    protected $request;
    protected $product;
    protected $customerSession;
   

     /**
     * @var \Dibs\EasyCheckout\Helper\Data
     */
    protected $DibsDataHelper;

    public function __construct(
        RedirectInterface $redirect,
        Cart $cart, 
        ManagerInterface $messageManager,  
        RequestInterface $request, 
        Product $product, 
        customerSession $session,
        DibsDataHelper $dibsDataHelper
    ){
            $this->redirect = $redirect;
            $this->cart = $cart;
            $this->messageManager = $messageManager;
            $this->request = $request;
            $this->product = $product;
            $this->customerSession = $session;
            $this->helper = $dibsDataHelper;
            
    }

    public function execute(\Magento\Framework\Event\Observer $observer){
        
        $isEnableSub = $this->helper->isSubscriptionEnabled();
        $error1 = strip_tags($this->helper->beforAddtoCartError());
        $error2 = strip_tags($this->helper->afterAddtoCartError());
        // \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info('isSubscription:- ' . $isEnableSub);
        // strip_tags("Hello <b>world!</b>");
        $postValues = $this->request->getPostValue('product');
        $cartItemsCount = $this->cart->getQuote()->getItemsCount();
        $cartItemsAll =   $this->cart->getQuote()->getAllItems();

        $product_id =  $postValues;
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($product_id);

        
        if(($isEnableSub =='1' && $product->getData('enable_subscription')=='1') && $cartItemsCount >= '1' ){
            $observer->getRequest()->setParam('product', false);
            $this->messageManager->addNoticeMessage(__($error1));
            
            return $this;
        }
        $isSubscription =0;
        foreach ($cartItemsAll as $product) {
           $isSubscription = $product->getProduct()->getData('enable_subscription');
        }

       
        if($isEnableSub =='1' && $isSubscription =='1'){
            if ($cartItemsCount > 0) {
                $observer->getRequest()->setParam('product', false);
                $this->messageManager->addNoticeMessage(__($error2));
                return $this;
            }
        }
        
    }
    
}