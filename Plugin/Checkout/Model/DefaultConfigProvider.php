<?php
namespace Dibs\EasyCheckout\Plugin\Checkout\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use \Dibs\EasyCheckout\Helper\Data as EasyCheckoutHelper;

class DefaultConfigProvider
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * Constructor
     *
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        EasyCheckoutHelper $helper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->helper = $helper;
    }

    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        array $result
    ) {
        //$isSubscription = "";
        $subscription = $this->helper->isSubscriptionEnabled();
        $cartItemsAll = $this->checkoutSession->getQuote()->getAllItems();
        $itemCount = $this->checkoutSession->getQuote()->getItemsCount();
        if($itemCount == "1") {
            foreach ($cartItemsAll as $product) {
                $options = $product->getProduct()->getTypeInstance(true)->getOrderOptions($product->getProduct());
                $isSubscription = $product->getProduct()->getData('enable_subscription');
                $subscriptionInterval = $product->getProduct()->getData('nets_interval') . "-" . $product->getProduct()->getData('nets_sub_interval');
                $subscriptionIntervalTime = $product->getProduct()->getData('nets_sub_interval_time');
                $subscriptionEndDate = $this->helper->getSubscriptionEndDate($subscriptionInterval, $subscriptionIntervalTime);
            }
            if($isSubscription && $subscription) {
                $result['subscription']['interval'] = str_replace("-", " ", $subscriptionInterval);
                $result['subscription']['startDate'] = date("Y-m-d");
                $result['subscription']['endDate'] = $subscriptionEndDate;
            }
        }       
        return $result;
    }
}