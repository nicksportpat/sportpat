<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


// phpcs:ignoreFile

namespace Amasty\Label\Block\Adminhtml\Labels\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject as ObjectConverter;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Store\Model\System\Store;

class Product extends AbstractImage
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Product');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Product');
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_amasty_label');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('labels_');

        $fldProduct = $form->addFieldset('product_page', ['legend'=> __('Product Page')]);

        $fldProduct->addType('color', \Amasty\Label\Block\Adminhtml\Data\Form\Element\Color::class);
        $fldProduct->addType('custom_file', \Amasty\Label\Block\Adminhtml\Data\Form\Element\File::class);
        $fldProduct->addType('preview', \Amasty\Label\Block\Adminhtml\Data\Form\Element\Preview::class);

        $fldProduct->addField(
            'prod_img',
            'custom_file',
            [
                'label'     => __('Label Type'),
                'name'      => 'prod_img',
                'after_element_html' => $this->getImageHtml('prod_img', $model->getProdImg()),
            ]
        );

        $fldProduct->addField(
            'prod_label_color',
            'color',
            [
                'label'     => __('Label Color'),
                'name'      => 'prod_label_color'
            ]
        );

        $fldProduct->addField(
            'prod_pos',
            'select',
            [
                'label'     => __('Label Position'),
                'name'      => 'prod_pos',
                'values'    => $model->getAvailablePositions(),
                'after_element_html' => $this->getPositionHtml('prod_pos')
            ]
        );

        $fldProduct->addField(
            'prod_image_size',
            'text',
            [
                'label'     => __('Label Size'),
                'name'      => 'prod_image_size',
                'note'      => __('Percent of the product image.'),
            ]
        );

        $fldProduct->addField(
            'prod_txt',
            'text',
            [
                'label'     => __('Label Text'),
                'name'      => 'prod_txt',
                'note'      => __($this->_getTextNote()),
            ]
        );

        $fldProduct->addField(
            'prod_color',
            'color',
            [
                'label' => __('Text Color'),
                'name' => 'prod_color'
            ]
        );

        $fldProduct->addField(
            'prod_size',
            'text',
            [
                'label' => __('Text Size'),
                'name' => 'prod_size',
                'note' => __('Example: 12px;'),
            ]
        );

        $fldProduct->addField(
            'prod_style',
            'textarea',
            [
                'label' => __('Advanced Settings/CSS'),
                'name'  => 'prod_style',
                'note'  => __(
                    'Customize label and text styles with CSS parameters. For more information click <a href="https://www.w3schools.com/cssref/default.asp" target="_blank">here</a>.<br> Ex.: text-align: center; line-height: 20px; transform: rotate(-90deg);'
                )
            ]
        );

        if ($model && $model->getId()) {
            $fldProduct->addField(
                'prod_preview',
                'preview',
                [
                    'label' => '',
                    'name'  => 'prod_preview'
                ]
            );
        }

        $data = $model->getData();
        $data = $this->_restoreSizeColor($data);
        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
