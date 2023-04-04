<?php

namespace Dibs\EasyCheckout\Controller\Adminhtml\Charge;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Dibs\EasyCheckout\Model\ResourceModel\Grid\CollectionFactory;
use Dibs\EasyCheckout\Model\Client\Api\Payment;

class Index extends \Magento\Backend\App\Action {

    /**
     * Mass actions filter.
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /** @var \Dibs\EasyCheckout\Model\Dibs\SubscriptionCharge $subscriptionHandler */
    protected $subscriptionHandler;
    protected $subscriptionData;

    /**
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
            Context $context,
            Filter $filter,
            CollectionFactory $collectionFactory,
            \Dibs\EasyCheckout\Model\Dibs\SubscriptionCharge $subscriptionHandler,
            \Dibs\EasyCheckout\Model\NetsSubscriptionDataFactory $subscriptionData,
            \Dibs\EasyCheckout\Model\Client\Api\Payment $paymentApi
    ) {

        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
        $this->subscriptionHandler = $subscriptionHandler;
        $this->subscriptionData = $subscriptionData;
        $this->paymentApi = $paymentApi;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute() {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $recordCharge = 0;
        // echo "<pre>";print_r($collection->getData());die();
        if ($collection->getData() != '') {

            foreach ($collection->getData() as $record) {
                $subscriptionDataQuery = $this->subscriptionData->create()->getCollection()
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('id_subscription_data', $record['id_subscription_data'])
                        ->addFieldToFilter('status', '1');
                $result = $subscriptionDataQuery->getData();

                if (!empty($result)) {
                    $bulkId = $this->subscriptionHandler->chargeUnscheduledSubscription($result[0]['order_entity_id']);

                    if (!empty($bulkId)) {
                        $data = $this->subscriptionHandler->verfiyChargeUnscheduledSubscription($bulkId, $result[0]['order_entity_id']);
                    }
                }
                $recordCharge++;
            }
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been Charge.', $recordCharge));
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/grid/index');
    }

    /**
     * Check Category Map recode delete Permission.
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Dibs_EasyCheckout::row_data_delete');
    }

}
