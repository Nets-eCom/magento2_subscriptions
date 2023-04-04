<?php

namespace Dibs\EasyCheckout\Ui\Component\Listing\Grid\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class View extends \Magento\Ui\Component\Listing\Columns\Column {

    
     /** @var UrlInterface */
     protected $_urlBuilder;
 
    

      /**
     * @var string
     */
  

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory, 
        UrlInterface $urlBuilder,
        
        array $components = [],
        array $data = []
     
        
    ){
        $this->_urlBuilder = $urlBuilder;
      
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            
            foreach ($dataSource['data']['items'] as & $item) {

                $orderID= $item['order_entity_id'];
                $url = $this->_urlBuilder->getUrl('sales/order/view', ['order_id' =>$orderID ] );
                $item['id_subscription_data'] = "<a href=".$url." > View </a>";
;
            }

        }

        return $dataSource;
    }
}