<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Block\Adminhtml\Labels\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject as ObjectConverter;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Amasty\Label\Model\RuleFactory;
use Magento\Store\Model\System\Store;

/**
 * Cart Price Rule General Information Tab
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Conditions extends Generic implements TabInterface
{
    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    private $objectConverter;

    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    private $rendererFieldset;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param ObjectConverter $objectConverter
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        RuleFactory $ruleFactory,
        ObjectConverter $objectConverter,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        \Amasty\Base\Model\Serializer $serializer,
        array $data = []
    ) {
        $this->objectConverter = $objectConverter;
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_conditions = $conditions;
        $this->ruleFactory = $ruleFactory;
        $this->serializer = $serializer;
        $this->rendererFieldset = $rendererFieldset;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Conditions');
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
        /** @var \Amasty\Label\Model\Rule $ruleModel */
        $ruleModel  = $this->ruleFactory->create();

        $form = $this->_formFactory->create();
        /** @var \Magento\Framework\Data\Form $form */
        $form->setHtmlIdPrefix('rule_');

        /* start condition block*/
        if ("" != $model->getData('cond_serialize')) {
            $modelData = $model->getData();
            if (isset($modelData['cond_serialize'])) {
                $ruleModel->setConditions([]);
                $ruleModel->setConditionsSerialized($modelData['cond_serialize']);
                $ruleModel->getConditions()->setJsFormObject('rule_conditions_fieldset');
            }
        }
        if ("" != $model->getData('customer_group_ids')) {
            $model->setData(
                'customer_group_ids',
                $this->serializer->unserialize($model->getData('customer_group_ids'))
            );
        }

        $renderer = $this->rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl('catalog_rule/promo_catalog/newConditionHtml/form/rule_conditions_fieldset')
        );

        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            ['legend' => __('Conditions')]
        )->setRenderer(
            $renderer
        );

        $fieldset->addField(
            'conditions',
            'text',
            [
                'name' => 'conditions',
                'label' => __('Product Conditions'),
                'title' => __('Product Conditions'),
                'required' => true
            ]
        )->setRule(
            $ruleModel
        )->setRenderer(
            $this->_conditions
        );
        /* end condition block*/

        /* start date block*/
        $fldDateRange = $form->addFieldset('timeline', ['legend'=> __('Date Range')]);
        $dateEnabled = $fldDateRange->addField(
            'date_range_enabled',
            'select',
            [
                'label'     => __('Use Date Range'),
                'title'     => __('Use Date Range'),
                'name'      => 'date_range_enabled',
                'options'   => [
                    '0' => __('No'),
                    '1' => __('Yes'),
                ],
            ]
        );

        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );
        $fromDate = $fldDateRange->addField(
            'from_date',
            'date',
            [
                'name' => 'from_date',
                'label' => __('From Date'),
                'title' => __('From Date'),
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                'date_format' => $dateFormat
            ]
        );

        $fromTime = $fldDateRange->addField('from_time', 'text', [
            'name'   => 'from_time',
            'label'  => __('From Time'),
            'title'  => __('From Time'),
            'note'      => __('In format 15:32. If the input field is blank, 00:00 will be used.'),
        ]);

        $toDate = $fldDateRange->addField('to_date', 'date', [
            'name'   => 'to_date',
            'label'  => __('To Date'),
            'title'  => __('To Date'),
            'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
            'date_format' => $dateFormat
        ]);

        $toTime = $fldDateRange->addField('to_time', 'text', [
            'name'   => 'to_time',
            'label'  => __('To Time'),
            'title'  => __('To Time'),
            'note'      => __('In format 19:32. If the input field is blank, 00:00 will be used.'),
        ]);
        /* end date block*/

        /* start sale block*/
        $fldState = $form->addFieldset('state', ['legend'=> __('State')]);
        $fldState->addField('is_new', 'select', [
            'label'     => __('Is New'),
            'name'      => 'is_new',
            'values'    => [
                0 => __('Does not matter'),
                1 => __('No'),
                2 => __('Yes'),
            ],
        ]);

        $isSale = $fldState->addField('is_sale', 'select', [
            'label'     => __('Is on Sale'),
            'name'      => 'is_sale',
            'values'    => [
                0 => __('Does not matter'),
                1 => __('No'),
                2 => __('Yes'),
            ],
        ]);

        $specialPriceOnly = $fldState->addField('special_price_only', 'select', [
            'label'     => __('Use Special Price Only'),
            'name'      => 'special_price_only',
            'note'      => __('For `On Sale` condition'),
            'values'    => [
                0 => __('No'),
                1 => __('Yes'),
            ],
        ]);

        $fldStock = $form->addFieldset('stock', ['legend'=> __('Stock')]);
        $fldStock->addField('stock_status', 'select', [
            'label'     => __('Status'),
            'name'      => 'stock_status',
            'values'    => [
                0 => __('Does not matter'),
                1 => __('Out of Stock'),
                2 => __('In Stock'),
            ],
        ]);

        $stockEnabled = $fldStock->addField('product_stock_enabled', 'select', [
            'label'     => __('Use Stock Range'),
            'title'     => __('Use Stock Range'),
            'name'      => 'product_stock_enabled',
            'options'   => [
                '0' => __('No'),
                '1' => __('Yes'),
            ],
        ]);

        $stockRangeHigher = $fldStock->addField('stock_higher', 'text', [
            'label'  => __('Display if stock is higher than '),
            'title'  => __('Display if stock is higher than '),
            'name'   => 'stock_higher',
            'default'     => null
        ]);

        $stockRangeLess = $fldStock->addField('stock_less', 'text', [
            'label'  => __('Display if stock is lower than '),
            'title'  => __('Display if stock is lower than '),
            'name'   => 'stock_less',
            'default'     => null
        ]);

        $fldPriceRange = $form->addFieldset('price', ['legend'=> __('Price Range')]);
        $priceEnabled = $fldPriceRange->addField('price_range_enabled', 'select', [
            'label'     => __('Use Price Range'),
            'title'     => __('Use Price Range'),
            'name'      => 'price_range_enabled',
            'options'   => [
                '0' => __('No'),
                '1' => __('Yes'),
            ],
        ]);

        $byPrice = $fldPriceRange->addField('by_price', 'select', [
            'label'     => __('By Price'),
            'title'     => __('By Price'),
            'name'      => 'by_price',
            'options'   => [
                '0' => __('Base Price'),
                '1' => __('Special Price'),
                '2' => __('Final Price'),
                '3' => __('Final Price Incl Tax'),
                '4' => __('Starting from Price'),
                '5' => __('Starting to Price'),
            ],
        ]);

        $fromPrice = $fldPriceRange->addField('from_price', 'text', [
            'name'   => 'from_price',
            'label'  => __('From Price'),
            'title'  => __('From Price'),
        ]);

        $toPrice = $fldPriceRange->addField('to_price', 'text', [
            'name'   => 'to_price',
            'label'  => __('To Price'),
            'title'  => __('To Price'),
        ]);

        $fldGroup = $form->addFieldset('customer_group', ['legend'=> __('Customer Groups')]);
        $groupEnabled = $fldGroup->addField('customer_group_enabled', 'select', [
            'label'     => __('Use Customer Groups'),
            'title'     => __('Use Customer Groups'),
            'name'      => 'customer_group_enabled',
            'options'   => [
                '0' => __('No'),
                '1' => __('Yes'),
            ],
        ]);

        $customerGroups = $this->groupRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        $groups = $fldGroup->addField('customer_group_ids', 'multiselect', [
            'label'  => __('For Customer Groups'),
            'title'  => __('For Customer Groups'),
            'name'   => 'customer_group_ids[]',
            'values' => $this->objectConverter->toOptionArray($customerGroups, 'id', 'code'),
        ]);
        /* end sale block*/

        $data = $model->getData();
        if ($data) {
            $data['is_active'] = '1';

            if (isset($data['from_date'])) {
                $dateFrom = explode(" ", $data['from_date']);

                if (isset($dateFrom[1]) && $dateFrom[1] != '00:00:00') {
                    $data['from_time'] = $dateFrom[1];
                }
            }

            if (isset($data['to_date'])) {
                $dateTo = explode(" ", $data['to_date']);

                if (isset($dateTo[1]) && $dateTo[1] != '00:00:00') {
                    $data['to_time'] = $dateTo[1];
                }
            }

            //set form values
            $form->setValues($data);
        }

        // define field dependencies
        /**
         * @var \Magento\Backend\Block\Widget\Form\Element\Dependence
         */
        $dependence = $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Form\Element\Dependence::class)
            // Customer Groups
            ->addFieldMap($groupEnabled->getHtmlId(), $groupEnabled->getName())
            ->addFieldMap($groups->getHtmlId(), $groups->getName())
            ->addFieldDependence(
                $groups->getName(),
                $groupEnabled->getName(),
                '1'
            ) // Price Range
            ->addFieldMap($priceEnabled->getHtmlId(), $priceEnabled->getName())
            ->addFieldMap($byPrice->getHtmlId(), $byPrice->getName())
            ->addFieldMap($fromPrice->getHtmlId(), $fromPrice->getName())
            ->addFieldMap($toPrice->getHtmlId(), $toPrice->getName())
            ->addFieldDependence(
                $byPrice->getName(),
                $priceEnabled->getName(),
                '1'
            )
            ->addFieldDependence(
                $fromPrice->getName(),
                $priceEnabled->getName(),
                '1'
            )
            ->addFieldDependence(
                $toPrice->getName(),
                $priceEnabled->getName(),
                '1'
            ) // Is on Sale
            ->addFieldMap($isSale->getHtmlId(), $isSale->getName())
            ->addFieldMap($specialPriceOnly->getHtmlId(), $specialPriceOnly->getName())
            ->addFieldDependence(
                $specialPriceOnly->getName(),
                $isSale->getName(),
                '2'
            ) // Date Range
            ->addFieldMap($dateEnabled->getHtmlId(), $dateEnabled->getName())
            ->addFieldMap($fromDate->getHtmlId(), $fromDate->getName())
            ->addFieldMap($fromTime->getHtmlId(), $fromTime->getName())
            ->addFieldMap($toDate->getHtmlId(), $toDate->getName())
            ->addFieldMap($toTime->getHtmlId(), $toTime->getName())
            ->addFieldDependence(
                $fromDate->getName(),
                $dateEnabled->getName(),
                '1'
            )
            ->addFieldDependence(
                $fromTime->getName(),
                $dateEnabled->getName(),
                '1'
            )
            ->addFieldDependence(
                $toDate->getName(),
                $dateEnabled->getName(),
                '1'
            )
            ->addFieldDependence(
                $toTime->getName(),
                $dateEnabled->getName(),
                '1'
            )
            ->addFieldMap($stockEnabled->getHtmlId(), $stockEnabled->getName())
            ->addFieldMap($stockRangeLess->getHtmlId(), $stockRangeLess->getName())
            ->addFieldMap($stockRangeHigher->getHtmlId(), $stockRangeHigher->getName())
            ->addFieldDependence(
                $stockRangeLess->getName(),
                $stockEnabled->getName(),
                '1'
            )
            ->addFieldDependence(
                $stockRangeHigher->getName(),
                $stockEnabled->getName(),
                '1'
            );
        $this->setChild('form_after', $dependence);

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
