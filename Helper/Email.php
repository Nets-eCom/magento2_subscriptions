<?php
namespace Dibs\EasyCheckout\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;


class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $inlineTranslation;
    protected $escaper;
    protected $transportBuilder;
    protected $logger;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    /**
     * Neworder form data
     */
    protected $_data;

    /**
     * Dibs System Settings, subscription new order Email Enable
     */
    const XML_PATH_SUBSCRIPTION_ORDER  = 'notification_subscription/general/';

    /**
     * Initialize
     *
     * @param Magento\Framework\App\Helper\Context $context
     * @param Magento\Catalog\Model\ProductFactory $productFactory
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */

    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder
        
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
    }

    /**
     * @param null $store
     * @return bool 
     * * @param string $controller
     * @return string
     *
     * 
     */
    public function isEnabledNewOrder($tempID,$store = null)
    {
        return $this->scopeConfig->isSetFlag(
            $tempID.'enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function fromEmailOrder($tempID,$store = null)
    {
        return $this->scopeConfig->getValue(
            $tempID.'send_from',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    // public function toEmailOrder($tempID,$store = null)
    // {
    //     return $this->scopeConfig->getValue(
    //         $tempID.'email',
    //         \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
    //         $store
    //     );
    // }

    public function templeteIdOrder($tempID,$store = null)
    {
        return $this->scopeConfig->getValue(
            $tempID.'template',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function subjectOrder($tempID,$store = null)
    {
        return $this->scopeConfig->getValue(
            $tempID.'subject',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }


    public function sendEmail($from, $to, $templateId, $vars)
    {
        try {
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->escaper->escapeHtml('Subscription New Order'),
                'email' => $this->escaper->escapeHtml($from),
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars($vars)
                ->setFrom($sender)
                ->addTo($to)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }


    /**
     * Fetch System Config Value
     */
    public function getConfigVal($str = '') {
        return $this->scopeConfig->getValue($str, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}