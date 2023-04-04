<?php

namespace Dibs\EasyCheckout\Controller\Adminhtml\Subscription;

class Charge extends \Magento\Backend\App\Action {

    /** @var \Dibs\EasyCheckout\Model\Dibs\SubscriptionCharge $subscriptionHandler */
    protected $subscriptionHandler;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    protected $subscriptionData;
    protected $subscriptionIntervalData;

    public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
            \Dibs\EasyCheckout\Model\NetsSubscriptionDataFactory $subscriptionData,
            \Dibs\EasyCheckout\Model\NetsSubscriptionIntervalChargeFactory $subscriptionIntervalData,
            \Dibs\EasyCheckout\Model\Dibs\SubscriptionCharge $subscriptionHandler
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultJsonFactory;
        $this->subscriptionData = $subscriptionData;
        $this->subscriptionIntervalData = $subscriptionIntervalData;
        $this->subscriptionHandler = $subscriptionHandler;
    }

    public function execute() {

        $params = $this->getRequest()->getParams();

        $postBulkId = $params['bulk_id'];
        $subscriptionDataQuery = $this->subscriptionData->create()->getCollection()
                ->addFieldToSelect('order_entity_id')
                ->addFieldToFilter('nets_subscription_id', $params['subscription_id'])
                ->addFieldToFilter('status', '1');
        $result = $subscriptionDataQuery->getData();

        $bulkId = $this->subscriptionHandler->chargeUnscheduledSubscription($result[0]['order_entity_id']);
        if (!empty($bulkId)) {
            $this->subscriptionHandler->verfiyChargeUnscheduledSubscription($bulkId, $result[0]['order_entity_id'], $postBulkId);
            if (isset($postBulkId)) {
                $post = $this->subscriptionIntervalData->create()->getCollection()
                        ->addFieldToFilter('BulkId', $postBulkId);

                $postUpdate = $post->load();
                foreach ($postUpdate as $item) {
                    $item->setSubscriptionStatus('Manualy_Charged');
                    $item->save();
                }
            }
        }
        $resultJson = $this->resultFactory->create();
        return $resultJson->setData([
                    'status' => 'Success',
                    'error' => false
        ]);
    }

}
