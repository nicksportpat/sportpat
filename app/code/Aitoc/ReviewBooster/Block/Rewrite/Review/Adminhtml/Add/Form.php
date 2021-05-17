<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Block\Rewrite\Review\Adminhtml\Add;

use Aitoc\ReviewBooster\Helper\AdminReviewFormModifier;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form as DataForm;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Review\Block\Adminhtml\Add\Form as AddForm;
use Magento\Review\Helper\Data;
use Magento\Store\Model\System\Store;

class Form extends AddForm
{
    const FIELDSET_ID_ADD_REVIEW_FORM = 'add_review_form';

    /**
     * @var AdminReviewFormModifier
     */
    private $adminReviewFormModifier;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        Data $reviewData,
        AdminReviewFormModifier $adminReviewFormModifier,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $systemStore, $reviewData, $data);
        $this->adminReviewFormModifier = $adminReviewFormModifier;
    }

    /**
     * Prepare add review form
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $form = $this->getForm();

        $this->modifyAddReviewFormFieldset($form);
        $this->addFieldsetReviewCommentWithFields($form);
    }

    /**
     * @param DataForm $form
     */
    private function modifyAddReviewFormFieldset(DataForm $form)
    {
        $fieldset = $this->getReviewDetailsFieldsetByForm($form);

        $this->addFieldProductAdvantages($fieldset);
        $this->addFieldProductDisadvantages($fieldset);
    }

    /**
     * @param DataForm $form
     * @return AbstractElement|null
     */
    private function getReviewDetailsFieldsetByForm(DataForm $form)
    {
        return $form->getElement(self::FIELDSET_ID_ADD_REVIEW_FORM);
    }

    /**
     * @param AbstractElement $fieldset
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
     * @param DataForm $form
     */
    protected function addFieldsetReviewCommentWithFields(DataForm $form)
    {
        $this->adminReviewFormModifier->addFieldsetReviewCommentWithFields($form);
    }

    /**
     * @param Fieldset $fieldset
     */
    protected function addFieldNickname(Fieldset $fieldset)
    {
        $fieldset->addField(
            'nickname',
            'text',
            [
                'name' => 'nickname',
                'title' => __('Nickname'),
                'label' => __('Nickname'),
                'maxlength' => '50',
                'required' => true
            ]
        );
    }

    /**
     * @param Fieldset $fieldset
     */
    protected function addFieldTitle(Fieldset $fieldset)
    {
        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'title' => __('Summary of Review'),
                'label' => __('Summary of Review'),
                'maxlength' => '255',
                'required' => true
            ]
        );
    }

    /**
     * @param Fieldset $fieldset
     */
    protected function addFieldDetail(Fieldset $fieldset)
    {
        $fieldset->addField(
            'detail',
            'textarea',
            [
                'name' => 'detail',
                'title' => __('Review'),
                'label' => __('Review'),
                'required' => true
            ]
        );
    }

    /**
     * @param Fieldset $fieldset
     */
    protected function addFieldProductId(Fieldset $fieldset)
    {
        $fieldset->addField(
            'product_id',
            'hidden',
            [
                'name' => 'product_id',
            ]
        );
    }
}
