<?php

/*
 * Symfony DataTables Bundle
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Omines\DataTablesBundle\Adapter;

use Omines\DataTablesBundle\Column\AbstractColumn;
use Omines\DataTablesBundle\DataTableState;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * AbstractAdapter.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
abstract class AbstractAdapter implements AdapterInterface
{
    protected readonly PropertyAccessor $accessor;

    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    final public function getData(DataTableState $state, bool $raw = false): ResultSetInterface
    {
        $query = new AdapterQuery($state);

        $this->prepareQuery($query);
        $propertyMap = $this->getPropertyMap($query);

        $transformer = $state->getDataTable()->getTransformer();
        $identifier = $query->getIdentifierPropertyPath();

        $data = (function () use ($query, $identifier, $transformer, $propertyMap, $raw) {
            foreach ($this->getResults($query) as $result) {
                $row = [];
                if (!empty($identifier)) {
                    $row['DT_RowId'] = $this->accessor->getValue($result, $identifier);
                }

                /** @var AbstractColumn $column */
                foreach ($propertyMap as list($column, $mapping)) {
                    $value = ($mapping && $this->accessor->isReadable($result, $mapping)) ? $this->accessor->getValue($result, $mapping) : null;
                    $row[$column->getName()] = $column->transform($value, $result, raw: $raw);
                }
                if (null !== $transformer) {
                    $row = call_user_func($transformer, $row, $result);
                }
                yield $row;
            }
        })();

        if (null === $query->getTotalRows() || null === $query->getFilteredRows()) {
            throw new \LogicException('Adapter did not set row counts');
        }

        return new ResultSet($data, $query->getTotalRows(), $query->getFilteredRows());
    }

    /**
     * @return array{AbstractColumn, ?string}[]
     */
    protected function getPropertyMap(AdapterQuery $query): array
    {
        $propertyMap = [];
        foreach ($query->getState()->getDataTable()->getColumns() as $column) {
            $propertyMap[] = [$column, $column->getPropertyPath() ?? (empty($column->getField()) ? null : $this->mapPropertyPath($query, $column))];
        }

        return $propertyMap;
    }

    abstract protected function prepareQuery(AdapterQuery $query): void;

    abstract protected function mapPropertyPath(AdapterQuery $query, AbstractColumn $column): ?string;

    /**
     * @return \Traversable<mixed[]>
     */
    abstract protected function getResults(AdapterQuery $query): \Traversable;
}
