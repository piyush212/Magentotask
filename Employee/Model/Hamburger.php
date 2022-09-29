<?php

namespace Piyush\Employee\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Piyush\Employee\Api\Data\HamburgerExtensionInterface;
use Piyush\Employee\Api\Data\HamburgerInterface;

class Hamburger extends AbstractExtensibleModel implements HamburgerInterface
{
    const Id = 'Id';
    const Name = 'Name';
    const Email = 'Email';
    const Number = 'Number';

    protected function _construct()
    {
        $this->_init(ResourceModel\Hamburger::class);
    }
    
    public function getId()
    {
        return $this->_getData('Id');
    }

    public function getName()
    {
        return $this->_getData('Name');
    }

    public function setName($Name)
    {
        $this->setData('Name', $Name);
    }

    public function getEmail()
    {
        return $this->_getData('Email');
    }

    public function setEmail($Email)
    {
        $this->setData('Email', $Email);
    }

    public function getNumber()
    {
        $this->_getData('Number');
    }

    public function setNumber($Number)
    {
        $this->setData('Number', $Number);
    }

}