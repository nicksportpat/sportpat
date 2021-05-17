<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Model;

use Aitoc\ReviewBooster\Api\ImageRepositoryInterface;
use Aitoc\ReviewBooster\Api\Data\ImageInterface;
use Aitoc\ReviewBooster\Model\ResourceModel\Image as ImageResourceModel;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aitoc\ReviewBooster\Model\ResourceModel\Image\Collection as ImageCollection;
use Aitoc\ReviewBooster\Model\ResourceModel\Image\CollectionFactory as ImageCollectionFactory;
use Magento\Framework\Model\AbstractModel;

/**
 * Class ImageRepository
 */
class ImageRepository implements ImageRepositoryInterface
{
    /**
     * @var ImageResourceModel
     */
    protected $imageModelResource;

    /**
     * @var ImageCollectionFactory
     */
    protected $imageCollectionFactory;

    /**
     * @var ImageFactory
     */
    protected $imageModelFactory;

    /**
     * @param ImageResourceModel $imageModelResource
     * @param ImageCollectionFactory $imageCollectionFactory
     * @param ImageFactory $imageModelFactory
     */
    public function __construct(
        ImageResourceModel $imageModelResource,
        ImageCollectionFactory $imageCollectionFactory,
        ImageFactory $imageModelFactory
    ) {
        $this->imageModelResource = $imageModelResource;
        $this->imageCollectionFactory = $imageCollectionFactory;
        $this->imageModelFactory = $imageModelFactory;
    }

    /**
     * @param ImageInterface|AbstractModel $imageModel
     * @return ImageInterface
     * @throws CouldNotSaveException
     */
    public function save(ImageInterface $imageModel)
    {
        try {
            $this->imageModelResource->save($imageModel);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save model %1', $imageModel->getEntityId()));
        }

        return $imageModel;
    }

    /**
     * @param int $entityId
     * @return ImageInterface|Image
     * @throws NoSuchEntityException
     */
    public function get($entityId)
    {
        $imageModel = $this->imageModelFactory->create();
        $this->imageModelResource->load($imageModel, $entityId);

        if (!$imageModel->getEntityId()) {
            throw new NoSuchEntityException(__('Entity with specified ID "%1" not found.', $entityId));
        }

        return $imageModel;
    }

    /**
     * @inheritDoc
     */
    public function getByReviewId($reviewId)
    {
        $imageCollection = $this->createImageCollection();
        $imageCollection->addFieldToFilter(ImageInterface::REVIEW_ID, $reviewId);

        return $imageCollection->getItems();
    }

    /**
     * @return ImageCollection
     */
    private function createImageCollection()
    {
        return $this->imageCollectionFactory->create();
    }

    /**
     * @param ImageInterface|AbstractModel $imageModel
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ImageInterface $imageModel)
    {
        try {
            $this->imageModelResource->delete($imageModel);
        } catch (ValidationException $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to remove entity with ID%', $imageModel->getEntityId()));
        }

        return true;
    }

    /**
     * @param int $entityId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($entityId)
    {
        $model = $this->get($entityId);
        $this->delete($model);

        return true;
    }
}
