<?php
namespace Dibs\EasyCheckout\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Dibs\EasyCheckout\Model\Client\DTO\UpdatePaymentReference;
use \Dibs\EasyCheckout\Model\Client\Client;
use Magento\Sales\Model\Order;
use Dibs\EasyCheckout\Model\NetsSubscriptionDataFactory;
use Dibs\EasyCheckout\Helper\Email as DibsEmailHelper;

class OnepageSuccessObserver extends Client implements ObserverInterface {

    protected $subscriptionData;

    /**
     * @var \Dibs\EasyCheckout\Helper\Data
     */
    protected $helper;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @param DibsEmailtHelper $dibsEmailHelper
     */
    protected $DibsEmailHelper;

    /** @var \Dibs\EasyCheckout\Model\Checkout */
    protected $dibsOrderHandler;

    /** @var \Magento\Framework\Session\Config\ConfigInterface  */
    protected $sessionConfig;

    /** @var \Magento\Framework\Stdlib\CookieManagerInterface  */
    protected $cookieManager;

    /** @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory  */
    protected $cookieMetadataFactory;

    public function __construct(
            \Dibs\EasyCheckout\Helper\Data $helper,
            \Dibs\EasyCheckout\Model\Client\Api\Payment $paymentApi,
            //\Dibs\EasyCheckout\Model\Client\Client $clientApi,
            \Dibs\EasyCheckout\Model\Checkout $dibsOrderHandler,
            \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
            \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
            \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
            \Dibs\EasyCheckout\Model\NetsSubscriptionDataFactory $subscriptionData,
            DibsEmailHelper $dibsEmailHelper
    ) {
        $this->helper = $helper;
        $this->paymentApi = $paymentApi;
       // $this->clientApi  = $clientApi;
        $this->dibsOrderHandler = $dibsOrderHandler;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->cookieManager = $cookieManager;
        $this->sessionConfig = $sessionConfig;
        $this->subscriptionData = $subscriptionData;
        $this->_emailHelper = $dibsEmailHelper;
    }

    public function execute(EventObserver $observer)
    {

        $order = $observer->getEvent()->getOrder();
        $orderId = $order->getIncrementId();
        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();
        $methodTitle = $method->getTitle();
        if($payment->getMethod() == "dibseasycheckout"){
            $paymentId = $order->getDibsPaymentId();
	          $storeId = $order->getStoreId();
	          //Update Card Type in sales_order_payment table in addition_information column.
	          $paymentDetails = $this->paymentApi->getPayment($paymentId, $storeId);
            $order->getPayment()->setAdditionalInformation('dibs_payment_method', $paymentDetails->getPaymentDetails()->getPaymentMethod());
            $order->setNetsSubscriptionId($paymentDetails->getUnscheduledSubscriptionId());
            $order->save();

            //If payment is unscheduled Subscription, than data is inserted
            if (!empty($paymentDetails->getUnscheduledSubscriptionId())) {
                $subscribeModel = $this->subscriptionData->create();
                $subscribeModel->addData([
                    "order_entity_id" => $order->getId(),
                    "order_id" => $orderId,
                    "nets_payment_id" => $paymentId,
                    "nets_subscriber_name" => $order->getCustomerFirstname() . " " . $order->getCustomerLastname(),
                    "nets_subscription_id" => $paymentDetails->getUnscheduledSubscriptionId(),
                    "nets_subscription_interval" => $order->getNetsSubscriptionInterval(),
                    "nets_subscription_specific_interval" => $order->getNetsSubscriptionSpecificInterval(),
                    "nets_subscription_enddate" => $order->getNetsSubscriptionEnddate(),
                    "nets_order_amount" => $order->getGrandTotal(),
                    "nets_order_currency" => $order->getOrderCurrencyCode(),
                    "customer_id" => $order->getCustomerId(),
                    "status" => 1
                ]);
                $subscribeModel->save();

                $order->getPayment()->setAdditionalInformation('nets_subscription_id', $paymentDetails->getUnscheduledSubscriptionId());
                $order->getPayment()->setAdditionalInformation('nets_subscription_enddate', $order->getNetsSubscriptionEnddate());
                $order->save();

                // Check if new order email notification enabled
                $tempID = 'notification_subscription/general/';
                $isEnable = $this->_emailHelper->isEnabledNewOrder($tempID);

                if ($isEnable) {
                    $from = $this->_emailHelper->fromEmailOrder($tempID);
                    $to = $order->getCustomerEmail();
                    $templateId = $this->_emailHelper->templeteIdOrder($tempID);
                    $subject = $this->_emailHelper->subjectOrder($tempID);
                    $vars = [
                        'admin_subject' => $subject,
                        'cur_order_id' => $orderId,
                        'subscription_id' => $paymentDetails->getUnscheduledSubscriptionId(),
                        'expiry_date' => $order->getNetsSubscriptionEnddate(),
                        'order_amt' => $order->getOrderCurrencyCode() . " " . $order->getGrandTotal(),
                        'subscription_frequency' => $order->getNetsSubscriptionInterval(),
                    ];
                    $this->_emailHelper->sendEmail($from, $to, $templateId, $vars);
                }
            }


	          $reference = new UpdatePaymentReference();
            $reference->setReference($order->getIncrementId());
            $reference->setCheckoutUrl($this->helper->getCheckoutUrl());
            if ($this->helper->getCheckoutFlow() === "HostedPaymentPage") {
                $payment = $this->paymentApi->getPayment($paymentId, $storeId);
                $checkoutUrl = $payment->getCheckoutUrl();
                $reference->setCheckoutUrl($checkoutUrl);
            }
	          $this->paymentApi->UpdatePaymentReference($reference, $paymentId, $storeId);
        }
    }
}
