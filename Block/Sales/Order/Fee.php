<?php

namespace Dibs\EasyCheckout\Block\Sales\Order;

class Fee extends \Magento\Framework\View\Element\Template {

    /**
     * Tax configuration model
     *
     * @var \Magento\Tax\Model\Config
     */
    protected $_config;

    /**
     * @var Order
     */
    protected $_order;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;

    /** @var \Magento\Tax\Model\Calculation */
    protected $_calculationTool;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Tax\Model\Calculation $taxCalculationTool
     * @param array $data
     */
    public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Tax\Model\Config $taxConfig,
            \Magento\Tax\Model\Calculation $taxCalculationTool,
            array $data = []
    ) {
        $this->_config = $taxConfig;
        $this->_calculationTool = $taxCalculationTool;
        parent::__construct($context, $data);
    }

    /**
     * Check if we nedd display full tax total info
     *
     * @return bool
     */
    public function displayFullSummary() {
        return true;
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource() {
        return $this->_source;
    }

    public function getStore() {
        return $this->_order->getStore();
    }

    /**
     * @return Order
     */
    public function getOrder() {
        return $this->_order;
    }

    /**
     * @return array
     */
    public function getLabelProperties() {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return array
     */
    public function getValueProperties() {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Initialize all order totals relates with tax
     *
     * @return \Magento\Tax\Block\Sales\Order\Tax
     */
    public function initTotals() {
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();
        $taxAmount = $this->_order->getTaxAmount();
        $store = $this->getStore();
        if ($this->_order->getNetsSubscriptionSignupFee() == 0 || $this->_order->getNetsSubscriptionSignupFee() == null) {
            return;
        }

        $vat = 0;
        $items = $this->_order->getAllVisibleItems();
        foreach ($items as $item) {
            $vat = $item->getTaxPercent();
        }
        $feeTax = $this->_calculationTool->calcTaxAmount($this->_order->getNetsSubscriptionSignupFee(), $vat, true);
        $this->_order->setTaxAmount($taxAmount + $feeTax);

        if ($this->_order->getNetsSubscriptionSignupFee() > 0) {
            $fee = new \Magento\Framework\DataObject(
                    [
                'code' => 'fee',
                'strong' => false,
                'value' => $this->_order->getNetsSubscriptionSignupFee(),
                'label' => __($this->_order->getNetsSubscriptionSignupName()),
                    ]
            );
            $parent->addTotal($fee, 'fee');
            return $this;
        }
    }

}
