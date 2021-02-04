<?php
namespace Sga\ProductCopy\Block\Adminhtml\Config;

use Sga\ProductCopy\Block\Adminhtml\Config\Field\Product\Attribute as AttributeField;
use Magento\Framework\DataObject;

class Attributes extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected $_attributeRenderer;

    public function _prepareToRender()
    {
        $this->addColumn(
            'attribute',
            [
                'label' => __('Attribute'),
                'renderer'  => $this->getAttributeRenderer(),
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

    protected function getAttributeRenderer()
    {
        if (!$this->_attributeRenderer) {
            $this->_attributeRenderer = $this->getLayout()->createBlock(
                AttributeField::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_attributeRenderer;
    }

    protected function _prepareArrayRow(DataObject $row)
    {
        $options = [];

        $attribute = $row->getAttribute();
        if ($attribute) {
            $options['option_' . $this->getAttributeRenderer()->calcOptionHash($attribute)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }
}
