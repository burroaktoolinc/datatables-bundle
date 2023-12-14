<?php

/*
 * Symfony DataTables Bundle
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit;

use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Omines\DataTablesBundle\DependencyInjection\Instantiator;
use Omines\DataTablesBundle\Exporter\DataTableExporterManager;
use Omines\DataTablesBundle\Twig\TwigRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tests\Fixtures\AppBundle\DataTable\Type\PersonTableConfigureType;

class DataTableTypeTest extends TestCase
{
    public function testTypeFromFactory(): void
    {
        $factory = new DataTableFactory([], $this->createMock(TwigRenderer::class), new Instantiator(), $this->createMock(EventDispatcher::class), $this->createMock(DataTableExporterManager::class));

        $table = $factory->createFromType(PersonTableConfigureType::class);

        $this->assertInstanceOf(DataTable::class, $table);
        $this->assertSame('banana', $table->getColumnByName('banana')->getName());
        $this->assertSame(true, $table->getOption('fixedHeader'));
    }

    public function testTypeFromFactoryWithTypeOptions(): void
    {
        $factory = new DataTableFactory([], $this->createMock(TwigRenderer::class), new Instantiator(), $this->createMock(EventDispatcher::class), $this->createMock(DataTableExporterManager::class));

        $table = $factory->createFromType(PersonTableConfigureType::class, ['fruit' => 'orange']);

        $this->assertInstanceOf(DataTable::class, $table);
        $this->assertSame('orange', $table->getColumnByName('orange')->getName());
        $this->assertSame(true, $table->getOption('fixedHeader'));
    }

    public function testTypeFromFactoryWithTableOptions(): void
    {
        $factory = new DataTableFactory([], $this->createMock(TwigRenderer::class), new Instantiator(), $this->createMock(EventDispatcher::class), $this->createMock(DataTableExporterManager::class));

        $table = $factory->createFromType(PersonTableConfigureType::class, [], ['pageLength' => 100]);

        $this->assertInstanceOf(DataTable::class, $table);
        $this->assertSame(100, $table->getOption('pageLength'));
        $this->assertSame(true, $table->getOption('fixedHeader'));
    }
}
