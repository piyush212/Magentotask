<?php

namespace Codilar\AdvancedCartRules\Model\Rule\Condition;

use Magento\Config\Model\Config\Source\Yesno;
use Magento\Newsletter\Model\Subscriber;
use Magento\Rule\Model\Condition\Context;

class CustomerNewsletterSubscribedLifetime extends \Magento\Rule\Model\Condition\AbstractCondition
{
    const LABEL = 'Newsletter subscribed lifetime (in days)';
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
        $this->_inputType = 'numeric';
        $this->setAttributeOption([
            'customer_newsletter_subscribed_lifetime' => __(self::LABEL)
        ]);
        return $this;
    }

    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        /** @var \Magento\Quote\Model\Quote\Address $model */
        if ($model->getEmail()) {
            $subscriber = $this->subscriberModel->loadBySubscriberEmail($model->getEmail(), $model->getQuote()->getStore()->getWebsiteId());
            if ($subscriber->isSubscribed()) {
                try {
                    $changeStatusDate = new \DateTime($subscriber->getChangeStatusAt());
                    $dateNow = new \DateTime(gmdate("Y-m-d H:i:s"));
                    $daysPassed = $dateNow->diff($changeStatusDate)->format("%a");
                    $attributeValue = $daysPassed;
                    return $this->validateAttribute($attributeValue);
                } catch (\Exception $e) {
                    return false;
                }
            }
        }
        return false;
    }
}
