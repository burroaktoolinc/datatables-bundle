<?php

/*
 * Symfony DataTables Bundle
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Omines\DataTablesBundle\Adapter\Doctrine\ORM;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Column\AbstractColumn;
use Omines\DataTablesBundle\Filter\AbstractFilter;
use Omines\DataTablesBundle\DataTableState;

/**
 * SearchCriteriaProvider.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
class SearchCriteriaProvider implements QueryBuilderProcessorInterface
{
    public function process(QueryBuilder $queryBuilder, DataTableState $state): void
    {
        $this->processSearchColumns($queryBuilder, $state);
        $this->processGlobalSearch($queryBuilder, $state);
    }

    private function processSearchColumns(QueryBuilder $queryBuilder, DataTableState $state): void
    {
        foreach ($state->getSearchColumns() as $searchInfo) {
            /** @var AbstractColumn $column */
            $column = $searchInfo['column'];
            $search = $searchInfo['search'];

            if ('' !== mb_trim($search)) {
                /* If we are using a filter we need to use the filter's left/right/operator */
                if (null !== ($filter = $column->getFilter())) {
                    if (!$filter->isValidValue($search)) {
                        continue;
                    }
                    $queryBuilder->andWhere($this->getFilterSearchComparison($filter, $column, $search));
                } else {
                    $queryBuilder->andWhere($this->getColumnSearchComparison($column, $search));
                }
            }
        }
    }

    private function processGlobalSearch(QueryBuilder $queryBuilder, DataTableState $state): void
    {
        if (!empty($globalSearch = $state->getGlobalSearch())) {
            $expr = $queryBuilder->expr();
            $comparisons = $expr->orX();
            foreach ($state->getDataTable()->getColumns() as $column) {
                if ($column->isGlobalSearchable() && !empty($column->getField()) && $column->isValidForSearch($globalSearch)) {
                    $comparisons->add($this->getColumnSearchComparison($column, $globalSearch));
                }
            }
            $queryBuilder->andWhere($comparisons);
        }
    }

    private function getColumnSearchComparison(AbstractColumn $column, string $search): Comparison
    {
        return new Comparison(
            $column->getLeftExpr($column->getField()),
            $column->getOperator(),
            (new Expr())->literal($column->getRightExpr($search)),
        );
    }

    private function getFilterSearchComparison(AbstractFilter $filter, AbstractColumn $column, string $search): Comparison
    {
        return new Comparison(
            $filter->getLeftExpr($column->getField()),
            $filter->getOperator(),
            (new Expr())->literal($filter->getRightExpr($search)),
        );
    }
}
