<?php

namespace Dibs\EasyCheckout\Block\Subscription;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Page\Config;
use Dibs\EasyCheckout\Model\NetsSubscriptionDataFactory;

class SubscriptionInfo extends Template {

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    protected $subscriptionData;

    public function __construct(
            Context $context,
            Config $pageConfig,
            \Dibs\EasyCheckout\Model\Client\Api\Payment $paymentApi,
            \Dibs\EasyCheckout\Model\NetsSubscriptionDataFactory $subscriptionData,
            \Magento\Framework\Registry $registry,
            array $data = []
    ) {
        parent::__construct($context, $data);
        $this->pageConfig = $pageConfig;
        $this->paymentApi = $paymentApi;
        $this->subscriptionData = $subscriptionData;
        $this->_coreRegistry = $registry;
    }

    public function getNetsSubscriptionInfo() {
        $returnData = '';
        $order = $this->getOrder();
        if ($this->getOrder()->getPayment()->getMethod() == "dibseasycheckout") {
            $storeId = $this->getOrder()->getStoreId();
            $paymentId = $this->getOrder()->getDibsPaymentId();
            $paymentDetails = $this->paymentApi->getPayment($paymentId, $storeId);
            $data['masked_pan'] = $paymentDetails->getPaymentDetails()->getCardDetails()->getMaskedPan();
            $data['expiry_date'] = $paymentDetails->getPaymentDetails()->getCardDetails()->getExpiryDate();
            $data['payment_type'] = $paymentDetails->getPaymentDetails()->getPaymentType();
            $data['payment_method'] = $paymentDetails->getPaymentDetails()->getPaymentMethod();
            $data['nets_subscription_id'] = $order->getNetsSubscriptionId();
            $data['nets_subscription_enddate'] = $order->getNetsSubscriptionEnddate();
            $returnData = ($data);
        }
        return $returnData;
    }

    public function getSubscriptionCardStatus() {
        $order = $this->getOrder();
        $subID = $order->getNetsSubscriptionId();

        $subscriptionDataQuery = $this->subscriptionData->create()->getCollection()->addFieldToSelect('*')->addFieldToFilter('nets_subscription_id', $subID);
        $rowResult = $subscriptionDataQuery->getData();
        return $rowResult;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder() {
        return $this->_coreRegistry->registry('current_order');
    }

}

?>