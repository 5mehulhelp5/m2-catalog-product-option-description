<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionDescription\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @throws \Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        if (version_compare(
            $context->getVersion(),
            '1.1.0',
            '<'
        )) {
            $this->addOptionShortDescriptionTable($connection);
        }

        $setup->endSetup();
    }

    /**
     * @throws \Exception
     */
    private function addOptionShortDescriptionTable(AdapterInterface $connection): void
    {
        $optionValueShortDescriptionTableName = $connection->getTableName('catalog_product_option_type_short_desc');

        if (! $connection->isTableExists($optionValueShortDescriptionTableName)) {
            $optionValueTableName = $connection->getTableName('catalog_product_option_type_value');
            $storeTableName = $connection->getTableName('store');

            $optionValueShortDescriptionTable = $connection->newTable($optionValueShortDescriptionTableName);

            $optionValueShortDescriptionTable->addColumn(
                'id',
                Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            );
            $optionValueShortDescriptionTable->addColumn(
                'option_type_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false]
            );
            $optionValueShortDescriptionTable->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false]
            );
            $optionValueShortDescriptionTable->addColumn(
                'short_description',
                Table::TYPE_TEXT,
                2000,
                ['nullable' => true]
            );

            $optionValueShortDescriptionTable->addForeignKey(
                $connection->getForeignKeyName(
                    $optionValueShortDescriptionTableName,
                    'option_type_id',
                    $optionValueTableName,
                    'option_type_id'
                ),
                'option_type_id',
                $optionValueTableName,
                'option_type_id',
                Table::ACTION_CASCADE
            );

            $optionValueShortDescriptionTable->addForeignKey(
                $connection->getForeignKeyName(
                    $optionValueShortDescriptionTableName,
                    'store_id',
                    $storeTableName,
                    'store_id'
                ),
                'store_id',
                $storeTableName,
                'store_id',
                Table::ACTION_CASCADE
            );

            $connection->createTable($optionValueShortDescriptionTable);
        }
    }
}
