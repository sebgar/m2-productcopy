<?php
namespace Sga\ProductCopy\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_ENABLED = 'sga_productcopy/general/enabled';
    const XML_PATH_MAPPING_LANG_STORE = 'sga_productcopy/mapping/lang_store';
    const XML_PATH_MAPPING_ATTRIBUTES = 'sga_productcopy/mapping/attributes';

    public function isEnabled($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getMappingLangStore($store = null)
    {
        $str = $this->scopeConfig->getValue(
            self::XML_PATH_MAPPING_LANG_STORE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
        return json_decode($str, true);
    }

    public function getMappingAttributes($store = null)
    {
        $str = $this->scopeConfig->getValue(
            self::XML_PATH_MAPPING_ATTRIBUTES,
            ScopeInterface::SCOPE_STORE,
            $store
        );
        return json_decode($str, true);
    }
}
