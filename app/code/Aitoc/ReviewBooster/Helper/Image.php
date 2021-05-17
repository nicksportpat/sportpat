<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Helper;

use Aitoc\ReviewBooster\Api\Data\ImageInterface;
use Aitoc\ReviewBooster\Api\Service\ConfigProviderInterface as ConfigHelper;
use Aitoc\ReviewBooster\Model\Image as ImageModel;
use Aitoc\ReviewBooster\Model\ImageFactory;
use Aitoc\ReviewBooster\Model\ImageRepository;
use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http\Proxy as HttpRequestProxy;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\File\Uploader;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Image\Adapter\AbstractAdapter;
use Magento\Framework\Image\Adapter\AdapterInterface;
use Magento\Framework\Image\AdapterFactory as ImageAdapterFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Zend\Stdlib\ParametersInterface;

/**
 * Class Image
 */
class Image extends AbstractHelper
{
    /**
     * Configuration path
     */
    const ORIGINAL_REVIEW_IMAGE_DIR = 'review_booster/image';
    const RESIZED_REVIEW_IMAGE_SUBDIR = 'resized';

    /**
     * Review settings
     */
    const ALLOWED_MIME_TYPES = ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'];
    const ALLOWED_FILE_EXTENSIONS = ['jpg', 'jpeg', 'gif', 'png'];

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var RequestInterface|HttpRequestProxy
     */
    private $request;

    /**
     * @var string
     */
    private $reviewImagePath;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ImageAdapterFactory
     */
    private $imageAdapterFactory;

    /**
     * @param Context $context
     * @param Filesystem $fileSystem
     * @param UploaderFactory $uploaderFactory
     * @param ImageFactory $imageFactory
     * @param ImageRepository $imageRepository
     * @param ConfigHelper $configHelper
     * @param StoreManagerInterface $storeManager
     * @param ImageAdapterFactory $imageAdapterFactory
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory,
        ImageFactory $imageFactory,
        ImageRepository $imageRepository,
        ConfigHelper $configHelper,
        StoreManagerInterface $storeManager,
        ImageAdapterFactory $imageAdapterFactory
    ) {
        parent::__construct($context);
        $this->request = $context->getRequest();
        $this->uploaderFactory = $uploaderFactory;
        $this->imageFactory = $imageFactory;
        $this->imageRepository = $imageRepository;
        $this->configHelper = $configHelper;
        $this->filesystem = $fileSystem;
        $this->storeManager = $storeManager;
        $this->imageAdapterFactory = $imageAdapterFactory;

        $this->reviewImagePath = $this->getReviewImagePath($fileSystem);
    }

    /**
     * @param Filesystem $filesystem
     * @return string
     * @throws FileSystemException
     */
    private function getReviewImagePath(Filesystem $filesystem)
    {
        $mediaDirectory = $this->getMediaDirectory($filesystem);

        return $mediaDirectory->getAbsolutePath(self::ORIGINAL_REVIEW_IMAGE_DIR);
    }

