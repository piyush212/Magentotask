<?php

namespace Codilar\SearchReport\Model\Resolver;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Codilar\SearchReport\Model\SearchRepository;

class SaveSearchDetails implements ResolverInterface

{
    /**
     * @var SearchRepository
     */

    private SearchRepository $searchRepository;

    /**
     * @param SearchRepository $searchRepository
     */

    public function __construct(
        SearchRepository $searchRepository
    ) {
        $this->searchRepository = $searchRepository;
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws AlreadyExistsException
     */

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $data = $this->searchRepository->create();

        $data->setQuery($args['query']);
        $data->setProduct($args['number_of_product']);

        $this->searchRepository->save($data);

        $save_message[] =
            [
                'status' => 'Data Save Successfully'
            ];

        return $save_message;
    }
}
