<?php
namespace Dibs\EasyCheckout\Block\Adminhtml\Sales;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Page\Config;

class Cardinfo extends \Magento\Framework\View\Element\Template
{

    public function __construct(
        Context $context,
        Config $pageConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->pageConfig = $pageConfig;
    }
    
    public function myFunction()
    {
        //your code
        return "Customers' Instruction";
    }
}