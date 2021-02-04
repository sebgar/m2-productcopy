<?php
namespace Sga\ProductCopy\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Sga\ProductCopy\Helper\Copy as CopyHelper;

class ProductSaveCommitAfter implements ObserverInterface
{
    protected $_copyHelper;
    protected $_messageManager;

    public function __construct(
        CopyHelper $copyHelper,
        MessageManagerInterface $messageManager
    ){
        $this->_copyHelper = $copyHelper;
        $this->_messageManager = $messageManager;
    }

    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getDataObject();
        $rs = $this->_copyHelper->execute($product);
        if ($rs['message'] !== '') {
            $this->_messageManager->addSuccessMessage($rs['message']);
        }
    }
}
