<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Controller\Adminhtml\Event\Campaign;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

/**
 * Class Index
 */
class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    //TODO :  ACL
    //const ADMIN_RESOURCE = 'Aitoc_FollowUpEmails::Campaigns';

    public function execute()
    {
        if (!$this->getRequest()->getParam('event_code')) {
            return $this->_redirect('followup/event/index');
        }
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
