<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionDescription\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @throws \Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        $this->addOptionShortDescriptionTable($connection);

        $setup->endSetup();
    }

    /**
     * @throws \Exception
     */
    protected function addOptionShortDescriptionTable(AdapterInterface $connection): void
    {
        $optionShortDescriptionTableName = $connection->getTableName('catalog_product_option_short_desc');

        if (! $connection->isTableExists($optionShortDescriptionTableName)) {
            $optionTableName = $connection->getTableName('catalog_product_option');
            $storeTableName = $connection->getTableName('store');

            $optionShortDescriptionTable = $connection->newTable($optionShortDescriptionTableName);

            $optionShortDescriptionTable->addColumn(
                'id',
                Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            );
            $optionShortDescriptionTable->addColumn(
                'option_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false]
            );
            $optionShortDescriptionTable->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false]
            );
            $optionShortDescriptionTable->addColumn(
                'short_description',
                Table::TYPE_TEXT,
                2000,
                ['nullable' => true]
            );

            $optionShortDescriptionTable->addForeignKey(
                $connection->getForeignKeyName(
                    $optionShortDescriptionTableName,
                    'option_id',
                    $optionTableName,
                    'option_id'
                ),
                'option_id',
                $optionTableName,
                'option_id',
                Table::ACTION_CASCADE
            );

            $optionShortDescriptionTable->addForeignKey(
                $connection->getForeignKeyName(
                    $optionShortDescriptionTableName,
                    'store_id',
                    $storeTableName,
                    'store_id'
                ),
                'store_id',
                $storeTableName,
                'store_id',
                Table::ACTION_CASCADE
            );

            $connection->createTable($optionShortDescriptionTable);
        }
    }
}
