<?php

namespace Dibs\EasyCheckout\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class SubscriptionFee extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $amount = $invoice->getOrder()->getNetsSubscriptionSignupFee();
        $invoice->setNetsSubscriptionSignupFee($amount);
        $invoice->setGrandTotal($invoice->getGrandTotal() + $amount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $amount);
        return $this;
    }
}