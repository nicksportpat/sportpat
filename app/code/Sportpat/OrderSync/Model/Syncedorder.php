<?php
namespace Sportpat\OrderSync\Model;

use Sportpat\OrderSync\Api\Data\SyncedorderInterface;
use Magento\Framework\Model\AbstractModel;
use Sportpat\OrderSync\Model\ResourceModel\Syncedorder as SyncedorderResourceModel;

/**
 * @method \Sportpat\OrderSync\Model\ResourceModel\Syncedorder _getResource()
 * @method \Sportpat\OrderSync\Model\ResourceModel\Syncedorder getResource()
 */
class Syncedorder extends AbstractModel implements SyncedorderInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'sportpat_ordersync_syncedorder';
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
    protected $_eventPrefix = 'sportpat_ordersync_syncedorder';
    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'syncedorder';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SyncedorderResourceModel::class);
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
    public function getSyncedorderId()
    {
        return $this->getData(SyncedorderInterface::SYNCEDORDER_ID);
    }

    /**
     * set Synced Order id
     *
     * @param  int $syncedorderId
     * @return SyncedorderInterface
     */
    public function setSyncedorderId($syncedorderId)
    {
        return $this->setData(SyncedorderInterface::SYNCEDORDER_ID, $syncedorderId);
    }

    /**
     * @param int $magentoOrderid
     * @return SyncedorderInterface
     */
    public function setMagentoOrderid($magentoOrderid)
    {
        return $this->setData(SyncedorderInterface::MAGENTO_ORDERID, $magentoOrderid);
    }

    /**
     * @return int
     */
    public function getMagentoOrderid()
    {
        return $this->getData(SyncedorderInterface::MAGENTO_ORDERID);
    }

    /**
     * @param int $lightspeedOrderid
     * @return SyncedorderInterface
     */
    public function setLightspeedOrderid($lightspeedOrderid)
    {
        return $this->setData(SyncedorderInterface::LIGHTSPEED_ORDERID, $lightspeedOrderid);
    }

    /**
     * @return int
     */
    public function getLightspeedOrderid()
    {
        return $this->getData(SyncedorderInterface::LIGHTSPEED_ORDERID);
    }

    /**
     * @param int $status
     * @return SyncedorderInterface
     */
    public function setStatus($status)
    {
        return $this->setData(SyncedorderInterface::STATUS, $status);
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->getData(SyncedorderInterface::STATUS);
    }

    /**
     * @param string $details
     * @return SyncedorderInterface
     */
    public function setDetails($details)
    {
        return $this->setData(SyncedorderInterface::DETAILS, $details);
    }

    /**
     * @return string
     */
    public function getDetails()
    {
        return $this->getData(SyncedorderInterface::DETAILS);
    }


}
