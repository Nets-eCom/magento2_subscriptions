<?php

namespace Dibs\EasyCheckout\Model\Dibs;

use Magento\Sales\Model\OrderFactory;
use Dibs\EasyCheckout\Model\Client\Api\Payment;
use Dibs\EasyCheckout\Model\Client\ClientException;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\OrderItem;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\CreatePaymentOrder;
use Dibs\EasyCheckout\Model\Client\DTO\CreateUnscheduledSubscriptionCharge;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\UnscheduledSubscriptionChargeOrder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Dibs\EasyCheckout\Helper\CronCreateOrder as DibsCrontHelper;

class SubscriptionCharge {

    /**
     * @var Items $items
     */
    protected $items;

    /**
     * @var \Dibs\EasyCheckout\Model\Client\Api\Payment $paymentApi
     */
    protected $paymentApi;

    /**
     * @var \Dibs\EasyCheckout\Helper\Data $helper
     */
    protected $helper;

    /** @var StoreManagerInterface */
    protected $storeManager;
    protected $subscriptionIntervalData;

    protected $subscriptionData;

    protected $dibsCrontHelper;

    /**
     * Order constructor.
     *
     * @param Payment $paymentApi
     * @param \Dibs\EasyCheckout\Helper\Data $helper
     * @param Items $itemsHandler
     * @param StoreManagerInterface $storeManager
     */

    /**
     * @param DibsCrontHelper $dibsCrontHelper
     */

    public function __construct(
            OrderFactory $orderCollectionFactory,
            \Dibs\EasyCheckout\Model\Client\Api\Payment $paymentApi,
            \Dibs\EasyCheckout\Helper\Data $helper,
            \Dibs\EasyCheckout\Model\NetsSubscriptionIntervalChargeFactory $subscriptionIntervalData,
            Items $itemsHandler,
            StoreManagerInterface $storeManager,
            \Dibs\EasyCheckout\Model\NetsSubscriptionDataFactory $subscriptionData,
            DibsCrontHelper $dibsCrontHelper
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->helper = $helper;
        $this->items = $itemsHandler;
        $this->paymentApi = $paymentApi;
        $this->subscriptionIntervalData = $subscriptionIntervalData;
        $this->storeManager = $storeManager;
        $this->subscriptionData = $subscriptionData;
        $this->_cronHelper = $dibsCrontHelper;
    }

    /**
     * @param $orderId
     * @throws \Exception
     */
    public function chargeUnscheduledSubscription($orderId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('Magento\Sales\Api\Data\OrderInterface')->load($orderId);
        $orders = $objectManager->create('\Magento\Sales\Model\OrderRepository')->get($orderId);
        $items = $this->items->fromOrder($orders);

        $totalAmount = $order->getBaseGrandTotal();
        if ($order->getNetsSubscriptionSignupFee() > 0) {
            $totalAmount = $order->getBaseGrandTotal() - $order->getNetsSubscriptionSignupFee();
        }
        $paymentOrder = new CreatePaymentOrder();
        $paymentOrder->setCurrency($order->getOrderCurrencyCode());
        $paymentOrder->setReference($order->getIncrementId()); // change increment id from sales_order table
        $paymentOrder->setAmount((int) round($totalAmount * 100, 0)); // amount total from sales_order
        $paymentOrder->setItems($items); //set items from sales_order

        $subscriptionChargeData = [];
        $subscriptionCharge = new UnscheduledSubscriptionChargeOrder();
        $subscriptionCharge->setUnscheduledSubscriptionId($order->getNetsSubscriptionId()); //set subscription id from sales_ordr
        $subscriptionCharge->setOrder($paymentOrder);
        $subscriptionChargeData[] = $subscriptionCharge;

        $createSubPaymentRequest = new CreateUnscheduledSubscriptionCharge();
        $createSubPaymentRequest->setExternalBulkChargeId(rand(1000, 9999));
        $createSubPaymentRequest->setUnscheduledSubscriptionIds($subscriptionChargeData);
        $chargeResponse = $this->paymentApi->chargeUnscheduledSubscription($createSubPaymentRequest);
        if (!empty($chargeResponse)) {
            $bulkId = $chargeResponse->getBulkId();
        }
        return $bulkId;
    }

    public function verfiyChargeUnscheduledSubscription($bulkId, $orderId, $postBulkID) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orders = $objectManager->create('\Magento\Sales\Model\OrderRepository')->get($orderId);

        $response = $this->paymentApi->getChargeUnscheduledSubscription($bulkId);

        foreach ($response->getUnscheduledSubscriptionItems() as $data) {
            $sub_id = $data['unscheduledSubscriptionId'];
            $payment_id = $data['paymentId'];
            $charge_id = $data['chargeId'];
            $status = $data['status'];

            if ($status == 'Succeeded' || $status == 'Pending') {
                
                $subscriptionQuery = $this->subscriptionData->create()->getCollection()->addFieldToSelect('*')->addFieldToFilter('nets_subscription_id', $sub_id);
                $rowResult = $subscriptionQuery->getData();
                
                $currentDate = date("Y-m-d");
                $updateCnt = $rowResult[0]['intervalCount'] + 1;
                $orderID = $rowResult[0]['order_entity_id'];
                $payID = $payment_id;

                //Update subscription charge interval count here
                $postSubscription = $this->subscriptionData->create()->getCollection()
                        ->addFieldToFilter('nets_subscription_id', $sub_id);

                $postSubscriptionUpdate = $postSubscription->load();
                foreach ($postSubscriptionUpdate as $itemUpdate) {
                    $itemUpdate->setIntervalCount($updateCnt);
                    $itemUpdate->setChargeDate($currentDate);
                    $itemUpdate->save();
                }

                //Create magento child order here post charge transaction
                $childOrderID = $this->_cronHelper->createMageOrder($orderID, $payID);
            }

            $subscriptionIntervalQuery = $this->subscriptionIntervalData->create()->getCollection()
                            ->addFieldToSelect('SubStatus')->addFieldToFilter('BulkId', $postBulkID);
            $result = $subscriptionIntervalQuery->getData();

            if ($result[0]['SubStatus'] == 'Failed') {

                $subscriptionIntervalModel = $this->subscriptionIntervalData->create();
                $subscriptionIntervalModel->addData([
                    "OrderID" => $orders->getIncrementId(),
                    "SubscriptinID" => $sub_id,
                    "PaymentID" => $payment_id,
                    "ChargeID" => $charge_id,
                    "SubStatus" => $status,
                    "BulkId" => $bulkId,
                    "ChildOrderId" => $childOrderID
                ]);
                $subscriptionIntervalModel->save();
            }
        }
    }

}
