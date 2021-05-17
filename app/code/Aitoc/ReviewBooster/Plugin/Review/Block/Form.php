<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Plugin\Review\Block;

use Magento\Review\Block\Form as ReviewForm;

/**
 * Class Form
 */
class Form
{
    /**
     * Rewrite template
     *
     * @param ReviewForm $form
     */
    public function beforeToHtml(ReviewForm $form)
    {
        $form->setTemplate('Aitoc_ReviewBooster::rewrite/review/view/frontend/templates/form.phtml');
    }
}
