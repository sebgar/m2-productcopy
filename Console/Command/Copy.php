<?php
namespace Sga\ProductCopy\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Sga\ProductCopy\Helper\Copy as CopyHelper;

class Copy extends Command
{
    protected $_input;
    protected $_output;
    protected $_copyHelper;
    protected $_storeManager;
    protected $_productCollectionFactory;

    public function __construct(
        CopyHelper $copyHelper,
        StoreManagerInterface $storeManager,
        ProductCollectionFactory $productCollectionFactory
    ) {
        $this->_copyHelper = $copyHelper;
        $this->_storeManager = $storeManager;
        $this->_productCollectionFactory = $productCollectionFactory;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('sga:product-copy:run')
            ->setDescription('Run Product Copy')
            ->addOption('sku', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'sku')
            ->addOption('store', null, InputOption::VALUE_REQUIRED, 'store code');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_input = $input;
        $this->_output = $output;

        $skus = $input->getOption('sku');
        $storeCode = $input->getOption('store');
        if ($storeCode !== null) {
            $collection = $this->_productCollectionFactory->create()
                ->setStoreId($this->_storeManager->getStore($storeCode)->getId());

            if (count($skus) > 0) {
                $collection->addFieldToFilter('sku', ['in' => $skus]);
            }

            foreach ($collection as $item) {
                $rs = $this->_copyHelper->execute($item);
                if ($rs['message'] !== '') {
                    $this->_output->writeln($rs['message']);
                }
            }
        } else {
            $this->_output->writeln('missing parameters store !');
        }
    }

    protected function _log($message, $level = 0)
    {
        $prefix = str_pad(' ', $level * 4, ' ', STR_PAD_LEFT);
        $this->_output->writeln(date('Y-m-d h:i:s').' '.$prefix.$message);
    }
}
