<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionDescription\Plugin\Catalog\Ui\DataProvider\Product\Form\Modifier;

use FeWeDev\Base\Arrays;
use Infrangible\Core\Helper\Attribute;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Eav\CompositeConfigProcessor;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class CustomOptions
{
    public const FIELD_SHORT_DESCRIPTION_NAME = 'short_description';

    /** @var Arrays */
    protected $arrays;

    /** @var Attribute */
    protected $attributeHelper;

    /** @var CompositeConfigProcessor */
    protected $wysiwygConfigProcessor;

    public function __construct(
        Arrays $arrays,
        Attribute $attributeHelper,
        CompositeConfigProcessor $wysiwygConfigProcessor
    ) {
        $this->arrays = $arrays;
        $this->attributeHelper = $attributeHelper;
        $this->wysiwygConfigProcessor = $wysiwygConfigProcessor;
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     */
    public function afterModifyMeta(
        \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions $subject,
        array $meta
    ): array {
        return $this->arrays->addDeepValue(
            $meta,
            [
                \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::GROUP_CUSTOM_OPTIONS_NAME,
                'children',
                \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::GRID_OPTIONS_NAME,
                'children',
                'record',
                'children',
                \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::CONTAINER_OPTION,
                'children',
                \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::CONTAINER_COMMON_NAME,
                'children',
                static::FIELD_SHORT_DESCRIPTION_NAME
            ],
            $this->getShortDescriptionFieldConfig(25)
        );
    }

    protected function getShortDescriptionFieldConfig(int $sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Short Description'),
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'dataScope'     => static::FIELD_SHORT_DESCRIPTION_NAME,
                        'dataType'      => Text::NAME,
                        'sortOrder'     => $sortOrder
                    ]
                ]
            ]
        ];
    }
}
