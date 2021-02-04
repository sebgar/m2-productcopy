<?php
namespace Sga\ProductCopy\Block\Adminhtml\Config\Field\Product;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;

class Attribute extends Select
{
    protected $_attributeCollectionFactory;

    public function __construct(
        Context $context,
        AttributeCollectionFactory $attributeCollectionFactory,
        array $data = []
    ) {
        $this->_attributeCollectionFactory = $attributeCollectionFactory;

        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            $collection = $this->_attributeCollectionFactory->create()
                ->addOrder('attribute_code', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
            $options = [];
            foreach ($collection as $item) {
                $options[] = [
                    'label' => $item->getAttributeCode().' ('.$item->getFrontendLabel().')',
                    'value' => $item->getAttributeCode(),
                ];
            }

            $this->setOptions($options);
        }

        $column = $this->getColumn();
        if (isset($column['style']) && (string)$column['style'] !== '') {
            $this->setExtraParams('style="'.$column['style'].'"');
        }

        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
