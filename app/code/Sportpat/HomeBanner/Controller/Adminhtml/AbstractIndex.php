<?php
namespace Sportpat\HomeBanner\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

abstract class AbstractIndex extends Action
{
    /**
     * @var string
     */
    private $pageTitle;
    /**
     * @var string
     */
    protected $activeMenuItem;

    /**
     * Index constructor.
     * @param Context $context
     * @param string $activeMenuItem
     * @param string $pageTitle
     */
    public function __construct(Context $context, $activeMenuItem = '', $pageTitle = '')
    {
        parent::__construct($context);
        $this->activeMenuItem = $activeMenuItem;
        $this->pageTitle = $pageTitle;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($this->activeMenuItem) {
            $resultPage->setActiveMenu($this->activeMenuItem);
        }
        $resultPage->getConfig()->getTitle()->prepend($this->pageTitle);
        return $resultPage;
    }
}
