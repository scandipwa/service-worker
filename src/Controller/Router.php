<?php

namespace ScandiPWA\ServiceWorker\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\Forward;

class Router implements \Magento\Framework\App\RouterInterface
{
    /** @var \Magento\Framework\App\ActionFactory $actionFactory */
    protected $actionFactory;

    /**
     * Router constructor.
     * @param ActionFactory $actionFactory
     */
    public function __construct(
        ActionFactory $actionFactory
    ) {
        $this->actionFactory = $actionFactory;
    }

    /**
     * @inheritdoc
     */
    public function match(RequestInterface $request)
    {
        if (trim($request->getPathInfo(), '/') == 'service-worker.js') {
            $request
                ->setModuleName('serviceworker')
                ->setControllerName('index')
                ->setActionName('index');

            return $this->actionFactory->create(Forward::class, ['request' => $request]);
        }
    }
}
