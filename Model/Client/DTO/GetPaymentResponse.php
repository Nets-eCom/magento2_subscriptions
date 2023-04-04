<?php
namespace Dibs\EasyCheckout\Model\Client\DTO;

use Dibs\EasyCheckout\Model\Client\DTO\Payment\ConsumerPhoneNumber;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\GetConsumerCompany;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\GetConsumerCompanyContactDetails;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\GetConsumerPrivatePerson;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\GetConsumerShippingAddress;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\GetPaymentCardDetails;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\GetPaymentConsumer;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\GetPaymentDetails;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\GetChargeDetails;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\GetPaymentInvoiceDetails;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\GetPaymentOrder;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\GetPaymentSummary;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\OrderItem;

class GetPaymentResponse implements PaymentResponseInterface
{
    private $isCompany = false;

    /** @var GetPaymentOrder $orderDetails */
    protected $orderDetails;

    /** @var GetPaymentConsumer $consumer */
    protected $consumer;

    /** @var GetPaymentSummary $summary */
    protected $summary;

    /** @var GetPaymentDetails $paymentDetails */
    protected $paymentDetails;

    /** @var string $paymentId */
    protected $paymentId;

    /** @var $checkoutUrl string */
    protected $checkoutUrl;

    /** @var GetChargeDetails $chargeDetails */
    protected $chargeDetails;

    //protected $orderItem;

    /** @var $unscheduledSubscriptionId string */
    protected $unscheduledSubscriptionId;

