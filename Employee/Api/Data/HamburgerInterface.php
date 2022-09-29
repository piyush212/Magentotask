<?php

namespace Piyush\Employee\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface HamburgerInterface extends ExtensibleDataInterface
{
    
    public function getId();
    
    public function getName();

    
    public function setName($Name);

    
    public function getEmail();

    
    public function setEmail($Email);

    
    public function getNumber();

    public function setNumber($Mobile);
}