    /**
     * @param Filesystem $fileSystem
     * @return WriteInterface
     * @throws FileSystemException
     */
    private function getMediaDirectory(Filesystem $fileSystem)
    {
        return $fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * @param int $reviewId
     * @throws Exception
     */
    public function processImageSaving($reviewId)
    {
        $requestedFiles = $this->getRequestedFiles();
        $uploadedImagesFilenames = $this->uploadImages($requestedFiles);
        $this->generateImagesByData($reviewId, $uploadedImagesFilenames);
    }

    /**
     * @return ParametersInterface
     */
    private function getRequestedFiles()
    {
        return $this->request->getFiles();
    }

    /**
     * @param ParametersInterface $requestedFiles
     * @return array
     * @throws Exception
     */
    private function uploadImages(ParametersInterface $requestedFiles)
    {
        $uploadedImagesFilenames = [];

        foreach ($requestedFiles as $fileInfo) {
            $uploadedImageFilename = $this->uploadImage($fileInfo);

            if (!$uploadedImageFilename) {
                continue;
            }

            $uploadedImagesFilenames[] = $uploadedImageFilename;
        }

        return $uploadedImagesFilenames;
    }

    /**
     * @param array $fileInfo
     * @return null|string
     * @throws Exception
     */
    private function uploadImage($fileInfo)
    {
        //q: In which case this happen?
        if ($fileInfo['type'] == '') {
            return null;
        }

        $this->validateFileType($fileInfo['type']);

        return $this->uploadFileAndGetName($fileInfo);
    }

    /**
     * @param string $mimeType
     * @throws Exception
     */
    private function validateFileType($mimeType)
    {
        $allowedMimeTypes = $this->getAllowedMimeTypes();

        if (!in_array($mimeType, $allowedMimeTypes)) {
            throw new Exception('The uploaded file(s) is not an image.');
        }
    }

    /**
     * @return array
     */
    private function getAllowedMimeTypes()
    {
        return self::ALLOWED_MIME_TYPES;
    }

    /**
     * @param array $fileInfo
     * @return string $filename
     * @throws Exception
     */
    private function uploadFileAndGetName($fileInfo)
    {
        $uploader = $this->createConfiguredUploader($fileInfo);
        $result = $uploader->save($this->reviewImagePath);

        return $result['file'];
    }

    /**
     * @param array $fileInfo
     * @return Uploader
     */
    private function createConfiguredUploader($fileInfo)
    {
        $uploader = $this->createUploader(['fileId' => $fileInfo]);

        $this->configureUploader($uploader);

        return $uploader;
    }

    /**
     * @param $data
     * @return Uploader
     */
    private function createUploader($data)
    {
        return $this->uploaderFactory->create($data);
    }

    /**
     * @param Uploader $uploader
     */
    private function configureUploader(Uploader $uploader)
    {
        $allowedExtensions = $this->getAllowedExtensions();

        $uploader
            ->setAllowedExtensions($allowedExtensions)
            ->setAllowRenameFiles(true)
            ->setFilesDispersion(true)
            ->setAllowCreateFolders(true);
    }

    /**
     * @return array
     */
    private function getAllowedExtensions()
    {
        return self::ALLOWED_FILE_EXTENSIONS;
    }

    /**
     * @param int $reviewId
     * @param string[] $imageFilenames
     * @throws CouldNotSaveException
     */
    private function generateImagesByData($reviewId, $imageFilenames)
    {
        foreach ($imageFilenames as $imageFilename) {
            $this->generateImageByData($reviewId, $imageFilename);
        }
    }

    /**
     * @param int $reviewId
     * @param string $imageFilename
     * @throws CouldNotSaveException
     */
    private function generateImageByData($reviewId, $imageFilename)
    {
        $imageModel = $this->createImageByData($reviewId, $imageFilename);
        $this->saveImage($imageModel);
    }

    /**
     * @param int $reviewId
     * @param string $imageFilename
     * @return ImageInterface
     */
    private function createImageByData($reviewId, $imageFilename)
    {
        $image = $this->createImage();
        $this->updateImageByData($image, $reviewId, $imageFilename);

        return $image;
    }

    /**
     * @return ImageModel
     */
    private function createImage()
    {
        return $this->imageFactory->create();
    }

    /**
     * @param ImageInterface $image
     * @param int $reviewId
     * @param string $imageFilename
     */
    private function updateImageByData(ImageInterface $image, $reviewId, $imageFilename)
    {
        $image
            ->setImage($imageFilename)
            ->setReviewId($reviewId)
        ;
    }

    /**
     * @param ImageInterface $image
     * @return ImageInterface
     * @throws CouldNotSaveException
     */
    private function saveImage(ImageInterface $image)
    {
        return $this->imageRepository->save($image);
    }

    /**
     * @param int $reviewId
     * @return array
     */
    public function getImagesFilenamesByReviewId($reviewId)
    {
        $images = $this->getImagesByReviewId($reviewId);

        return $this->getImagesFilenames($images);
    }

    /**
     * @param $reviewId
     * @return ImageInterface[]|DataObject[]
     */
    private function getImagesByReviewId($reviewId)
    {
        return $this->imageRepository->getByReviewId($reviewId);
    }

    /**
     * @param ImageInterface[] $images
     * @return array
     */
    private function getImagesFilenames($images)
    {
        $imagesFilenames = [];

        foreach ($images as $imageId => $image) {
            $imageFilename = $image->getImage();
            $imagesFilenames[$imageId] = $imageFilename;
        }

        return $imagesFilenames;
    }

    /**
     * @param string $image
     * @param int $websiteId
     * @return string
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function getResizedImageUrlAndResizeIfRequired($image, $websiteId)
    {
        $width = $this->getConfigImageWidth($websiteId);

        $mediaDirectoryRead = $this->getMediaDirectoryRead();
        $resizedImageAbsolutePath = $this->getResizedImageAbsolutePath($mediaDirectoryRead, $width, $image);

        if (!file_exists($resizedImageAbsolutePath)) {
            $this->resizeImage($image, $mediaDirectoryRead, $width, $resizedImageAbsolutePath, $websiteId);
        }

        return $this->getResizedImageUrl($image, $width);
    }

    /**
     * @param int $websiteId
     * @return int
     */
    private function getConfigImageWidth($websiteId)
    {
        return $this->configHelper->getImageWidth($websiteId);
    }

    /**
     * @return ReadInterface
     */
    private function getMediaDirectoryRead()
    {
        return $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
    }

    /**
     * @param ReadInterface $mediaDirectoryRead
     * @param $image
     * @param $width
     * @return string
     */
    private function getResizedImageAbsolutePath(ReadInterface $mediaDirectoryRead, $width, $image)
    {
        return $this->getResizedImageAbsoluteDir($mediaDirectoryRead, $width) . $image;
    }

    /**
     * @param ReadInterface $mediaDirectoryRead
     * @param int $width
     * @return string
     */
    private function getResizedImageAbsoluteDir(ReadInterface $mediaDirectoryRead, $width)
    {
        return $mediaDirectoryRead->getAbsolutePath(
            self::ORIGINAL_REVIEW_IMAGE_DIR
            . DIRECTORY_SEPARATOR
            . self::RESIZED_REVIEW_IMAGE_SUBDIR
            . DIRECTORY_SEPARATOR
            . $width
        );
    }

    /**
     * @param $image
     * @param $mediaDirectoryRead
     * @param $width
     * @param $resizedImageAbsolutePath
     * @param int $websiteId
     * @throws Exception
     */
    private function resizeImage($image, $mediaDirectoryRead, $width, $resizedImageAbsolutePath, $websiteId)
    {
        $height = $this->getConfigImageHeight($websiteId);
        $originalImageAbsolutePath = $this->getOriginalImageAbsolutePath($mediaDirectoryRead, $image);

        $imageAdapter = $this->createImageAdapter();
        $imageAdapter->open($originalImageAbsolutePath);
        $imageAdapter->constrainOnly(true);
        $imageAdapter->keepTransparency(true);
        $imageAdapter->keepFrame(false);
        $imageAdapter->keepAspectRatio(true);
        $imageAdapter->resize($width, $height);
        $imageAdapter->save($resizedImageAbsolutePath);
    }

    /**
     * @param int $websiteId
     * @return int
     */
    private function getConfigImageHeight($websiteId)
    {
        return $this->configHelper->getImageHeight($websiteId);
    }

    /**
     * @param ReadInterface $mediaDirectoryRead
     * @param string $image
     * @return string
     */
    private function getOriginalImageAbsolutePath(ReadInterface $mediaDirectoryRead, $image)
    {
        return $mediaDirectoryRead->getAbsolutePath(self::ORIGINAL_REVIEW_IMAGE_DIR) . $image;
    }

    /**
     * @return AdapterInterface|AbstractAdapter
     */
    private function createImageAdapter()
    {
        return $this->imageAdapterFactory->create();
    }

    /**
     * @param string $image
     * @param int $width
     * @return string
     * @throws NoSuchEntityException
     */
    private function getResizedImageUrl($image, $width)
    {
        return $this->getStoreMediaUrl()
            . self::ORIGINAL_REVIEW_IMAGE_DIR
            . '/' . self::RESIZED_REVIEW_IMAGE_SUBDIR
            . '/' . $width
            . $image;
    }

    /**
     * Get current url
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getStoreMediaUrl()
    {
        $store = $this->getStore();

        return $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @return StoreInterface|Store
     * @throws NoSuchEntityException
     */
    private function getStore()
    {
        return $this->storeManager->getStore();
    }
}
