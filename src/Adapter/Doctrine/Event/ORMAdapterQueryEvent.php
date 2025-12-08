<?php

/*
 * Symfony DataTables Bundle
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Omines\DataTablesBundle\Adapter\Doctrine\Event;

use Doctrine\ORM\Query;
use Symfony\Contracts\EventDispatcher\Event;

class ORMAdapterQueryEvent extends Event
{
    /** @var Query<mixed> */
    protected Query $query;

    /** @param Query<mixed> $query */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /** @return Query<mixed> */
    public function getQuery(): Query
    {
        return $this->query;
    }
}
