<?php

namespace Codilar\SearchReport\Api;

use Codilar\SearchReport\Api\Data\SearchInterface;

interface SearchRepositoryInterface
{
    /**
     * @param SearchInterface $search
     * @return \Codilar\SearchList\Api\Data\SearchInterface
     */
    public function save(SearchInterface $search);

     public function create();

}
