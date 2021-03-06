<?php

namespace Sportpat\Tabcontent\Block;

use Magento\Framework\Model\AbstractModel;
use Sportpat\Tabcontent\Model\Uploader;
use Sportpat\Tabcontent\Helper\Image as ImageHelper;
use Sportpat\Tabcontent\Helper\ImageFactory as HelperFactory;

/**
 * @api
 */
class ImageBuilder
{
    /**
     * @var ImageFactory
     */
    private $imageFactory;
    /**
     * @var HelperFactory
     */
    private $helperFactory;
    /**
     * @var \Magento\Framework\Model\AbstractModel
     */
    private $entity;
    /**
     * @var string
     */
    private $imageId;
    /**
     * @var array
     */
    private $attributes = [];
    /**
     * @var string
     */
    private $entityCode;
    /**
     * @var Uploader
     */
    private $uploader;

    /**
     * @param HelperFactory $helperFactory
     * @param ImageFactory $imageFactory
     * @param Uploader $uploader
     * @param string $entityCode
     */
    public function __construct(
        HelperFactory $helperFactory,
        ImageFactory $imageFactory,
        Uploader $uploader,
        string $entityCode
    ) {
        $this->helperFactory = $helperFactory;
        $this->imageFactory  = $imageFactory;
        $this->uploader      = $uploader;
        $this->entityCode    = $entityCode;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $entity
     * @return $this
     */
    public function setEntity(AbstractModel $entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Set image ID
     *
     * @param string $imageId
     * @return $this
     */
    public function setImageId($imageId)
    {
        $this->imageId = $imageId;
        return $this;
    }

    /**
     * Set custom attributes
     *
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        if ($attributes) {
            $this->attributes = $attributes;
        }
        return $this;
    }

    /**
     * Retrieve image custom attributes for HTML element
     *
     * @return string
     */
    protected function getCustomAttributes()
    {
        $result = [];
        foreach ($this->attributes as $name => $value) {
            $result[] = $name . '="' . $value . '"';
        }
        return !empty($result) ? implode(' ', $result) : '';
    }

    /**
     * Calculate image ratio
     *
     * @param ImageHelper $helper
     * @return float|int
     */
    protected function getRatio(ImageHelper $helper)
    {
        $width = $helper->getWidth();
        $height = $helper->getHeight();
        if ($width && $height) {
            return $height / $width;
        }
        return 1;
    }

    /**
     * Create image block
     *
     * @return \Sportpat\Tabcontent\Block\Image
     */
    public function create()
    {
        /** @var ImageHelper $helper */
        $helper = $this->helperFactory
            ->create([
                'entityCode' => $this->entityCode,
                'uploader' => $this->uploader
            ])
            ->init(
                $this->entity,
                $this->imageId,
                $this->attributes
            );
        $template = $helper->getFrame()
            ? 'Sportpat_Tabcontent::image.phtml'
            : 'Sportpat_Tabcontent::image_with_borders.phtml';
        $imagesize = $helper->getResizedImageInfo();
        $data = [
            'data' => [
                'template'              => $template,
                'image_url'             => $helper->getUrl(),
                'width'                 => $helper->getWidth(),
                'height'                => $helper->getHeight(),
                'label'                 => $helper->getLabel(),
                'ratio'                 => $this->getRatio($helper),
                'custom_attributes'     => $this->getCustomAttributes(),
                'resized_image_width'   => !empty($imagesize[0]) ? $imagesize[0] : $helper->getWidth(),
                'resized_image_height'  => !empty($imagesize[1]) ? $imagesize[1] : $helper->getHeight(),
            ],
        ];
        return $this->imageFactory->create($data);
    }
}
