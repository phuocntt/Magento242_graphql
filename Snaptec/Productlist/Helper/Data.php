<?php

/**
 * Connector data helper
 */

namespace Snaptec\Productlist\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Media path to extension images
     *
     * @var string
     */
    const MEDIA_PATH = 'SnaptecProductList';

    /*
     * Scope Config
     */
    public $scopeConfig;

    /**
     * @var \Magento\Framework\Filesystem
     */
    public $filesystem;

    /**
     * @var \Magento\Framework\HTTP\Adapter\FileTransferFactory
     */
    public $httpFactory;

    /**
     * File Uploader factory
     *
     * @var \Magento\Core\Model\File\UploaderFactory
     */
    public $fileUploaderFactory;

    /*
     * Object Mangager
     */
    public $snaptecObjectManager;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $snaptecObjectManager,
        DirectoryList $directoryList
    ) {
        $this->snaptecObjectManager = $snaptecObjectManager;
        $this->scopeConfig = $this->snaptecObjectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->snaptecObjectManager = $snaptecObjectManager;
        $this->filesystem = $this->snaptecObjectManager->get('\Magento\Framework\Filesystem');
        $this->httpFactory = $this->snaptecObjectManager->create('\Magento\Framework\HTTP\Adapter\FileTransferFactory');
        $this->storeManager = $this->snaptecObjectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $this->fileUploaderFactory = $this->snaptecObjectManager
            ->get('\Magento\MediaStorage\Model\File\UploaderFactory');
        parent::__construct($context);
    }

    /*
     * Get Store Config Value
     */

    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Upload image and return uploaded image file name or false
     *
     * @throws \Exception
     * @param string $scope the request key for file
     * @return bool|string
     */
    public function uploadImage($scope)
    {
        $adapter = $this->httpFactory->create();
        if ($adapter->isUploaded($scope)) {
            // validate image
            if (!$adapter->isValid($scope)) {
                throw new \Exception(__('Uploaded image is not valid.'));
            }
            $uploader = $this->fileUploaderFactory->create(['fileId' => $scope]);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $uploader->setAllowCreateFolders(true);
            $ext = $uploader->getFileExtension();
            if ($uploader->save($this->getBaseDir(), $scope . time() . '.' . $ext)) {
                return 'SnaptecProductList/' . $uploader->getUploadedFileName();
            }
        }
        return false;
    }

    /**
     * Return the base media directory for Snaptec Product List Item images
     *
     * @return string
     */
    public function getBaseDir()
    {
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(self::MEDIA_PATH);
        return $path;
    }

}
