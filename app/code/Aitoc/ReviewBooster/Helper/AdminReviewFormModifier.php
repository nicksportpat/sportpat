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

use Aitoc\ReviewBooster\Api\Data\ReviewDetailsInterface;
use Magento\Framework\Data\Form as DataForm;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Fieldset;

/**
 * Class AdminReviewFormModifier
 */
class AdminReviewFormModifier
{
    const FIELDSET_ID_REVIEW_COMMENT = 'review_comment';
    const FIELD_ID_SEND_TO_CUSTOMER = 'send_to';

    /**
     * @param AbstractElement $fieldset
     */
    public function addFieldProductAdvantages(AbstractElement $fieldset)
    {
        $fieldset->addField(
            ReviewDetailsInterface::PRODUCT_ADVANTAGES,
            'text',
            [
                'label' => __('What I like about this product'),
                'required' => false,
                'name' => 'product_advantages'
            ]
        );
    }

    /**
     * @param AbstractElement $fieldset
     */
    public function addFieldProductDisadvantages(AbstractElement $fieldset)
    {
        $fieldset->addField(
            ReviewDetailsInterface::PRODUCT_DISADVANTAGES,
            'text',
            [
                'label' => __('What I dislike about this product'),
                'required' => false,
                'name' => 'product_disadvantages'
            ]
        );
    }

    /**
     * @param DataForm $form
     */
    public function addFieldsetReviewCommentWithFields(DataForm $form)
    {
        $fieldset = $this->addFieldsetReviewComment($form);

        $this->addFieldAdminTitle($fieldset);
        $this->addFieldComment($fieldset);
        $this->addFieldSentTo($fieldset);
    }

    /**
     * @param DataForm $form
     * @return Fieldset
     */
    private function addFieldsetReviewComment(DataForm $form)
    {
        $reviewCommentFieldset = $form->addFieldset(
            self::FIELDSET_ID_REVIEW_COMMENT,
            [
                'legend' => __('Review Comment'),
                'class' => 'fieldset-wide',
            ]
        );

        return $reviewCommentFieldset;
    }

    /**
     * @param Fieldset $fieldset
     */
    private function addFieldAdminTitle(Fieldset $fieldset)
    {
        $fieldset->addField(
            ReviewDetailsInterface::ADMIN_TITLE,
            'text',
            [
                'name' => ReviewDetailsInterface::ADMIN_TITLE,
                'title' => __('Title'),
                'label' => __('Title'),
                'maxlength' => '50'
            ]
        );
    }

    /**
     * @param AbstractElement $reviewCommentFieldset
     */
    private function addFieldComment(AbstractElement $reviewCommentFieldset)
    {
        $reviewCommentFieldset->addField(
            ReviewDetailsInterface::COMMENT,
            'textarea',
            [
                'name' => ReviewDetailsInterface::COMMENT,
                'title' => __('Comment'),
                'label' => __('Comment'),
                'style' => 'height:24em;',
            ]
        );
    }

    /**
     * @param AbstractElement $reviewCommentFieldset
     */
    private function addFieldSentTo(AbstractElement $reviewCommentFieldset)
    {
        $reviewCommentFieldset->addField(
            self::FIELD_ID_SEND_TO_CUSTOMER,
            'checkbox',
            [
                'name' => self::FIELD_ID_SEND_TO_CUSTOMER,
                'label' => __('Send to customer'),
                'title' => __('Send to customer'),
                'onchange' => 'this.value = this.checked;'
            ]
        );
    }
}