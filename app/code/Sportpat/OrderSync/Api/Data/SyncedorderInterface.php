<?php
namespace Sportpat\OrderSync\Api\Data;

/**
 * @api
 */
interface SyncedorderInterface
{
    const SYNCEDORDER_ID = 'syncedorder_id';
    const MAGENTO_ORDERID = 'magento_orderid';
    const LIGHTSPEED_ORDERID = 'lightspeed_orderid';
    const STATUS = 'status';
    const DETAILS = 'details';
    /**
     * @param int $id
     * @return SyncedorderInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return SyncedorderInterface
     */
    public function setSyncedorderId($id);

    /**
     * @return int
     */
    public function getSyncedorderId();

    /**
     * @param int $magentoOrderid
     * @return SyncedorderInterface
     */
    public function setMagentoOrderid($magentoOrderid);

    /**
     * @return int
     */
    public function getMagentoOrderid();
    /**
     * @param int $lightspeedOrderid
     * @return SyncedorderInterface
     */
    public function setLightspeedOrderid($lightspeedOrderid);

    /**
     * @return int
     */
    public function getLightspeedOrderid();
    /**
     * @param int $status
     * @return SyncedorderInterface
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getStatus();
    /**
     * @param string $details
     * @return SyncedorderInterface
     */
    public function setDetails($details);

    /**
     * @return string
     */
    public function getDetails();
}
