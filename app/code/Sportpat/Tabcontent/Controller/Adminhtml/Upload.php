<?php
namespace Sportpat\Tabcontent\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Sportpat\Tabcontent\Model\Uploader;

class Upload extends Action
{
    /**
     * Uploader model
     *
     * @var Uploader
     */
    protected $uploader;
    /**
     * @var string
     */
    protected $aclResource;

    /**
     * Upload constructor.
     * @param Context $context
     * @param Uploader $uploader
     * @param string $aclResource
     */
    public function __construct(
        Context $context,
        Uploader $uploader,
        $aclResource
    ) {
        $this->uploader = $uploader;
        $this->aclResource = $aclResource;
        parent::__construct($context);
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        try {
            $uploadResult = $this->uploader->saveFileToTmpDir($this->getRequest()->getParam('field'));

            $uploadResult['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $uploadResult = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setData($uploadResult);
        return $result;
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed($this->aclResource);
    }
}
