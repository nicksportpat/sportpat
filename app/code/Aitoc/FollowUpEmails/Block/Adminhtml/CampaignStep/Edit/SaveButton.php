<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */


namespace Aitoc\FollowUpEmails\Block\Adminhtml\CampaignStep\Edit;

use Magento\Ui\Component\Control\Container;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Aitoc\FollowUpEmails\Block\Adminhtml\CampaignStep\GenericButton;

class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'aitoc_followup_event_campaignstep_edit.aitoc_followup_event_campaignstep_edit',
                                'actionName' => 'save',
                                'params' => [
                                    false
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'class_name' => Container::SPLIT_BUTTON,
            'options' => $this->getOptions(),
        ];
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    protected function getOptions()
    {
        $options[] = [
            'id_hard' => 'save_and_continue1',
            'label' => __('Save & Continue'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'aitoc_followup_event_campaignstep_edit.aitoc_followup_event_campaignstep_edit',
                                'actionName' => 'save',
                                'params' => [true, ['back' => 'continue']]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        $options[] = [
            'id_hard' => 'save_and_new',
            'label' => __('Save & New'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'aitoc_followup_event_campaignstep_edit.aitoc_followup_event_campaignstep_edit',
                                'actionName' => 'save',
                                'params' => [true, ['back' => 'new']]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        return $options;
    }
}
