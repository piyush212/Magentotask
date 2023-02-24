<?php

namespace Codilar\OrderReview\Model\Resolver;

use Magento\Framework\Exception\StateException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Magento\Customer\Model\ResourceModel\Address\CollectionFactory as CustomerAddressCollectionFactory;
use Magento\Quote\Api\PaymentMethodManagementInterface;

class OrderReviewResolver implements ResolverInterface
{

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var ShippingMethodManagementInterface
     */
    private $shippingMethodManagement;
    /**
     * @var CustomerAddressCollectionFactory
     */
    private CustomerAddressCollectionFactory $customerAddressCollectionFactory;
    /**
     * @var PaymentMethodManagementInterface
     */
    private PaymentMethodManagementInterface $paymentMethodManagement;

    /**
     * OrderReviewResolver constructor.
     * @param CartRepositoryInterface $cartRepository
     * @param ShippingMethodManagementInterface $shippingMethodManagement
     * @param CustomerAddressCollectionFactory $customerAddressCollectionFactory
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        ShippingMethodManagementInterface $shippingMethodManagement,
        CustomerAddressCollectionFactory $customerAddressCollectionFactory,
        PaymentMethodManagementInterface $paymentMethodManagement
    )
    {
        $this->cartRepository = $cartRepository;
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->customerAddressCollectionFactory = $customerAddressCollectionFactory;
        $this->paymentMethodManagement = $paymentMethodManagement;
    }


    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws NoSuchEntityException
     */
    public function resolve(
        Field       $field,
                    $context,
        ResolveInfo $info,
        array       $value = null,
        array       $args = null)
    {
        $cartId = $value['entity_id'] ?? null;
        if ($cartId === null) {
            throw new \UnexpectedValueException("Unable to retrieve cart, cart ID is null");
        }
        /** @var \Magento\Quote\Model\Quote $cart */
        $cart = $this->cartRepository->get($cartId);
        try {
            $payment = !empty($cart->getPayment()->getMethod()) ? $cart->getPayment()->getMethodInstance() : false;
        } catch(\Exception $exception) {
            $payment = false;
        }

        return [
            'shipping_method' => $this->getShippingMethodFromQuote($cart),
            'billing_address' => $this->getAddressInfo($cart->getBillingAddress()),
            'shipping_address' => $this->getAddressInfo($cart->getShippingAddress()),
            'payment_method' => $payment ? $this->preparePaymentMethod($payment) : null,
            'available_payment_methods' => $this->getAvailablePaymentMethods($cart)
        ];
    }

    /**
     * @param \Magento\Quote\Model\Quote $cart
     * @return array
     */
    protected function getAvailablePaymentMethods($cart)
    {
        $paymentMethods = $this->paymentMethodManagement->getList($cart->getId());
        $response = [];
        foreach ($paymentMethods as $paymentMethod) {
            $response[] = $this->preparePaymentMethod($paymentMethod);
        }
        return $response;
    }

    /**
     * @param \Magento\Quote\Api\Data\PaymentMethodInterface|\Magento\Payment\Model\MethodInterface $paymentMethod
     * @return array
     */
    protected function preparePaymentMethod($paymentMethod)
    {
        return [
            'code' => $paymentMethod->getCode(),
            'instructions' => method_exists($paymentMethod, 'getInstructions') ? $paymentMethod->getInstructions() : '',
            'title' => $paymentMethod->getTitle()
        ];
    }

    /**
     * @param \Magento\Quote\Model\Quote $cart
     * @return array
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function getShippingMethodFromQuote($cart)
    {
        $shippingAddress = $cart->getShippingAddress();
        if ($shippingAddress) {
            $shippingMethodCode = $shippingAddress->getShippingMethod();
            $shippingRate = $shippingAddress->getShippingRateByCode($shippingMethodCode);
            if ($shippingRate) {
                return [
                    'method_code' => $shippingRate->getMethod(),
                    'carrier_code' => $shippingRate->getCarrier(),
                    'code' => $shippingMethodCode,
                    'title' => $shippingRate->getCarrierTitle()
                ];
            }
        }
        return null;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return array
     */
    public function getAddressInfo($address)
    {
        if ($address) {
            $street = $address->getStreet();
            $streetSAddress0 = isset($street[0]) ? $street[0] : '';
            $streetSAddress1 = isset($street[1]) ? $street[1] : '';
            $streetSAddress2 = isset($street[2]) ? $street[2] : '';
            $addressInfo = [
                'id' => $this->getCustomerAddressId($address),
                'city' => $address->getCity(),
                'company' => $address->getCompany(),
                'country_id' => $address->getCountryId(),
                'firstname' => $address->getFirstname(),
                'lastname' => $address->getLastname(),
                'middlename' => $address->getMiddlename(),
                'prefix' => $address->getPrefix(),
                'region' => $address->getRegion(),
                'street' => $streetSAddress0 . ' ' . $streetSAddress1 . ' ' . $streetSAddress2,
                'telephone' => $address->getTelephone(),
                'postcode' => $address->getPostcode()
            ];
        } else {
            $addressInfo = [];
        }
        return $addressInfo;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return int|null
     */
    protected function getCustomerAddressId($address)
    {
        $customerId = $address->getCustomerId();
        $customerAddresses = $this->customerAddressCollectionFactory->create()->addFieldToFilter('parent_id', $customerId)->getItems();
        $fieldsToCheck = [
            'city',
            'company',
            'country_id',
            'firstname',
            'lastname',
            'middlename',
            'prefix',
            'region',
            'street',
            'telephone',
            'postcode'
        ];
        $finalAddress = null;
        /** @var \Magento\Customer\Model\Address $customerAddress */
        foreach ($customerAddresses as $customerAddress) {
            $finalAddress = $customerAddress;
            foreach ($fieldsToCheck as $field) {
                $addressData = $address->getData($field);
                $customerAddressData = $customerAddress->getData($field);
                if (is_array($addressData)) {
                    $addressData = implode('', $addressData);
                }
                if (is_array($customerAddressData)) {
                    $customerAddressData = implode('', $customerAddressData);
                }

                if ($addressData !== $customerAddressData) {
                    $finalAddress = null;
                    continue 2;
                }
            }
            if ($finalAddress) {
                break;
            }
        }

        if ($finalAddress) {
            return $finalAddress->getId();
        }
        return null;
    }
}
