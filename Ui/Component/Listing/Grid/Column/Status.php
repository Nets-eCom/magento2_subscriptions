<?php

namespace Dibs\EasyCheckout\Ui\Component\Listing\Grid\Column;

class Status extends \Magento\Ui\Component\Listing\Columns\Column {

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
                // $items['instock'] is column name
                if ($items['status'] == 1) {
                    $items['status'] = 'Active';
                }else if ($items['status'] == 2){
                   $items['status'] = 'Closed';
                }else {
                    $items['status'] = 'InActive';
                }
            }
        }
        return $dataSource;
    }

}
