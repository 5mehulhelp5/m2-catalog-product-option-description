<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionDescription\Setup;

use Infrangible\Core\Helper\Attribute;
use Infrangible\Core\Helper\EntityType;
use Infrangible\Core\Helper\Setup;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Validator\ValidateException;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class InstallData implements InstallDataInterface
{
    /** @var CategorySetupFactory */
    protected $categorySetupFactory;

    /** @var Attribute */
    protected $attributeHelper;

    /** @var EntityType */
    protected $entityTypeHelper;

    /** @var Setup */
    protected $setupHelper;

    public function __construct(
        CategorySetupFactory $categorySetupFactory,
        Attribute $attributeHelper,
        EntityType $entityTypeHelper,
        Setup $setupHelper
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->attributeHelper = $attributeHelper;
        $this->entityTypeHelper = $entityTypeHelper;
        $this->setupHelper = $setupHelper;
    }

    /**
     * @throws LocalizedException
     * @throws ValidateException
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();

        $eavSetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $attributeId = $eavSetup->getAttributeId(
            Product::ENTITY,
            'options_description'
        );

        if (! $attributeId) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'options_description',
                [
                    'label'                         => 'Options Description',
                    'type'                          => 'text',
                    'input'                         => 'textarea',
                    'global'                        => ScopedAttributeInterface::SCOPE_STORE,
                    'visible'                       => true,
                    'searchable'                    => false,
                    'filterable'                    => false,
                    'comparable'                    => false,
                    'visible_on_front'              => false,
                    'wysiwyg_enabled'               => true,
                    'pagebuilder_enabled'           => false,
                    'html_allowed_on_front'         => false,
                    'is_visible_in_advanced_search' => false,
                    'is_filterable_in_search'       => false,
                    'used_in_product_listing'       => false,
                    'used_for_sort_by'              => false,
                    'is_configurable'               => false,
                    'used_for_promo_rules'          => false,
                    'is_html_allowed_on_front'      => true,
                    'required'                      => false,
                    'user_defined'                  => false
                ]
            );

            $attributeSetCollection = $this->attributeHelper->getAttributeSetCollection();

            $attributeSetCollection->setEntityTypeFilter($this->entityTypeHelper->getProductEntityTypeId());

            /** @var Set $attributeSet */
            foreach ($attributeSetCollection as $attributeSet) {
                $attributeSortOrder = 100;

                $groupId = $attributeSet->getDefaultGroupId();

                if ($groupId) {
                    $this->setupHelper->addProductAttributeToSetAndGroup(
                        $eavSetup,
                        'exclude_from_sitemap',
                        strval($attributeSet->getId()),
                        strval($groupId),
                        $attributeSortOrder
                    );
                }
            }
        }

        $setup->endSetup();
    }
}