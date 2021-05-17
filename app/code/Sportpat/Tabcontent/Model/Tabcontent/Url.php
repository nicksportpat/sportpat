<?php

namespace Sportpat\Tabcontent\Model\Tabcontent;

use Magento\Framework\UrlInterface;
use Sportpat\Tabcontent\Api\Data\TabcontentInterface;

class Url
{
    /**
     * url builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return string
     */
    public function getListUrl()
    {
        return $this->urlBuilder->getUrl('sportpat_tabcontent/tabcontent/index');
    }

    /**
     * @param TabcontentInterface $tabcontent
     * @return string
     */
    public function getTabcontentUrl(TabcontentInterface $tabcontent)
    {
        return $this->urlBuilder->getUrl('sportpat_tabcontent/tabcontent/view', ['id' => $tabcontent->getId()]);
    }
}
