<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */


namespace Aitoc\FollowUpEmails\Helper;

use Aitoc\FollowUpEmails\Api\Setup\V100\CampaignStepTableInterface;
use Aitoc\FollowUpEmails\Helper\Campaign;

/**
 * Class Url
 * @package Aitoc\FollowUpEmails\Helper
 */
class Url
{
    /**
     * @var array
     */
    private $utmParams = [
        CampaignStepTableInterface::COLUMN_NAME_GOOGLE_UTM_SOURCE,
        CampaignStepTableInterface::COLUMN_NAME_GOOGLE_UTM_MEDIUM,
        CampaignStepTableInterface::COLUMN_NAME_GOOGLE_UTM_TERM,
        CampaignStepTableInterface::COLUMN_NAME_GOOGLE_UTM_CONTENT,
        CampaignStepTableInterface::COLUMN_NAME_GOOGLE_UTM_CAMPAIGN
    ];

    /**
     * @var \Aitoc\FollowUpEmails\Helper\Campaign
     */
    private $campaign;

    public function __construct(
        Campaign $campaign
    ) {
        $this->campaign = $campaign;
    }

    /**
     * @param $campaignStep
     * @return string
     */
    public function prepareUtmParamsByCampaignStep($campaignStep)
    {
        $result = '';
        $params = [];

        if ($campaignStep->getEntityId()) {
            foreach ($this->utmParams as $utmParam) {
                $data = $campaignStep->getData($utmParam);

                if ($data) {
                    $params[$utmParam] = $data;
                }
            }

            if ($params) {
                $result = http_build_query($params);
            }
        }

        return $result;
    }

    /**
     * @param array $parts
     * @return string
     */
    public function buildUrl(array $parts)
    {
        return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') .
            ((isset($parts['user']) || isset($parts['host'])) ? '//' : '') .
            (isset($parts['user']) ? "{$parts['user']}" : '') .
            (isset($parts['pass']) ? ":{$parts['pass']}" : '') .
            (isset($parts['user']) ? '@' : '') .
            (isset($parts['host']) ? "{$parts['host']}" : '') .
            (isset($parts['port']) ? ":{$parts['port']}" : '') .
            (isset($parts['path']) ? "{$parts['path']}" : '') .
            (isset($parts['query']) ? "?{$parts['query']}" : '') .
            (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
    }

    /**
     * @param $url
     * @param $emailId
     * @return string
     */
    public function prepareUtmByEmailId($url, $emailId)
    {
        if ($url && $emailId) {
            $campaignStep = $this->campaign->getCampaignStepByEmailId($emailId);

            if ($campaignStep->getEntityId()) {
                $parts = parse_url($url);
                $utmQuery = $this->prepareUtmParamsByCampaignStep($campaignStep);

                if ($utmQuery && $parts && isset($parts['query'])) {
                    $parts['query'] .= '&' . $utmQuery;
                    $url = $this->buildUrl($parts);
                }

                return $url;
            }
        }

        return $url;
    }
}
