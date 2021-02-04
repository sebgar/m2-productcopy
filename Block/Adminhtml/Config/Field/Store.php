<?php
namespace Sga\ProductCopy\Block\Adminhtml\Config\Field;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Magento\Store\Model\StoreManagerInterface;

class Store extends Select
{
    protected $_storeManager;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;

        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->_storeManager->getStores() as $item) {
                $options[] = [
                    'label' => $item->getName().' ('.$item->getCode().')',
                    'value' => $item->getCode(),
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
