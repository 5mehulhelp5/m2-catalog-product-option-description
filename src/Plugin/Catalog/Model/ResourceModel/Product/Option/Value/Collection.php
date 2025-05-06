<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionDescription\Plugin\Catalog\Model\ResourceModel\Product\Option\Value;

use Infrangible\CatalogProductOptionDescription\Helper\Data;
use Infrangible\Core\Helper\Stores;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Collection
{
    /** @var Data */
    protected $helper;

    /** @var Stores */
    protected $storeHelper;

    public function __construct(Data $helper, Stores $storeHelper)
    {
        $this->helper = $helper;
        $this->storeHelper = $storeHelper;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function beforeAddTitleToResult(
        \Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection $subject,
        $storeId
    ): void {
        if ($storeId === null) {
            $storeId = $this->storeHelper->getStore()->getId();
        }

        $this->helper->addValueShortDescriptionToResult(
            $subject,
            $storeId
        );
    }
}
