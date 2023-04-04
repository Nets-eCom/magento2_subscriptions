<?php

namespace Dibs\EasyCheckout\Block\Subscription\History;

/**
 * Nets subscription order history extra container block
 *
 */
class Container extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Dibs\EasyCheckout\Api\Data\NetsSubscriptionDataInterface
     */
    private $subscription;

    /**
     * Set order
     *
     * @param \Dibs\EasyCheckout\Api\Data\NetsSubscriptionDataInterface $subscription
     * @return $this
     */
    public function setOrder(\Dibs\EasyCheckout\Api\Data\NetsSubscriptionDataInterface $subscription)
    {
        $this->subscription = $subscription;
        return $this;
    }

    /**
     * Get order
     *
     * @return \Dibs\EasyCheckout\Api\Data\NetsSubscriptionDataInterface
     */
    private function getOrder()
    {
        return $this->subscription;
    }

    /**
     * Here we set an order for children during retrieving their HTML
     *
     * @param string $alias
     * @param bool $useCache
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChildHtml($alias = '', $useCache = false)
    {
        $layout = $this->getLayout();
        if ($layout) {
            $name = $this->getNameInLayout();
            foreach ($layout->getChildBlocks($name) as $child) {
                $child->setOrder($this->getOrder());
            }
        }
        return parent::getChildHtml($alias, $useCache);
    }
}
