<?php

namespace ScandiPWA\ServiceWorker\Controller\Index;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Index implements HttpGetActionInterface
{
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @var DriverInterface
     */
    private $filesystemDriver;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        DirectoryList $directoryList,
        DriverInterface $filesystemDriver,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->directoryList = $directoryList;
        $this->filesystemDriver = $filesystemDriver;
        $this->assetRepo = $assetRepo;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getServiceWorkerContent(): string
    {
        $serviceWorkerName = 'service-worker.js';
        $staticViewDirectory = $this->directoryList->getPath(DirectoryList::STATIC_VIEW);
        $bundleFilePath = sprintf(
            '%s/%s/Magento_Theme/%s',
            $staticViewDirectory,
            $this->assetRepo->getStaticViewFileContext()->getPath(),
            $serviceWorkerName
        );
        return $this->filesystemDriver->fileGetContents($bundleFilePath);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $content = $this->getServiceWorkerContent();
        $resultPage = $this->resultFactory
            ->create(ResultFactory::TYPE_RAW)
            ->setHeader('Content-Type', 'text/javascript')
            ->setHeader('Service-Worker-Allowed', '/')
            ->setContents($content);

        return $resultPage;
    }
}
