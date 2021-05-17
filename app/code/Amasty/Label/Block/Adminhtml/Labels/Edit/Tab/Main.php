<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
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

/**
 * Cart Price Rule General Information Tab
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Main extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_objectConverter;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $_salesRule;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param RuleFactory $salesRule
     * @param ObjectConverter $objectConverter
     * @param Store $systemStore
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        RuleFactory $salesRule,
        ObjectConverter $objectConverter,
        Store $systemStore,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_objectConverter = $objectConverter;
        $this->_salesRule = $salesRule;
        $this->groupRepository = $groupRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('General');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_amasty_label');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('label_');

        $fieldset = $form->addFieldset('general', ['legend' => __('Label Information')]);
        if ($model->getLabelId()) {
            $fieldset->addField('label_id', 'hidden', ['name' => 'label_id']);
        }
        $fieldset->addField('open_tab_input',
            'hidden',
            [
                'name' => 'open_tab_input',
                'after_element_html' => '<script>
                    require([
                      "jquery",
                      "Amasty_Label/js/amlabel"
                    ], function ($) {
                       $("body").amLabeltabs();
                    });
                 </script>'
            ]
        );

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
            'required' => true
        ));

        $fieldset->addField('status', 'select', array(
            'name' => 'status',
            'label' => __('Status'),
            'title' => __('Status'),
            'values'    => array(
                0 => __('Inactive'),
                1 => __('Active'),
            )
        ));

        $validateClass = sprintf(
            'validate-not-negative-number validate-length maximum-length-%d',
            5
        );
        $fieldset->addField('pos', 'text', array(
            'label'     => __('Priority'),
            'name'      => 'pos',
            'note'      => __('Use 0 to show label first, and 99 to show it last'),
            'class' => $validateClass
        ));

        $fieldset->addField('is_single', 'select', array(
            'label'     => __('Hide if label with higher priority is already applied'),
            'name'      => 'is_single',
            'values'    => array(
                0 => __('No'),
                1 => __('Yes'),
            ),
        ));

        $fieldset->addField('use_for_parent', 'select', array(
            'label'     => __('Use for Parent'),
            'title'     => __('Use for Parent'),
            'name'      => 'use_for_parent',
            'note'      => __('Display child`s label for parent (configurable and grouped products only)'),
            'values'   => array(
                '0' => __('No'),
                '1' => __('Yes'),
            ),
        ));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'stores',
                'multiselect',
                [
                    'label' => __('Store'),
                    'title' => __('Store'),
                    'values' => $this->_systemStore->getStoreValuesForForm(),
                    'name' => 'stores',
                    'required' => true
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'stores',
                'hidden',
                ['name' => 'stores', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
        }
        if(!$model->getData()) {
            $model->setData('status', 1);
        }
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
