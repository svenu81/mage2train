<?php

namespace E3n\CatalogTags\Logger;

use Psr\Log\LoggerInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class Custom implements ObserverInterface
{
    /** @var LoggerInterface  */
    protected $logger;

    public function __construct(
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $log = new \Zend\Log\Writer\FirePhp();
        $log->getFirePhp()->log(print_r($observer->getData(), true));
        return $this;
    }
}