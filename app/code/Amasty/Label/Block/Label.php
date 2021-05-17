<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Block;

class Label extends \Magento\Framework\View\Element\Template implements \Magento\Framework\DataObject\IdentityInterface
{
    const DISPLAY_PRODUCT  = 'display/product';
    const DISPLAY_CATEGORY = 'display/category';

    protected $_template = 'Amasty_Label::label.phtml';

    /**
     * @var \Amasty\Label\Helper\Config
     */
    private $helper;

    /**
     * @var \Amasty\Label\Model\Labels
     */
    private $label;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var int
     */
    private $themeId;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Label\Helper\Config $helper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->jsonEncoder = $jsonEncoder;
        $this->label = $data['label'] ?? null;
        $this->storeId = $this->_storeManager->getStore()->getId();
        $this->themeId = $this->_design->getDesignTheme()->getId();
        $this->addData([
            'cache_lifetime' => 86400
        ]);
    }

    /**
     * @return string
     */
    public function getJsonConfig()
    {
        $label = $this->getLabel();
        $productId = $label->getParentProduct()
            ? $label->getParentProduct()->getId()
            : $label->getProduct()->getId();

        return $this->jsonEncoder->encode(
            [
                'position' => $label->getCssClass(),
                'size' => $label->getValue('image_size'),
                'path' => $this->getContainerPath(),
                'mode' => $label->getMode(),
                'move' => (int)$label->getShouldMove(),
                'product' => $productId,
                'label' => (int)$label->getId(),
                'margin' => $this->helper->getModuleConfig('display/margin_between'),
                'alignment' => $this->helper->getModuleConfig('display/labels_alignment')
            ]
        );
    }

    /**
     * @return boolean|int
     */
    public function isAdminArea()
    {
        return $this->getArea() == 'adminhtml';
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $productId = null;

        if ($this->getLabel()->getParentProduct()) {
            $productId = $this->getLabel()->getParentProduct()->getId();
        } elseif ($this->getLabel()->getProduct()) {
            $productId = $this->getLabel()->getProduct()->getId();
        }

        return [
            $this->storeId,
            $this->themeId,
            $this->getLabel()->getId(),
            $this->getLabel()->getMode(),
            $productId
        ];
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return $this->getLabel()->getIdentities();
    }

    /**
     * @param \Amasty\Label\Model\Labels $label
     *
     * @return $this
     */
    public function setLabel(\Amasty\Label\Model\Labels $label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return \Amasty\Label\Model\Labels
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get container path from module settings
     *
     * @return string
     */
    public function getContainerPath()
    {
        if ($this->label->getMode() == 'cat') {
            $path = $this->helper->getModuleConfig(self::DISPLAY_CATEGORY);
        } else {
            $path = $this->helper->getModuleConfig(self::DISPLAY_PRODUCT);
        }

        return $path;
    }

    /**
     * Get image url with mode and site url
     *
     * @return string
     */
    public function getImageSrc()
    {
        $img = $this->label->getValue('img');

        return $this->helper->getImageUrl($img);
    }
}
