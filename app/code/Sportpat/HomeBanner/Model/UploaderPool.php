<?php
namespace Sportpat\HomeBanner\Model;

use Sportpat\HomeBanner\Model\Uploader;

class UploaderPool
{
    /**
     * Available Uploaders
     *
     * @var array
     */
    protected $uploaders = [];

    /**
     * constructor
     *
     * @param array $uploaders
     */
    public function __construct(
        array $uploaders = []
    ) {
        $this->uploaders = $uploaders;
    }

    /**
     * @param string $type
     * @return Uploader
     * @throws \Exception
     */
    public function getUploader($type)
    {
        if (!isset($this->uploaders[$type])) {
            throw new \Exception("Uploader not found for type: " . $type);
        }
        $uploader = $this->uploaders[$type];
        if (!($uploader instanceof Uploader)) {
            throw new \Exception("Uploader for type {$type} not instance of " . Uploader::class);
        }
        return $uploader;
    }
}
