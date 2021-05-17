<?php
namespace Sportpat\Sync\Model;

interface SyncInterface {
    public function syncProducts($syncType, $output);
}
