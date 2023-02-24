<?php

namespace Codilar\AdvancedCartRules\Model\Rule\Condition;

use Magento\Config\Model\Config\Source\Yesno;
use Magento\Newsletter\Model\Subscriber;
use Magento\Rule\Model\Condition\Context;

class CustomerNewsletterSubscribed extends \Magento\Rule\Model\Condition\AbstractCondition
{
    const LABEL = 'Customer newsletter subscribed';
    /**
     * @var Yesno
     */
    private $yesnoConfigSource;
    /**
     * @var Subscriber
     */
    private $subscriberModel;

    /**
     * CustomerNewsletterSubscribed constructor.
     * @param Context $context
     * @param Yesno $yesnoConfigSource
     * @param Subscriber $subscriberModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        Yesno $yesnoConfigSource,
        Subscriber $subscriberModel,
        array $data = []
    ) {
        $this->yesnoConfigSource = $yesnoConfigSource;
        parent::__construct($context, $data);
        $this->subscriberModel = $subscriberModel;
    }

    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            'customer_newsletter_subscribed' => __(self::LABEL)
        ]);
        return $this;
    }

    public function getInputType()
    {
        return 'select';
    }

    public function getValueElementType()
    {
        return 'select';
    }

    public function loadValueOptions()
    {
        $this->setValueOption($this->yesnoConfigSource->toArray());
        return $this;
    }

    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        /** @var \Magento\Quote\Model\Quote\Address $model */
        $subscriber = $this->subscriberModel->loadBySubscriberEmail($model->getEmail(), $model->getQuote()->getStore()->getWebsiteId());
        $attributeValue = $subscriber->isSubscribed() ? 1 : 0;
        return $this->validateAttribute($attributeValue);
    }
}
