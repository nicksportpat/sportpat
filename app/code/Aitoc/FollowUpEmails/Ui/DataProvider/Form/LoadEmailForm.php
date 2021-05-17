<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Ui\DataProvider\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Aitoc\FollowUpEmails\Model\ResourceModel\CampaignStep\CollectionFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Aitoc\FollowUpEmails\Model\Source\EmailVariables as Variables;
use Magento\Variable\Model\VariableFactory;
use Magento\Email\Model\Template;
use Magento\Framework\App\RequestInterface;

/**
 * Loading form data
 */
class LoadEmailForm extends AbstractDataProvider
{
    /**
     * @var array
     */
    private $loadedData;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var Variables
     */
    protected $variables;

    /**
     * @var VariableFactory
     */
    protected $variableFactory;

    /**
     * @var Template
     */
    protected $emailTemplateModel;

    /**
     * Request instance
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $campaignsCollectionFactory
     * @param UrlInterface $urlBuilder
     * @param Json $serializer
     * @param Variables $variables
     * @param VariableFactory $variableFactory
     * @param Template $emailTemplateModel
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $campaignsCollectionFactory,
        UrlInterface $urlBuilder,
        Json $serializer,
        Variables $variables,
        VariableFactory $variableFactory,
        Template $emailTemplateModel,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    )
    {
        $this->collection = $campaignsCollectionFactory->create();
        $this->urlBuilder = $urlBuilder;
        $this->serializer = $serializer;
        $this->variables = $variables;
        $this->variableFactory = $variableFactory;
        $this->emailTemplateModel = $emailTemplateModel;
        $this->request = $request;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $ajaxLinks['test_email'] = $this->urlBuilder->getUrl('followup/email/ajaxtrialemail');
        $ajaxLinks['update_content'] = $this->urlBuilder->getUrl('followup/email/getemailtemplatecontent');
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        if (!empty($items)) {
            foreach ($items as $item) {
                $campaignId = $item->getId();
                // method load is deprecated but there is no repository to get this model.
                $emailTemplateModel = $this->emailTemplateModel->load($item->getTemplateId());
                $this->loadedData[$campaignId] = $item->getData();
                $this->loadedData[$campaignId]['ajax_links'] = $this->serializer->serialize($ajaxLinks);
                $this->loadedData[$campaignId]['variables'] = $this->serializer->serialize(
                    $this->getVariables($item->getTemplateId())
                );
                $this->loadedData[$campaignId]['template_subject'] = $emailTemplateModel->getTemplateSubject();
                $this->loadedData[$campaignId]['template_content'] = $emailTemplateModel->getTemplateText();
            }
        }
        return $this->loadedData;
    }


    /**
     * @param null $templateId
     * @return array
     */
    private function getVariables($templateId = null)
    {
        $variables = $this->variables->toOptionArray(true);
        $customVariables = $this->variableFactory->create()->getVariablesOptionArray(true);
        if ($customVariables) {
            $variables = array_merge_recursive($variables, $customVariables);
        }

        if ($templateId) {
            $template = $this->emailTemplateModel->load($templateId);
            if ($template->getId() && ($templateVariables = $template->getVariablesOptionArray(true))) {
                $variables[] = $templateVariables;
            }
        }

        return $variables;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        $campaignId = $this->request->getParam('campaign_id');
        $ajaxLinks['test_email'] = $this->urlBuilder->getUrl('followup/email/ajaxtrialemail');
        $ajaxLinks['update_content'] = $this->urlBuilder->getUrl('followup/email/getemailtemplatecontent');
        $this->meta['general']['children']['ajax_links']['arguments']['data']['config']['default'] = $this->serializer->serialize($ajaxLinks);
        $this->meta['general']['children']['variables']['arguments']['data']['config']['default'] = $this->serializer->serialize($this->getVariables());
        if ($campaignId) {
            $this->meta['general']['children']['campaign_id']['arguments']['data']['config']['default'] = $campaignId;
        }
        return $this->meta;
    }
}
