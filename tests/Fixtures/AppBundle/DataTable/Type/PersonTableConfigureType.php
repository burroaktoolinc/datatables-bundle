<?php

/*
 * Symfony DataTables Bundle
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Fixtures\AppBundle\DataTable\Type;

use Omines\DataTablesBundle\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonTableConfigureType extends DataTableType
{
    public function configure(DataTable $dataTable, array $options): void
    {
        $fruit_column = $options['fruit'];
        $dataTable
            ->add('firstName', TextColumn::class)
            ->add($fruit_column, TextColumn::class)
            ->createAdapter(ArrayAdapter::class, [
                ['firstName' => 'Fred', $fruit_column => 'yellow'],
                ['firstName' => 'George', $fruit_column => 'green'],
                ['firstName' => 'Ringo', $fruit_column => 'white'],
                ['firstName' => 'Bill', $fruit_column => 'rotten'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'fruit' => 'banana',
        ]);
    }

    public function configureTableOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'fixedHeader' => true,
        ]);
    }
}
