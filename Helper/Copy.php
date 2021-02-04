<?php
namespace Sga\ProductCopy\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product;
use Sga\ProductCopy\Helper\Config as ConfigHelper;

class Copy extends AbstractHelper
{
    protected $_helperConfig;
    protected $_storeManager;

    public function __construct(
        Context $context,
        ConfigHelper $configHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->_helperConfig = $configHelper;
        $this->_storeManager = $storeManager;

        parent::__construct($context);
    }

    public function execute(Product $product)
    {
        $rs = array(
            'ok' => true,
            'message' => ''
        );

        if ($this->_helperConfig->isEnabled()) {
            $storeId = (int)$product->getStoreId();
            if ($storeId > 0) {
                $mappingLangStore = $this->_helperConfig->getMappingLangStore($storeId);

                // search if current store is master for lang
                $langToProcess = '';
                foreach ($mappingLangStore as $line) {
                    if ((int)$this->_storeManager->getStore($line['store_code'])->getId() === $storeId) {
                        $langToProcess = $line['lang'];
                        break;
                    }
                }

                if ($langToProcess !== '') {
                    $attributeLines = $this->_helperConfig->getMappingAttributes($storeId);

                    // search all store which use this lang
                    $stores = $this->_getStoreForLang($langToProcess);
                    $storeCodes = [];
                    foreach ($stores as $store) {
                        // dont process current store
                        if ((int)$store->getId() === $storeId) {
                            continue;
                        }

                        $storeCodes[] = $store->getCode();

                        // change storeId
                        $product->setStoreId($store->getId());

                        // process attributes
                        foreach ($attributeLines as $attributeLine) {
                            $product->getResource()->saveAttribute($product, $attributeLine['attribute']);
                        }
                    }

                    // change storeId to default
                    $product->setStoreId($storeId);

                    $rs['message'] = __('Product %1 - Copy Data On Store : %2', $product->getSku(), implode(', ', $storeCodes));
                }
            }
        }

        return $rs;
    }

    protected function _getStoreForLang($lang)
    {
        $stores = [];
        foreach ($this->_storeManager->getStores() as $store) {
            if ($this->scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE, $store->getId()) === $lang) {
                $stores[] = $store;
            }
        }
        return $stores;
    }
}
