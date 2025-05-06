<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionDescription\Block\Product\View\Options;

use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Registry;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\View\Element\Template;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Description extends Template
{
    /** @var Registry */
    protected $registryHelper;

    /** @var Json */
    protected $json;

    /** @var Variables */
    protected $variables;

    /** @var Product */
    private $product;

    public function __construct(
        Template\Context $context,
        Registry $registryHelper,
        Json $json,
        Variables $variables,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );

        $this->registryHelper = $registryHelper;
        $this->json = $json;
        $this->variables = $variables;
    }

    public function getProduct(): Product
    {
        if (! $this->product) {
            if ($this->registryHelper->registry('current_product')) {
                $this->product = $this->registryHelper->registry('current_product');
            } else {
                throw new \LogicException('Product is not defined');
            }
        }

        return $this->product;
    }

    public function getOptionsConfig(): string
    {
        $config = [];

        $product = $this->getProduct();

        $optionsDescription = $product->getData('options_description');

        if (! $this->variables->isEmpty($optionsDescription)) {
            $config[ 'description' ] = $optionsDescription;
        }

        /** @var Option $option */
        foreach ($this->getProduct()->getProductOptionsCollection() as $option) {
            $optionShortDescription = $option->getData('short_description');

            if (! $this->variables->isEmpty($optionShortDescription)) {
                $config[ 'options' ][ $option->getId() ] = $optionShortDescription;
            }
        }

        return $this->json->encode($config);
    }
}
