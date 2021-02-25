<?php

namespace ScandiPWA\ServiceWorker\Controller\Index;

use Magento\Framework\UrlInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\View\Asset\Repository;

class Index implements HttpGetActionInterface
{
    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * @var DriverInterface
     */
    private $filesystemDriver;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var UrlInterface
     */
    private $urlModel;

    /**
     * Index constructor.
     *
     * @param DirectoryList $directoryList,
     * @param DriverInterface $filesystemDriver,
     * @param Repository $assetRepo,
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        DirectoryList $directoryList,
        DriverInterface $filesystemDriver,
        Repository $assetRepo,
        ResultFactory $resultFactory,
        UrlInterface $urlModel
    ) {
        $this->directoryList = $directoryList;
        $this->filesystemDriver = $filesystemDriver;
        $this->assetRepo = $assetRepo;
        $this->resultFactory = $resultFactory;
        $this->urlModel = $urlModel;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getServiceWorkerContent(): string
    {
        $staticAbsolutePath = $this->directoryList->getPath(DirectoryList::STATIC_VIEW);
        $frontendLocalePath = $this->assetRepo->getStaticViewFileContext()->getPath();
        $serviceWorkerName = 'service-worker.js';
        $baseUrl = $this->urlModel->getBaseUrl(['_type' => UrlInterface::URL_TYPE_STATIC]);

        $bundleFilePath = sprintf(
            '%s/%s/Magento_Theme/%s',
            $staticAbsolutePath,
            $frontendLocalePath,
            $serviceWorkerName
        );

        if (file_exists($bundleFilePath) !== true) {
            $bundleFilePath = sprintf(
                '%s/%s/Magento_Theme/%s',
                $baseUrl,
                $frontendLocalePath,
                $serviceWorkerName
            );
        }

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
