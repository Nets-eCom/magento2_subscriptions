<?php

namespace Dibs\EasyCheckout\Ui\Component\Listing\Grid\Column;

class NetsOrderAmount extends \Magento\Ui\Component\Listing\Columns\Column {

    public function __construct(
            \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
            \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
            array $components = [],
            array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                $orderCurrency = !empty($items['nets_order_currency'])?$items['nets_order_currency']:'';
                $orderAmount = !empty($items['nets_order_amount'])?$items['nets_order_amount']:'';

                $items['nets_order_amount'] = $orderCurrency." ".$orderAmount;  
            }
        }
        return $dataSource;
    }

}


