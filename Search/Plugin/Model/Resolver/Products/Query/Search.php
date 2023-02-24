<?php
/**
 * @package     wingreens.
 * @author      codilar
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://www.codilar.com/
 */

namespace Codilar\Search\Plugin\Model\Resolver\Products\Query;

use Codilar\Search\Helper\SearchTerms as SearchTermsHelper;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use ScandiPWA\CatalogGraphQl\Model\Resolver\Products\Query\Search as Subject;

class Search
{
    /**
     * @var SearchTermsHelper
     */
    private SearchTermsHelper $searchTermsHelper;

    /**
     * @param SearchTermsHelper $searchTermsHelper
     */
    public function __construct(
        SearchTermsHelper $searchTermsHelper
    ) {
        $this->searchTermsHelper = $searchTermsHelper;
    }

    /**
     * @param Subject $subject
     * @param SearchCriteriaInterface $searchCriteria
     * @param ResolveInfo $info
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function beforeGetResult(
        Subject $subject,
        SearchCriteriaInterface $searchCriteria,
        ResolveInfo $info
    ) {
        $searchTerm = null;
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'search_term') {
                    $searchTerm = $filter->getValue();
                    break;
                }
            }
        }
        if ($searchTerm) {
            $this->searchTermsHelper->addSearchTerm($searchTerm);
        }

        return [$searchCriteria, $info];
    }
}
