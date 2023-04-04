<?php
namespace Dibs\EasyCheckout\Block;


class NetsSubscriptionSummary extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Dibs\EasyCheckout\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Dibs\EasyCheckout\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Cart $cart, 
        \Dibs\EasyCheckout\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->_cart = $cart;
        parent::__construct($context, $data);
    }

    public function getSubscriptionData()
    {
        $responseData = [];
        $subscription = $this->helper->isSubscriptionEnabled();
        $cartItemsAll = $this->_cart->getQuote()->getAllItems();
        $itemCount = $this->_cart->getQuote()->getItemsCount();
        if($itemCount == "1") {
            foreach ($cartItemsAll as $product) {
                $options = $product->getProduct()->getTypeInstance(true)->getOrderOptions($product->getProduct());
                $isSubscription = $product->getProduct()->getData('enable_subscription');
                $subscriptionInterval = $product->getProduct()->getData('nets_interval') . "-" . $product->getProduct()->getData('nets_sub_interval');
                $subscriptionIntervalTime = $product->getProduct()->getData('nets_sub_interval_time');
                $subscriptionEndDate = $this->helper->getSubscriptionEndDate($subscriptionInterval, $subscriptionIntervalTime);
            }
            if($isSubscription && $subscription) {
                $responseData['interval'] = str_replace("-", " ", $subscriptionInterval);
                $responseData['startDate'] = date("Y-m-d");
                $responseData['endDate'] = $subscriptionEndDate;
            }
        }
        
        return $responseData;
    }
        
}