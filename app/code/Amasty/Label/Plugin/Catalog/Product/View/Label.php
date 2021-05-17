<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Plugin\Catalog\Product\View;

/**
 * Class Label
 * @package Amasty\Label\Plugin\Catalog\Product\View
 */
class Label
{
    /**
     * @var array
     */
    private $allowedNames = [
        'product.info.media.magiczoomplus',
        'product.info.media.image',
        'product.info.media.magicthumb.younify'
    ];

    /**
     * @var \Amasty\Label\Model\LabelViewer
     */
    private $helper;

    /**
     * Label constructor.
     * @param \Amasty\Label\Model\LabelViewer $helper
     */
    public function __construct(
        \Amasty\Label\Model\LabelViewer $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param $subject
     * @param $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(
        $subject,
        $result
    ) {
        $product = $subject->getProduct();
        $name = $subject->getNameInLayout();

        if ($product
            && in_array($name, $this->getAllowedNames())
            && !$subject->getAmlabelObserved()
        ) {
            $subject->setAmlabelObserved(true);
            $result .= $this->helper->renderProductLabel($product, 'product');
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedNames()
    {
        return $this->allowedNames;
    }
}
