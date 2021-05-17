<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Block\Adminhtml\Campaign;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class BackButton
 */
class BackButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $backUrl = $this->getBackUrl();

        return [
            'label' => __('Back'),
            'on_click' => "location.href = '{$backUrl}';",
            'class' => 'back',
            'sort_order' => 10,
        ];
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getEventsUrl();
    }

    /**
     * @return string
     */
    protected function getEventsUrl()
    {
        return $this->getUrl('followup/event/index');
    }
}
