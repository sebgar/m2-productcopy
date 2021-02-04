<?php
namespace Sga\ProductCopy\Block\Adminhtml\Config;

use Sga\ProductCopy\Block\Adminhtml\Config\Field\Store as StoreField;
use Magento\Framework\DataObject;

class LangStore extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected $_storeRenderer;

    public function _prepareToRender()
    {
        $this->addColumn(
            'lang',
            [
                'label' => __('Lang'),
                'style' => 'width:150px',
            ]
        );
        $this->addColumn(
            'store_code',
            [
                'label' => __('Store'),
                'renderer'  => $this->getStoreRenderer(),
                'style' => 'width:300px',
            ]
        );
    }

    public function getArrayRows()
    {
        $this->_fixMissingColumn();
        return parent::getArrayRows();
    }

    protected function _fixMissingColumn()
    {
        $element = $this->getElement();
        $values = $element->getValue();
        if ($values && is_array($values)) {
            foreach ($values as $rowId => $row) {
                foreach ($this->getColumns() as $key => $column) {
                    if (!isset($row[$key])) {
                        $values[$rowId][$key] = '';
                    }
                }
            }
            $element->setValue($values);
        }
    }

    protected function getStoreRenderer()
    {
        if (!$this->_storeRenderer) {
            $this->_storeRenderer = $this->getLayout()->createBlock(
                StoreField::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_storeRenderer;
    }

    protected function _prepareArrayRow(DataObject $row)
    {
        $options = [];

        $storeId = $row->getStoreCode();
        if ($storeId) {
            $options['option_' . $this->getStoreRenderer()->calcOptionHash($storeId)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }
}
