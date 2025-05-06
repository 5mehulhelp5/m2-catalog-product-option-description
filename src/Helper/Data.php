<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionDescription\Helper;

use Magento\Catalog\Model\ResourceModel\Product\Option\Collection;
use Magento\Store\Model\Store;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Data
{
    public function addShortDescriptionToResult(Collection $collection, $storeId): void
    {
        if ($collection->isLoaded() || $collection->hasFlag('short_description')) {
            return;
        }

        $dbAdapter = $collection->getConnection();

        $tableName = $dbAdapter->getTableName('catalog_product_option_short_desc');

        $select = $collection->getSelect();

        $select->joinLeft(
            ['default_option_short_desc' => $tableName],
            sprintf(
                'default_option_short_desc.option_id = main_table.option_id AND %s',
                $dbAdapter->quoteInto(
                    'default_option_short_desc.store_id = ?',
                    Store::DEFAULT_STORE_ID
                )
            ),
            ['default_short_description' => 'short_description']
        );

        $shortDescriptionExpr = $dbAdapter->getCheckSql(
            'store_option_short_desc.short_description IS NULL',
            'default_option_short_desc.short_description',
            'store_option_short_desc.short_description'
        );

        $select->joinLeft(
            ['store_option_short_desc' => $tableName],
            sprintf(
                'store_option_short_desc.option_id = main_table.option_id AND %s',
                $dbAdapter->quoteInto(
                    'store_option_short_desc.store_id = ?',
                    $storeId
                )
            ),
            [
                'store_short_description' => 'short_description',
                'short_description'       => $shortDescriptionExpr
            ]
        );

        $collection->setFlag(
            'short_description',
            true
        );
    }
}
