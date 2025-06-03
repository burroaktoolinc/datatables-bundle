<?php

/*
 * Symfony DataTables Bundle
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Omines\DataTablesBundle;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * DataTableTypeInterface.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
interface DataTableTypeInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function configure(DataTable $dataTable, array $options): void;

    /**
     * Configures the options for this type.
     */
    public function configureOptions(OptionsResolver $resolver): void;

    /**
     * Configures the options for this table.
     */
    public function configureTableOptions(OptionsResolver $resolver): void;
}
