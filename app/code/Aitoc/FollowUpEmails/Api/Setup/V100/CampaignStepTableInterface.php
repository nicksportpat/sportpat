<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api\Setup\V100;

/**
 * Interface CampaignStepsTableInterface
 */
interface CampaignStepTableInterface
{
    const TABLE_NAME = 'aitoc_follow_up_emails_campaign_steps';

    const COLUMN_NAME_ENTITY_ID = 'entity_id';
    const COLUMN_NAME_CAMPAIGN_ID = 'campaign_id';
    const COLUMN_NAME_TEMPLATE_ID = 'template_id';
    const COLUMN_NAME_DELAY_PERIOD = 'delay_period';
    const COLUMN_NAME_DELAY_UNIT = 'unit';
    const COLUMN_NAME_NAME = 'name';
    const COLUMN_NAME_STATUS = 'status';
    const COLUMN_NAME_DISCOUNT_STATUS = 'discount_status';
    const COLUMN_NAME_DISCOUNT_PERCENT = 'discount_percent';
    const COLUMN_NAME_DISCOUNT_PERIOD = 'discount_period';
    const COLUMN_NAME_DISCOUNT_TYPE = 'discount_type';
    const COLUMN_NAME_SALES_RULE = 'sales_rule_id';
    const COLUMN_NAME_GOOGLE_UTM_SOURCE = 'utm_source';
    const COLUMN_NAME_GOOGLE_UTM_MEDIUM = 'utm_medium';
    const COLUMN_NAME_GOOGLE_UTM_TERM = 'utm_term';
    const COLUMN_NAME_GOOGLE_UTM_CONTENT = 'utm_content';
    const COLUMN_NAME_GOOGLE_UTM_CAMPAIGN = 'utm_campaign';
    const COLUMN_NAME_OPTIONS = 'options';
}
