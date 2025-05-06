<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionDescription\Observer;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Database;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Option\Value;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\Store;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ModelSaveAfter implements ObserverInterface
{
    /** @var Database */
    protected $databaseHelper;

    /** @var Arrays */
    protected $arrays;

    /** @var Variables */
    protected $variables;

    public function __construct(Database $databaseHelper, Arrays $arrays, Variables $variables)
    {
        $this->databaseHelper = $databaseHelper;
        $this->arrays = $arrays;
        $this->variables = $variables;
    }

    /**
     * @throws \Exception
     */
    public function execute(Observer $observer): void
    {
        $object = $observer->getData('object');

        if ($object instanceof Option) {
            $optionId = $object->getData('option_id');
            $storeId = $object->getData('store_id');
            $shortDescription = $object->getData('short_description');

            $dbAdapter = $object->getResource()->getConnection();

            $tableName = $dbAdapter->getTableName('catalog_product_option_short_desc');

            $query = $this->databaseHelper->select(
                $tableName,
                ['id', 'short_description']
            );

            $query->where(
                'option_id = ?',
                $optionId
            );

            $query->where(
                'store_id  = ?',
                $storeId
            );

            $queryResult = $this->databaseHelper->fetchRow(
                $query,
                $dbAdapter
            );

            if ($queryResult === null) {
                $this->databaseHelper->createTableData(
                    $dbAdapter,
                    $tableName,
                    ['option_id' => $optionId, 'store_id' => $storeId, 'short_description' => $shortDescription]
                );
            } else {
                $currentValue = $this->arrays->getValue(
                    $queryResult,
                    'short_description'
                );

                if ($currentValue != $shortDescription) {
                    $id = $this->arrays->getValue(
                        $queryResult,
                        'id'
                    );

                    $this->databaseHelper->updateTableData(
                        $dbAdapter,
                        $tableName,
                        ['short_description' => $shortDescription, 'id' => $id],
                        sprintf(
                            'id = %d',
                            $id
                        )
                    );
                }
            }
        }

        if ($object instanceof Value) {
            $optionTypeId = $object->getData('option_type_id');
            $storeId = $object->getData('store_id');
            $shortDescription = $object->getData('short_description');

            if ($this->variables->isEmpty($shortDescription)) {
                $shortDescription = null;
            }

            $isDeleteRecord = $storeId > Store::DEFAULT_STORE_ID && $shortDescription === null;

            $dbAdapter = $object->getResource()->getConnection();

            $tableName = $dbAdapter->getTableName('catalog_product_option_type_short_desc');

            $query = $this->databaseHelper->select(
                $tableName,
                ['id', 'short_description']
            );

            $query->where(
                'option_type_id = ?',
                $optionTypeId
            );

            $query->where(
                'store_id  = ?',
                $storeId
            );

            $queryResult = $this->databaseHelper->fetchRow(
                $query,
                $dbAdapter
            );

            if ($queryResult === null) {
                if (! $isDeleteRecord) {
                    $this->databaseHelper->createTableData(
                        $dbAdapter,
                        $tableName,
                        [
                            'option_type_id'    => $optionTypeId,
                            'store_id'          => $storeId,
                            'short_description' => $shortDescription
                        ]
                    );
                }
            } else {
                $currentShortDescription = $this->arrays->getValue(
                    $queryResult,
                    'short_description'
                );

                $id = $this->arrays->getValue(
                    $queryResult,
                    'id'
                );

                if ($isDeleteRecord) {
                    $this->databaseHelper->deleteTableData(
                        $dbAdapter,
                        $tableName,
                        sprintf(
                            'id = %d',
                            $id
                        )
                    );
                } elseif ($currentShortDescription != $shortDescription) {

                    $this->databaseHelper->updateTableData(
                        $dbAdapter,
                        $tableName,
                        [
                            'short_description' => $shortDescription
                        ],
                        sprintf(
                            'id = %d',
                            $id
                        )
                    );
                }
            }
        }
    }
}
