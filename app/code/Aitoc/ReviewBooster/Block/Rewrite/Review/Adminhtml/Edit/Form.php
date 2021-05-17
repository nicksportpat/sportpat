<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Block\Rewrite\Review\Adminhtml\Edit;

use Aitoc\ReviewBooster\Api\Data\ImageInterface;
use Aitoc\ReviewBooster\Api\Data\ReviewDetailsInterface;
use Aitoc\ReviewBooster\Api\ImageRepositoryInterface;
use Aitoc\ReviewBooster\Helper\AdminReviewFormModifier;
use Aitoc\ReviewBooster\Helper\Image as ImageHelper;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Data\Form as DataForm;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Review\Block\Adminhtml\Edit\Form as EditForm;
use Magento\Review\Helper\Data;
use Magento\Review\Model\Review;
use Magento\Store\Model\System\Store;

/**
 * Class Form
 */
class Form extends EditForm
{
    const FIELDSET_ID_REVIEW_DETAILS = 'review_details';
    const FIELD_ID_CUSTOMER = 'customer';

    /**
     * @var ImageRepositoryInterface
     */
    private $imageRepository;

    /**
     * @var AdminReviewFormModifier
     */
    private $adminReviewFormModifier;

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param CustomerRepositoryInterface $customerRepository
     * @param ProductFactory $productFactory
     * @param Data $reviewData
     * @param ImageRepositoryInterface $imageRepository
     * @param AdminReviewFormModifier $adminReviewFormModifier
     * @param ImageHelper $imageHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        CustomerRepositoryInterface $customerRepository,
        ProductFactory $productFactory,
        Data $reviewData,
        ImageRepositoryInterface $imageRepository,
        AdminReviewFormModifier $adminReviewFormModifier,
        ImageHelper $imageHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $systemStore, $customerRepository, $productFactory, $reviewData, $data);
        $this->imageRepository = $imageRepository;
        $this->adminReviewFormModifier = $adminReviewFormModifier;
        $this->imageHelper = $imageHelper;
    }

    /**
     * Prepare edit review form
     *
     * @return $this
     * @throws NoSuchEntityException
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $form = $this->getForm();
        $this->modifyReviewDetailsFieldset($form);
        $this->addFieldsetReviewCommentWithFields($form);

        $review = $this->getReviewData();
        $form->setValues($review->getData());

        return $this;
    }

    /**
     * @param DataForm $form
     * @throws NoSuchEntityException
     */
    private function modifyReviewDetailsFieldset(DataForm $form)
    {
        $reviewDetailsFieldset = $this->getReviewDetailsFieldsetByForm($form);
        $review = $this->getRegistryReview();

        $this->addCustomerFieldVerifiedText($reviewDetailsFieldset, $review);

        $this->addFieldProductAdvantages($reviewDetailsFieldset);
        $this->addFieldProductDisadvantages($reviewDetailsFieldset);

        $this->addImagesFields($review, $reviewDetailsFieldset);

        $this->addFieldReviewReported($reviewDetailsFieldset, $review);
        $this->addFieldReviewHelpful($reviewDetailsFieldset, $review);
        $this->addFieldReviewUnhelpfull($reviewDetailsFieldset, $review);
    }

    /**
     * @param DataForm $form
     * @return AbstractElement|null
     */
    private function getReviewDetailsFieldsetByForm(DataForm $form)
    {
        return $form->getElement(self::FIELDSET_ID_REVIEW_DETAILS);
    }

    /**
     * @return Review
     */
    private function getRegistryReview()
    {
        return $this->_coreRegistry->registry('review_data');
    }

    /**
     * @param AbstractElement $reviewDetailsFieldset
     * @param Review $review
     */
    private function addCustomerFieldVerifiedText(AbstractElement $reviewDetailsFieldset, Review $review)
    {
        $customerField = $this->getCustomerFieldByFieldset($reviewDetailsFieldset);
        $this->addVerifiedStateText($customerField, $review);
    }

    /**
     * @param AbstractElement $fieldset
     * @return AbstractElement
     */
    private function getCustomerFieldByFieldset(AbstractElement $fieldset)
    {
        return $this->getFieldsetElementById($fieldset, self::FIELD_ID_CUSTOMER);
    }

    /**
     * @param AbstractElement $fieldset
     * @param int $elementId
     * @return AbstractElement
     */
    private function getFieldsetElementById(AbstractElement $fieldset, $elementId)
    {
        return $fieldset->getElements()->searchById($elementId);
    }

    /**
     * @param AbstractElement $field
     * @param Review $review
     */
    private function addVerifiedStateText(AbstractElement $field, Review $review)
    {
        $origText = $field->getData('text');

        $verifiedStateText = $this->getVerifiedStateText($review);

        $newText = $origText . $verifiedStateText;

        $field->setData('text', $newText);
    }

    /**
     * @param Review $review
     * @return string
     */
    private function getVerifiedStateText(Review $review)
    {
        return $this->getVerifiedStateTextByReview($review);
    }

    /**
     * @param Review $review
     * @return string
     */
    private function getVerifiedStateTextByReview(Review $review)
    {
        $isCustomerVerified = $review->getData(ReviewDetailsInterface::CUSTOMER_VERIFIED);

        return $this->getVerifiedStateTextByVerifiedState($isCustomerVerified);
    }

    /**
     * @param int $verifiedState
     * @return string
     */
    private function getVerifiedStateTextByVerifiedState($verifiedState)
    {
        if ($verifiedState) {
            $verifiedStateValue = __('verified');
            $verifiedStateTitle = __('Verified reviews are written by shoppers who purchased this item.');
        } else {
            $verifiedStateValue = __('not verified');
            $verifiedStateTitle = __('Review posted by an unregistered customer or by a customer who never purchased this item.');
        }

        return <<<TXT
 - <span title="{$verifiedStateTitle}">{$verifiedStateValue}</span>
TXT;
    }

    /**
     * @param AbstractElement  $fieldset
     */
    private function addFieldProductAdvantages(AbstractElement $fieldset)
    {
        $this->adminReviewFormModifier->addFieldProductAdvantages($fieldset);
    }

    /**
     * @param AbstractElement $fieldset
     */
    private function addFieldProductDisadvantages(AbstractElement $fieldset)
    {
        $this->adminReviewFormModifier->addFieldProductDisadvantages($fieldset);
    }

    /**
     * @param Review $review
     * @param AbstractElement $fieldset
     * @throws NoSuchEntityException
     */
    private function addImagesFields(Review $review, AbstractElement $fieldset)
    {
        $reviewId = $review->getId();
        $images = $this->getImagesByReviewId($reviewId);

        foreach ($images as $key => $image) {
            $this->addImageField($fieldset, $key, $image);
        }
    }

    /**
     * @param int $reviewId
     * @return ImageInterface[]
     */
    private function getImagesByReviewId($reviewId)
    {
        return $this->imageRepository->getByReviewId($reviewId);
    }

    /**
     * @param AbstractElement $fieldset
     * @param $key
     * @param $image
     * @throws NoSuchEntityException
     */
    private function addImageField(AbstractElement $fieldset, $key, $image)
    {
        $fieldId = 'aitoc_review_image' . $key;
        $imageUrl = $this->getImageUrl($image);
        $text = <<<IMGURL
<img class="aitoc_review_image" src="{$imageUrl}"/>
IMGURL;

        $fieldset->addField(
            $fieldId,
            'note',
            [
                'label' => 'Image',
                'text' => $text,
            ]
        );
    }

    /**
     * @param ImageInterface $image
     * @return string
     * @throws NoSuchEntityException
     */
    private function getImageUrl(ImageInterface $image)
    {
        $storeMediaUrl = $this->getStoreMediaUrl();
        $imageDir = ImageHelper::ORIGINAL_REVIEW_IMAGE_DIR;
        $imageFilename = $image->getImage();

        return "{$storeMediaUrl}{$imageDir}{$imageFilename}";
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    private function getStoreMediaUrl()
    {
        return $this->imageHelper->getStoreMediaUrl();
    }

    /**
     * @param AbstractElement $fieldset
     * @param Review $review
     */
    private function addFieldReviewReported(AbstractElement $fieldset, Review $review)
    {
        $abuseReportedCount = (int) $review->getData(ReviewDetailsInterface::REVIEW_REPORTED);

        $fieldset->addField(
            ReviewDetailsInterface::REVIEW_REPORTED,
            'note',
            [
                'label' => '',
                'text' => __('%1 abuse reports submitted', $abuseReportedCount),
            ]
        );
    }

    /**
     * @param AbstractElement $fieldset
     * @param Review $review
     */
    private function addFieldReviewHelpful(AbstractElement $fieldset, Review $review)
    {
        $helpfulCount = (int) $review->getData(ReviewDetailsInterface::REVIEW_HELPFUL);

        $fieldset->addField(
            ReviewDetailsInterface::REVIEW_HELPFUL,
            'note',
            ['label' => '', 'text' => __('%1 people found this helpful', $helpfulCount)]
        );
    }

    /**
     * @param AbstractElement $reviewDetailsFieldset
     * @param Review $review
     */
    private function addFieldReviewUnhelpfull(AbstractElement $reviewDetailsFieldset, Review $review)
    {
        $unhelpfulCount = (int) $review->getData(ReviewDetailsInterface::REVIEW_UNHELPFUL);

        $reviewDetailsFieldset->addField(
            ReviewDetailsInterface::REVIEW_UNHELPFUL,
            'note',
            [
                'label' => '',
                'text' => __('%1 people found this unhelpful', $unhelpfulCount),
            ]
        );
    }

    /**
     * @param DataForm $form
     */
    private function addFieldsetReviewCommentWithFields(DataForm $form)
    {
        $this->adminReviewFormModifier->addFieldsetReviewCommentWithFields($form);
    }

    /**
     * @return array
     */
    private function getReviewData()
    {
        return $this->_coreRegistry->registry('review_data');
    }
}