    /**
     * If response is not empty, we fill the values from the API.
     *
     * GetPaymentResponse constructor.
     * @param $response string
     */
    public function __construct($response = "")
    {
        if ($response !== "") {
            $data = json_decode($response);
            $p = $data->payment;
            $orderDetails = new GetPaymentOrder();
            $summary = new GetPaymentSummary();
            $paymentDetails = new GetPaymentDetails();
            $consumer = new GetPaymentConsumer();
            //$orderItem = new OrderItem();

            if (!empty((array)$p->orderDetails)) {
                $orderDetails->setReference($this->_get($p->orderDetails, 'reference'));
                $orderDetails->setAmount($this->_get($p->orderDetails, 'amount'));
                $orderDetails->setCurrency($this->_get($p->orderDetails, 'currency'));
            }

            if (!empty((array)$p->summary)) {
                $summary->setReservedAmount($this->_get($p->summary, 'reservedAmount'));
                $summary->setChargedAmount($this->_get($p->summary, 'chargedAmount'));
            }

            if (!empty((array)$p->paymentDetails) && isset($p->paymentDetails->paymentMethod)) {
                $paymentDetails->setPaymentMethod($this->_get($p->paymentDetails, 'paymentMethod'));
                $paymentDetails->setPaymentType($this->_get($p->paymentDetails, 'paymentType'));

                // if card!
                if (!empty((array)$p->paymentDetails->cardDetails)) {
                    $cardDetails = new GetPaymentCardDetails();
                    $cardDetails->setExpiryDate($this->_get($p->paymentDetails->cardDetails, 'expiryDate'));
                    $cardDetails->setMaskedPan($this->_get($p->paymentDetails->cardDetails, 'maskedPan'));

                    $paymentDetails->setCardDetails($cardDetails);
                }

                // if invoice!
                if (!empty((array)$p->paymentDetails->invoiceDetails)) {
                    $invoiceDetails = new GetPaymentInvoiceDetails();
                    $invoiceDetails->setDueDate($this->_get($p->paymentDetails->invoiceDetails, 'dueDate'));
                    $invoiceDetails->setInvoiceNumber($this->_get($p->paymentDetails->invoiceDetails, 'invoiceNumber'));
                    $invoiceDetails->setOcr($this->_get($p->paymentDetails->invoiceDetails, 'ocr'));
                    $invoiceDetails->setPdfLink($this->_get($p->paymentDetails->invoiceDetails, 'pdfLink'));

                    $paymentDetails->setInvoiceDetails($invoiceDetails);
                }
            }

            if (!empty((array)$p->consumer)) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $cart = $objectManager->create('Magento\Checkout\Model\Cart');
                
                if (!empty((array)$p->consumer->shippingAddress)) {
                    $s = $p->consumer->shippingAddress;
                    $shippingAddress = new GetConsumerShippingAddress();

                    $shippingAddress->setPostalCode($this->_get($s, 'postalCode'));
                    $shippingAddress->setCountry($this->_get($s, 'country'));
                    $shippingAddress->setCity($this->_get($s, 'city'));
                    $shippingAddress->setReceiverLine($this->_get($s, 'receiverLine'));
                    $shippingAddress->setAddressLine1($this->_get($s, 'addressLine1'));
                    if (isset($s->addressLine2)) {
                        $shippingAddress->setAddressLine2($s->addressLine2);
                    }

                    $consumer->setShippingAddress($shippingAddress);
                } else {
                    $shippingAddressData = $cart->getQuote()->getShippingAddress();
                    $street             = $shippingAddressData->getData('street');
                    $city               = $shippingAddressData->getData('city');
                    $countryCode        = $shippingAddressData->getData('country_id');
                    $region             = $shippingAddressData->getData('region');
                    $postalCode         = $shippingAddressData->getData('postcode');
                    $telephone          = $shippingAddressData->getData('telephone');

                    $shippingAddress = new GetConsumerShippingAddress();
                    $shippingAddress->setPostalCode($postalCode);
                    $shippingAddress->setCountry($countryCode);
                    $shippingAddress->setCity($city);
                    $shippingAddress->setReceiverLine($telephone);
                    $shippingAddress->setAddressLine1($street);
                    
                    $consumer->setShippingAddress($shippingAddress);
                }

                if (!empty((array)$p->consumer->billingAddress)) {
                    $s = $p->consumer->billingAddress;
                    $billingAddress = new GetConsumerShippingAddress();

                    $billingAddress->setPostalCode($this->_get($s, 'postalCode'));
                    $billingAddress->setCountry($this->_get($s, 'country'));
                    $billingAddress->setCity($this->_get($s, 'city'));
                    $billingAddress->setReceiverLine($this->_get($s, 'receiverLine'));
                    $billingAddress->setAddressLine1($this->_get($s, 'addressLine1'));
                    if (isset($s->addressLine2)) {
                        $billingAddress->setAddressLine2($s->addressLine2);
                    }

                    $consumer->setBillingAddress($billingAddress);
                } else {
                    $billingAddressData = $cart->getQuote()->getBillingAddress();
                    $street             = $billingAddressData->getData('street');
                    $city               = $billingAddressData->getData('city');
                    $countryCode        = $billingAddressData->getData('country_id');
                    $region             = $billingAddressData->getData('region');
                    $postalCode         = $billingAddressData->getData('postcode');
                    $telephone          = $billingAddressData->getData('telephone');

                    $billingAddress = new GetConsumerShippingAddress();
                    $billingAddress->setPostalCode($postalCode);
                    $billingAddress->setCountry($countryCode);
                    $billingAddress->setCity($city);
                    $billingAddress->setReceiverLine($telephone);
                    $billingAddress->setAddressLine1($street);
                    
                    $consumer->setBillingAddress($billingAddress);
                }

                if (!empty((array)$p->consumer->privatePerson)) {
                    $priv = $p->consumer->privatePerson;
                    $pp = new GetConsumerPrivatePerson();

                    //
                    $phoneNumber = new ConsumerPhoneNumber();
                    $phoneNumber->setNumber($this->_get($priv->phoneNumber, 'number'));
                    $phoneNumber->setPrefix($this->_get($priv->phoneNumber, 'prefix'));

                    $pp->setLastName($this->_get($priv, 'lastName'));
                    $pp->setFirstName($this->_get($priv, 'firstName'));
                    $pp->setEmail($this->_get($priv, 'email'));
                    $pp->setPhoneNumber($phoneNumber);

                    $consumer->setPrivatePerson($pp);

                    $this->isCompany = false;
                }

                // consumer->company seems never to be empty, since it contains empty objects of contactDetails, so we check if company name is empty
                if (!empty($p->consumer->company->name)) {
                    $this->isCompany = true;
                    $org = $p->consumer->company;
                    $company = new GetConsumerCompany();
                    $contact = new GetConsumerCompanyContactDetails();

                    if (!empty((array)$org->contactDetails->phoneNumber)) {
                        $phone = new ConsumerPhoneNumber();
                        $phone->setNumber($this->_get($org->contactDetails->phoneNumber, 'number'));
                        $phone->setPrefix($this->_get($org->contactDetails->phoneNumber, 'prefix'));
                        $contact->setPhoneNumber($phone);
                    }

                    if (!empty((array)$org->contactDetails) && isset($org->contactDetails->firstName)) {
                        $contact->setFirstName($this->_get($org->contactDetails, 'firstName'));
                        $contact->setLastName($this->_get($org->contactDetails, 'lastName'));
                        $contact->setEmail($this->_get($org->contactDetails, 'email'));
                    }

                    // add data to company
                    $company->setContactDetails($contact);
                    $company->setName($this->_get($org, 'name'));
                    $company->setRegistrationNumber($this->_get($org, 'registrationNumber'));

                    // add company to consumer
                    $consumer->setCompany($company);
                }
            }

            $url = "";
            if (!empty((array)$p->checkout)) {
                $url = $this->_get($p->checkout, 'url');
            }

            if (isset($p->charges)) {
                $c = $data->payment->charges[0];
                $chargeDetails = new GetChargeDetails();
                $chargeDetails->setChargeId($this->_get($p->charges[0], 'chargeId'));
                $chargeDetails->setAmount($this->_get($p->charges[0], 'amount'));
            }

            if (isset($p->charges)) {
                $itemCount = 0;
                $orderItem = new OrderItem();

                $orderItems = $this->_get($p->charges[0], 'orderItems');
                $cartItems = [];
                $cartItems1 = [];
                $cartItems2 = [];
                foreach ($orderItems as $key => $item) {
                    $cartItems[$key][$item->reference] = $item->reference;
                    $cartItems[$key]['reference'] = $item->reference;
                    $cartItems[$key]['name'] = $item->name;
                    $cartItems[$key]['quantity'] = $item->quantity;
                    $cartItems[$key]['unit'] = $item->unit;
                    $cartItems[$key]['unitPrice'] = $item->unitPrice;
                    $cartItems[$key]['taxRate'] = $item->taxRate;
                    $cartItems[$key]['taxAmount'] = $item->taxAmount;
                    $cartItems[$key]['grossTotalAmount'] = $item->grossTotalAmount;
                    $cartItems[$key]['netTotalAmount'] = $item->netTotalAmount;

                    //print_r($item->reference);
                    //die;
                    /* $orderItem->setReference($this->_get($orderItems[$itemCount], 'reference'));
                      $orderItem->setName($this->_get($orderItems[$itemCount], 'name'));
                      $orderItem->setQuantity($this->_get($orderItems[$itemCount], 'quantity'));
                      $orderItem->setUnit($this->_get($orderItems[$itemCount], 'unit'));
                      $orderItem->setUnitPrice($this->_get($orderItems[$itemCount], 'unitPrice'));
                      $orderItem->setTaxRate($this->_get($orderItems[$itemCount], 'taxRate'));
                      $orderItem->setTaxAmount($this->_get($orderItems[$itemCount], 'taxAmount'));
                      $orderItem->setGrossTotalAmount($this->_get($orderItems[$itemCount], 'grossTotalAmount'));
                      $orderItem->setNetTotalAmount($this->_get($orderItems[$itemCount], 'netTotalAmount'));
                      $cartItems1[$itemCount] = $orderItem;

                      $itemCount = 1;
                      $orderItem->setReference($this->_get($orderItems[$itemCount], 'reference'));
                      $orderItem->setName($this->_get($orderItems[$itemCount], 'name'));
                      $orderItem->setQuantity($this->_get($orderItems[$itemCount], 'quantity'));
                      $orderItem->setUnit($this->_get($orderItems[$itemCount], 'unit'));
                      $orderItem->setUnitPrice($this->_get($orderItems[$itemCount], 'unitPrice'));
                      $orderItem->setTaxRate($this->_get($orderItems[$itemCount], 'taxRate'));
                      $orderItem->setTaxAmount($this->_get($orderItems[$itemCount], 'taxAmount'));
                      $orderItem->setGrossTotalAmount($this->_get($orderItems[$itemCount], 'grossTotalAmount'));
                      $orderItem->setNetTotalAmount($this->_get($orderItems[$itemCount], 'netTotalAmount')); */

                    //$cartItems2[$itemCount] = $orderItem;
                    //$itemCount++;
                    //break;
                    //print_r($orderItem);
                    //$this->setOrderItems($orderItem);
                }
                //echo 'here----------------------';
                //print_r(array_merge($cartItems1,$cartItems2));
                //print_r($cartItems);
                //die;
                //echo $orderItems = $this->_get($orderItems[0], 'reference'); //working to fetch
                //echo $this->_get($orderItems, 'reference');
            }

            //We set unscheduled Subscription if it is unscheduled Subscription payment
            if (isset($p->unscheduledSubscription) && !empty((array) $p->unscheduledSubscription->unscheduledSubscriptionId)) {
                $unscheduledSubscriptionId = $this->_get($p->unscheduledSubscription, 'unscheduledSubscriptionId');
            }

            // we set all data!
            $this->setPaymentId($p->paymentId);
            $this->setOrderDetails($orderDetails);
            $this->setSummary($summary);
            $this->setPaymentDetails($paymentDetails);
            $this->setConsumer($consumer);
            $this->setCheckoutUrl($url);
            if (isset($p->charges)) {
                $this->setChargeDetails($chargeDetails);
                //$this->setOrderItems($orderItem);
                $this->setOrderItems($cartItems);
            }

            //We set unscheduled Subscription if it is unscheduled Subscription payment
            if (isset($p->unscheduledSubscription) && !empty((array) $p->unscheduledSubscription->unscheduledSubscriptionId)) {
                $this->setUnscheduledSubscriptionId($unscheduledSubscriptionId);
            }
        }
    }

    /**
     * @return string
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * @param string $paymentId
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
    }

    /**
     * @return GetPaymentOrder
     */
    public function getOrderDetails()
    {
        return $this->orderDetails;
    }

    /**
     * @param GetPaymentOrder $orderDetails
     * @return GetPaymentResponse
     */
    public function setOrderDetails($orderDetails)
    {
        $this->orderDetails = $orderDetails;
        return $this;
    }

    /**
     * @return GetPaymentDetails
     */
    public function getPaymentDetails()
    {
        return $this->paymentDetails;
    }

    /**
     * @param GetPaymentDetails $paymentDetails
     * @return GetPaymentResponse
     */
    public function setPaymentDetails($paymentDetails)
    {
        $this->paymentDetails = $paymentDetails;
        return $this;
    }

    /**
     * @return GetPaymentSummary
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param GetPaymentSummary $summary
     * @return GetPaymentResponse
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return GetPaymentConsumer
     */
    public function getConsumer()
    {
        return $this->consumer;
    }

    /**
     * @param GetPaymentConsumer $consumer
     * @return GetPaymentResponse
     */
    public function setConsumer($consumer)
    {
        $this->consumer = $consumer;
        return $this;
    }

    /**
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->checkoutUrl;
    }

    /**
     * @param string $checkoutUrl
     * @return GetPaymentResponse
     */
    public function setCheckoutUrl($checkoutUrl)
    {
        $this->checkoutUrl = $checkoutUrl;
        return $this;
    }

    /**
     * @return GetChargeDetails
     */
    public function getChargeDetails()
    {
        return $this->chargeDetails;
    }

    /**
     * @param GetChargeDetails $chargeDetails
     * @return GetPaymentResponse
     */
    public function setChargeDetails($chargeDetails)
    {
        $this->chargeDetails = $chargeDetails;
        return $this;
    }

    public function getOrderItem() {
        return $this->orderItem;
    }

    public function setOrderItems($orderItem) {
        $this->orderItem = $orderItem;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnscheduledSubscriptionId() {
        return $this->unscheduledSubscriptionId;
    }

    /**
     * @param string $unscheduledSubscriptionId
     * @return GetPaymentResponse
     */
    public function setUnscheduledSubscriptionId($unscheduledSubscriptionId) {
        $this->unscheduledSubscriptionId = $unscheduledSubscriptionId;
        return $this;
    }

    public function getIsCompany()
    {
        return $this->isCompany;
    }

    protected function _get($obj, $key)
    {
        $arr = (array)$obj;
        if (isset($arr[$key])) {
            return $arr[$key];
        }

        return null;
    }
}
