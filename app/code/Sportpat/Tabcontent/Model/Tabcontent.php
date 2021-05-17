<?php
namespace Sportpat\Tabcontent\Model;

use Sportpat\Tabcontent\Api\Data\TabcontentInterface;
use Magento\Framework\Model\AbstractModel;
use Sportpat\Tabcontent\Model\ResourceModel\Tabcontent as TabcontentResourceModel;
use Magento\Framework\Data\Collection\AbstractDb as DbCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Option\ArrayInterface;

/**
 * @method \Sportpat\Tabcontent\Model\ResourceModel\Tabcontent _getResource()
 * @method \Sportpat\Tabcontent\Model\ResourceModel\Tabcontent getResource()
 */
class Tabcontent extends AbstractModel implements TabcontentInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'sportpat_tabcontent_tabcontent';
    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sportpat_tabcontent_tabcontent';
    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'tabcontent';
    /**
     * Uploader pool
     *
     * @var UploaderPool
     */
    protected $uploaderPool;
    /**
     * @var Output
     */
    protected $outputProcessor;
    /**
     * @var ArrayInterface[]
     */
    protected $optionProviders;
    /**
     * constructor
     * @param Context $context
     * @param Registry $registry
     * @param UploaderPool $uploaderPool
     * @param Output $outputProcessor,
     * @param array $optionProviders
     * @param AbstractResource $resource
     * @param DbCollection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        UploaderPool $uploaderPool,
        Output $outputProcessor,
        array $optionProviders = [],
        AbstractResource $resource = null,
        DbCollection $resourceCollection = null,
        array $data = []
    ) {
        $this->outputProcessor = $outputProcessor;
        $this->uploaderPool = $uploaderPool;
        $this->optionProviders = $optionProviders;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(TabcontentResourceModel::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Page id
     *
     * @return array
     */
    public function getTabcontentId()
    {
        return $this->getData(TabcontentInterface::TABCONTENT_ID);
    }

    /**
     * set Manage Content id
     *
     * @param  int $tabcontentId
     * @return TabcontentInterface
     */
    public function setTabcontentId($tabcontentId)
    {
        return $this->setData(TabcontentInterface::TABCONTENT_ID, $tabcontentId);
    }

    /**
     * @param string $title
     * @return TabcontentInterface
     */
    public function setTitle($title)
    {
        return $this->setData(TabcontentInterface::TITLE, $title);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(TabcontentInterface::TITLE);
    }

    /**
     * @param int $tabContenttype
     * @return TabcontentInterface
     */
    public function setTabContenttype($tabContenttype)
    {
        return $this->setData(TabcontentInterface::TAB_CONTENTTYPE, $tabContenttype);
    }

    /**
     * @return int
     */
    public function getTabContenttype()
    {
        return $this->getData(TabcontentInterface::TAB_CONTENTTYPE);
    }

    /**
     * @param string $contentHtml
     * @return TabcontentInterface
     */
    public function setContentHtml($contentHtml)
    {
        return $this->setData(TabcontentInterface::CONTENT_HTML, $contentHtml);
    }

    /**
     * @return string
     */
    public function getContentHtml()
    {
        return $this->getData(TabcontentInterface::CONTENT_HTML);
    }

    /**
     * @param string $image
     * @return TabcontentInterface
     */
    public function setImage($image)
    {
        return $this->setData(TabcontentInterface::IMAGE, $image);
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->getData(TabcontentInterface::IMAGE);
    }

    /**
     * @param int $forBrand
     * @return TabcontentInterface
     */
    public function setForBrand($forBrand)
    {
        return $this->setData(TabcontentInterface::FOR_BRAND, $forBrand);
    }

    /**
     * @return int
     */
    public function getForBrand()
    {
        return $this->getData(TabcontentInterface::FOR_BRAND);
    }

    /**
     * @param int $forCategory
     * @return TabcontentInterface
     */
    public function setForCategory($forCategory)
    {
        return $this->setData(TabcontentInterface::FOR_CATEGORY, $forCategory);
    }

    /**
     * @return int
     */
    public function getForCategory()
    {
        return $this->getData(TabcontentInterface::FOR_CATEGORY);
    }

    /**
     * @param string $useForSkus
     * @return TabcontentInterface
     */
    public function setUseForSkus($useForSkus)
    {
        return $this->setData(TabcontentInterface::USE_FOR_SKUS, $useForSkus);
    }

    /**
     * @return string
     */
    public function getUseForSkus()
    {
        return $this->getData(TabcontentInterface::USE_FOR_SKUS);
    }

    /**
     * @param string $forGender
     * @return TabcontentInterface
     */
    public function setForGender($forGender)
    {
        return $this->setData(TabcontentInterface::FOR_GENDER, $forGender);
    }

    /**
     * @return string
     */
    public function getForGender()
    {
        return $this->getData(TabcontentInterface::FOR_GENDER);
    }
    /**
     * @param array $storeId
     * @return TabcontentInterface
     */
    public function setStoreId(array $storeId)
    {
        return $this->setData(TabcontentInterface::STORE_ID, $storeId);
    }

    /**
    * @return int[]
    */
    public function getStoreId()
    {
        return $this->getData(TabcontentInterface::STORE_ID);
    }

    /**
     * @param int $isActive
     * @return TabcontentInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(TabcontentInterface::IS_ACTIVE, $isActive);
    }

    /**
     * @return int
     */
    public function getIsActive()
    {
        return $this->getData(TabcontentInterface::IS_ACTIVE);
    }

    /**
     * @return bool|string
     * @throws LocalizedException
     */
    public function getContentHtmlHtml()
    {
        return $this->outputProcessor->filterOutput($this->getContentHtml());
    }

    /**
     * @return bool|string
     * @throws LocalizedException
     */
    public function getImageUrl()
    {
        $url = false;
        $image = $this->getImage();
        if ($image) {
            if (is_string($image)) {
                $uploader = $this->uploaderPool->getUploader('image');
                $url = $uploader->getBaseUrl() . $uploader->getBasePath() . $image;
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the Image url.')
                );
            }
        }
        return $url;
    }
    /**
     * @return array
     */
    private function getMultiSelectFields()
    {
        return [
            'for_gender'
        ];
    }

    /**
     * @return AbstractModel|$this
     */
    public function beforeSave()
    {
        foreach ($this->getMultiSelectFields() as $field) {
            if (is_array($this->getData($field))) {
                $this->setData($field, implode(',', $this->getData($field)));
            }
        }
        return parent::beforeSave();
    }

    /**
     * @return AbstractModel|$this
     */
    public function afterLoad()
    {
        foreach ($this->getMultiSelectFields() as $field) {
            if (!is_array($this->getData($field))) {
                $this->setData($field, explode(',', $this->getData($field)));
            }
        }
        return parent::afterLoad();
    }
    /**
     * @param $attribute
     * @return string
     */
    public function getAttributeText($attribute)
    {
        if (!isset($this->optionProviders[$attribute])) {
            return '';
        }
        if (!($this->optionProviders[$attribute] instanceof ArrayInterface)) {
            return '';
        }
        $value = $this->getData($attribute);
        if (!is_array($value)) {
            $value = explode(',', $value);
        }
        $keyValuePair = array_filter(
            $this->optionProviders[$attribute]->toOptionArray(),
            function ($item) use ($value) {
                return in_array($item['value'], $value);
            }
        );
        return implode(
            ', ',
            array_map(
                function ($item) {
                    return $item['label'];
                },
                $keyValuePair
            )
        );
    }
}
