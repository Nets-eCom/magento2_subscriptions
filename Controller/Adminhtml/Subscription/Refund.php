<?php

namespace Dibs\EasyCheckout\Controller\Adminhtml\Subscription;

use Dibs\EasyCheckout\Model\Client\DTO\RefundPayment;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\OrderItem;
use Dibs\EasyCheckout\Model\Dibs\Items;

class Refund extends \Magento\Backend\App\Action {

    /** @var \Dibs\EasyCheckout\Model\Dibs\SubscriptionCharge $subscriptionHandler */
    protected $subscriptionHandler;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    protected $subscriptionIntervalData;

    public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
            \Dibs\EasyCheckout\Model\Dibs\SubscriptionCharge $subscriptionHandler,
            \Dibs\EasyCheckout\Model\Client\Api\Payment $payment,
            \Magento\Sales\Model\Spi\OrderResourceInterface $orderResource,
            \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory,
            \Dibs\EasyCheckout\Model\NetsSubscriptionIntervalChargeFactory $subscriptionIntervalData,
            Items $items
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultJsonFactory;
        $this->subscriptionHandler = $subscriptionHandler;
        $this->payment = $payment;
        $this->orderResource = $orderResource;
        $this->orderFactory = $orderFactory;
        $this->subscriptionIntervalData = $subscriptionIntervalData;
        $this->items = $items;
    }

    public function execute() {

        $params = $this->getRequest()->getParams();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $incrId = $params['id'];
        $collection = $objectManager->create('Magento\Sales\Model\Order');
        $orderInfo = $collection->loadByIncrementId($incrId);
        $storeId = $orderInfo->getStore()->getId();

        $this->payment->getPayment($params['payment_id'], $storeId);

        $chargeId = $result->getChargeDetails()->getChargeId();
        $refundPayment = new RefundPayment();
        $amountToRefund = $result->getChargeDetails()->getAmount();

        $refundPayment->setAmount($amountToRefund);
        if ("partial" == $params['type']) {
            $amount = 0;
            $orderData = array();
            $orderItem = new OrderItem();
            if (!empty($result->orderItem)) {
                foreach ($result->orderItem as $item) {
                    if ($item['reference'] == "shipping_fee" && $params['option'] == "shipping") {
                        $amount = $params['total'] * 100;
                        $orderItem
                                ->setReference($item['reference'])
                                ->setName($item['name'])
                                ->setUnit("shipping")
                                ->setQuantity(1)
                                ->setTaxRate(0) // the tax rate i.e 25% (2500)
                                ->setTaxAmount(0) // total tax amount
                                ->setUnitPrice($amount) // excl. tax price per item
                                ->setNetTotalAmount($amount) // excl. tax
                                ->setGrossTotalAmount($amount); // incl. tax
                        $orderData['orderItems'] = $orderItem;
                    } else if ($item['reference'] != "shipping_fee" && $params['option'] == "product") {
                        $grossPrice = $params['productTotal'] * 100;
                        $amount = $grossPrice;
                        $orderItem
                                ->setReference($item['reference'])
                                ->setName($item['name'])
                                ->setUnit("product")
                                //->setQuantity($params['qty'])
                                ->setQuantity(1)
                                ->setTaxRate(0) // the tax rate i.e 25% (2500)
                                ->setTaxAmount(0) // total tax amount
                                ->setUnitPrice($item['unitPrice']) // excl. tax price per item
                                ->setNetTotalAmount($grossPrice) // excl. tax
                                ->setGrossTotalAmount($grossPrice); // incl. tax
                        $orderData['orderItems'] = $orderItem;
                    }
                }
                $refundPayment->setItems($orderData);
                $amountToRefund = $amount;
                $refundPayment->setAmount($amountToRefund);
            }
        }

        $result = $this->payment->refundPayment($refundPayment, $result->getChargeDetails()->getChargeId());

        $status = 'Refunded';
        if (!empty($result->getRefundId())) {
            if ("partial" == $params['type']) {
                $status = 'Partially Refunded';
            }

            $post = $this->subscriptionIntervalData->create()->getCollection()
                    ->addFieldToFilter('ChargeID', $chargeId);

            $postUpdate = $post->load();
            foreach ($postUpdate as $item) {
                $item->setSubscriptionStatus($status);
                $item->save();
            }
            $resultJson = $this->resultFactory->create();
            return $resultJson->setData([
                        'status' => 'Success',
                        'id' => $result->getRefundId()
            ]);
        } else {
            $resultJson = $this->resultFactory->create();
            return $resultJson->setData([
                        'status' => 'Failed',
                        'message' => $result->getMessage()
            ]);
        }
    }

}
