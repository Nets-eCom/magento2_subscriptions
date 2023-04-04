<?php

namespace Dibs\EasyCheckout\Block\Adminhtml\Order\View;

use Dibs\EasyCheckout\Model\Client\Api\Payment;
use Magento\Sales\Block\Adminhtml\Order\View as OrderView;
use Magento\Framework\UrlInterface;
use Dibs\EasyCheckout\Model\NetsSubscriptionIntervalChargeFactory;
use Dibs\EasyCheckout\Model\NetsSubscriptionDataFactory;

class View extends \Magento\Backend\Block\Template {

    protected $orderFactory;
    protected $_urlBuilder;

    protected $subscriptionIntervalData;
    
    protected $subscriptionData;
    /**
     * View constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Magento\Framework\Registry $registry,
            \Dibs\EasyCheckout\Helper\Data $dibsHelper,
            \Dibs\EasyCheckout\Model\NetsSubscriptionIntervalChargeFactory $subscriptionIntervalData,
            \Dibs\EasyCheckout\Model\NetsSubscriptionDataFactory $subscriptionData,
            Payment $payment,
            UrlInterface $url,
            array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->subscriptionIntervalData = $subscriptionIntervalData;
        $this->subscriptionData = $subscriptionData;
        $this->payment = $payment;
        $this->_dibsHelper = $dibsHelper;
        parent::__construct($context, $data);
        $this->_urlBuilder = $url;
    }

    public function beforeSetLayout(OrderView $view) {

        $params = $this->getRequest()->getParams();

        if (isset($params['redirect']) == '1') {
            $url = $this->_urlBuilder->getUrl('grid/grid/index');
        } else {
            $url = $this->_urlBuilder->getUrl('sales/order/*');
        }
        $view->updateButton('back', 'onclick', 'setLocation(\'' . $url . '\')');
    }

    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Dibs_EasyCheckout::row_data_delete');
    }

    public function getSubscriptionCardData() {
        $returnData = '';
        $order = $this->getOrder();
        if (!empty($order->getNetsSubscriptionId())) {
            $paymentDetails = $this->payment->getPayment($order->getDibsPaymentId(), $order->getStoreId());
            $data['masked_pan'] = $paymentDetails->getPaymentDetails()->getCardDetails()->getMaskedPan();
            $data['expiry_date'] = $paymentDetails->getPaymentDetails()->getCardDetails()->getExpiryDate();
            $data['payment_type'] = $paymentDetails->getPaymentDetails()->getPaymentType();
            $data['payment_method'] = $paymentDetails->getPaymentDetails()->getPaymentMethod();
            $data['nets_subscription_id'] = $order->getNetsSubscriptionId();
            $data['nets_subscription_enddate'] = $order->getNetsSubscriptionEnddate();
            $returnData = json_encode($data);
        }
        return $returnData;
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder() {
        return $this->coreRegistry->registry('current_order');
    }

    public function getSubscriptionInterval() {
        $order = $this->getOrder();
        $subID = $order->getNetsSubscriptionId();

        $subscriptionIntervalQuery = $this->subscriptionIntervalData->create()->getCollection()->addFieldToSelect('*')->addFieldToFilter('SubscriptinID', $subID);
        $result = $subscriptionIntervalQuery->getData();
        
        return $result;
    }

    public function getSubscriptionCardDetails() {
        $order = $this->getOrder();
        $subID = $order->getNetsSubscriptionId();
        
        $subscriptionDataQuery = $this->subscriptionData->create()->getCollection()->addFieldToSelect('*')->addFieldToFilter('nets_subscription_id', $subID);
        $rowResult = $subscriptionDataQuery->getData();
        return $rowResult;
    }

}
