<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Model;

use Aitoc\FollowUpEmails\Api\Data\EventAttributeInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\Event\StatusInterface;
use Aitoc\FollowUpEmails\Api\EventManagementInterface;
use Aitoc\FollowUpEmails\Api\Helper\EventEmailsGeneratorHelperInterface;
use Magento\Framework\Filesystem\Driver\File as FileReader;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use Magento\Framework\Module\ModuleList;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Xml\Parser as XmlParser;

/**
 * Class EventManagement
 */
class EventManagement implements EventManagementInterface
{
    const XML_FILE_NAME = 'event_attributes.xml';
    const PACKAGE_NAME = 'Aitoc';

    /**
     * @var ModuleDirReader
     */
    private $moduleDirReader;

    /**
     * Filesystem driver to allow reading of module.xml files which live outside of app/code
     *
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var XmlParser
     */
    private $xmlParser;

    /**
     * @var array[]
     */
    private $attributes = [];

    /**
     * Module info
     *
     * @var ModuleList
     */
    private $moduleList;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * EventManagement constructor.
     * @param ModuleDirReader $moduleDirReader
     * @param XmlParser $xmlParser
     * @param FileReader $fileReader
     * @param ModuleList $moduleList
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ModuleDirReader $moduleDirReader,
        XmlParser $xmlParser,
        FileReader $fileReader,
        ModuleList $moduleList,
        ObjectManagerInterface $objectManager
    ) {
        $this->moduleDirReader = $moduleDirReader;
        $this->xmlParser = $xmlParser;
        $this->fileReader = $fileReader;
        $this->moduleList = $moduleList;
        $this->objectManager = $objectManager;
        $this->init();
    }

    private function init()
    {
        $this->attributes = $this->getEventsAttributes();
    }

    /**
     * @return array[]
     */
    protected function getEventsAttributes()
    {
        $preparedFollowUpEmailsEventsAttributes = $this->getPreparedFollowUpEmailsEventsAttributes();
        $submodulesEventsAttributes = $this->getSubmodulesEventsAttributes();

        return $this->getResultEventsAttributes($preparedFollowUpEmailsEventsAttributes, $submodulesEventsAttributes);
    }

    /**
     * @return array
     */
    protected function getPreparedFollowUpEmailsEventsAttributes()
    {
        $eventsAttributes = $this->getFollowUpEmailsEventsAttributes();
        $eventsAttributesWithStatusInactive = $this->addStatusInactive($eventsAttributes);

        return $this->getArrayWithFieldAsKey($eventsAttributesWithStatusInactive, EventAttributeInterface::CODE);
    }

    /**
     * @return array|null
     */
    protected function getFollowUpEmailsEventsAttributes()
    {
        $followUpModuleName = $this->getCurrentModuleName();

        return $this->getModuleEventsAttributes($followUpModuleName);
    }

    /**
     * @return string
     */
    protected function getCurrentModuleName()
    {
        return 'Aitoc_FollowUpEmails';
        //todo: implement dynamically like in abstract helper
    }

    /**
     * @param string $moduleName
     * @return array|null
     */
    protected function getModuleEventsAttributes($moduleName)
    {
        $xmlArray = $this->getModuleEventsAttributesXmlArray($moduleName);

        if (!$xmlArray) {
            return null;
        }

        return $this->convertXmlArrayToEventsAttributesArray($xmlArray);
    }

    /**
     * @param string $moduleName
     * @return array|bool
     */
    protected function getModuleEventsAttributesXmlArray($moduleName)
    {
        $filename = $this->getModuleEventsAttributesFilename($moduleName);

        if (!file_exists($filename)) {
            return false;
        }

        return $this->getXmlFileAsArray($filename);
    }

    /**
     * @param string $moduleName
     * @return string
     */
    protected function getModuleEventsAttributesFilename($moduleName)
    {
        $moduleEtcDirectory = $this->getModuleEtcDir($moduleName);

        return $moduleEtcDirectory . DIRECTORY_SEPARATOR . self::XML_FILE_NAME;
    }

    /**
     * @param string $moduleName
     * @return string
     */
    protected function getModuleEtcDir($moduleName)
    {
        return $this->moduleDirReader->getModuleDir('etc', $moduleName);
    }

    /**
     * @param string $filename
     * @return array
     */
    protected function getXmlFileAsArray($filename)
    {
        return $this->xmlParser->load($filename)->xmlToArray();
    }

    /**
     * @param array $xmlArray
     * @return array
     */
    protected function convertXmlArrayToEventsAttributesArray($xmlArray)
    {
        $events = reset($xmlArray['config']['_value']['events']);

        if (!array_key_exists(EventAttributeInterface::CODE, $events)) {
            return $events;
        }

        return [$events];
    }

    /**
     * @param array $followUpEmailsEventsAttributes
     * @return array
     */
    protected function addStatusInactive($followUpEmailsEventsAttributes)
    {
        $ret = [];

        foreach ($followUpEmailsEventsAttributes as $followUpEmailsEventAttribute) {
            $followUpEmailsEventAttribute[EventAttributeInterface::STATUS] = StatusInterface::INACTIVE;
            $ret[] = $followUpEmailsEventAttribute;
        }

        return $ret;
    }

    /**
     * @param array $array
     * @param string $field
     * @return array
     */
    protected function getArrayWithFieldAsKey($array, $field)
    {
        $ret = [];

        foreach ($array as $item) {
            $filedValue = $item[$field];
            $ret[$filedValue] = $item;
        }

        return $ret;
    }

    /**
     * @return array
     */
    protected function getSubmodulesEventsAttributes()
    {
        $possibleSubmodulesNames = $this->getPossibleSubmodulesNames();

        $ret = [];

        foreach ($possibleSubmodulesNames as $possibleSubmoduleName) {
            $possibleSubmoduleEventsAttributes = $this->getModuleEventsAttributes($possibleSubmoduleName);

            if ($possibleSubmoduleEventsAttributes) {
                $ret[$possibleSubmoduleName] = $possibleSubmoduleEventsAttributes;
            }
        }

        return $ret;
    }

    /**
     * @return array
     */
    protected function getPossibleSubmodulesNames()
    {
        $possibleSubmodulesNames = $this->getAitocModules();
        $followUpModuleName = $this->getCurrentModuleName();

        return $this->removeFromArrayByValue($possibleSubmodulesNames, $followUpModuleName);
    }

    /**
     * @return array
     */
    private function getAitocModules()
    {
        $aitocModules = [];
        $enabledModules = $this->moduleList->getNames();
        for ($i = 0; $i < count($enabledModules); $i++) {
            if (strpos($enabledModules[$i], self::PACKAGE_NAME) !== false) {
                $aitocModules[] = $enabledModules[$i];
            }
        }

        return $aitocModules;
    }

    /**
     * @param array $array
     * @param mixed $value
     * @return array
     */
    protected function removeFromArrayByValue($array, $value)
    {
        if (($key = array_search($value, $array)) !== false) {
            unset($array[$key]);
        }

        return $array;
    }

    /**
     * @param array $followUpEmailsEventsAttributes
     * @param array $submodulesEventsAttributes
     * @return array[]
     */
    protected function getResultEventsAttributes($followUpEmailsEventsAttributes, $submodulesEventsAttributes)
    {
        $attributes = $followUpEmailsEventsAttributes;

        foreach ($submodulesEventsAttributes as $submoduleEventsAttributes) {
            $attributes = $this->getResultModuleEventsAttributes($attributes, $submoduleEventsAttributes);
        }

        return $attributes;
    }

    /**
     * @param array $attributes
     * @param array $submoduleEventsAttributes
     * @return mixed
     */
    protected function getResultModuleEventsAttributes($attributes, $submoduleEventsAttributes)
    {
        foreach ($submoduleEventsAttributes as $submoduleEventAttribute) {
            $attributes = $this->getResultModuleEventAttribute($attributes, $submoduleEventAttribute);
        }

        return $attributes;
    }

    /**
     * @param array $attributes
     * @param array $submoduleEventAttribute
     * @return array
     */
    protected function getResultModuleEventAttribute($attributes, $submoduleEventAttribute)
    {
        $eventCode = $submoduleEventAttribute[EventAttributeInterface::CODE];
        $fuEventAttributes = $attributes[$eventCode];
        $fuEventAttributes[EventAttributeInterface::STATUS] = StatusInterface::ACTIVE;
        $resultEventAttributes = array_replace($fuEventAttributes, $submoduleEventAttribute);
        $attributes[$eventCode] = $resultEventAttributes;

        return $attributes;
    }

    /**
     * @param string $eventCode
     * @return array|null
     */
    public function getAttributesByEventCode($eventCode)
    {
        return $this->attributes[$eventCode];
    }

    /**
     * @return array
     */
    public function getAllEvents()
    {
        return $this->attributes;
    }

    /**
     * @return array
     */
    public function getActiveEventsCodes()
    {
        $activeEventsCodes = [];

        foreach ($this->attributes as $attribute) {
            if ($attribute['status'] == StatusInterface::ACTIVE) {
                $activeEventsCodes[] = $attribute['code'];
            }
        }

        return $activeEventsCodes;
    }

    /**
     * @return array
     */
    public function getActiveEvents()
    {
        $activeEvents = [];

        foreach ($this->attributes as $attribute) {
            if ($attribute['status'] == StatusInterface::ACTIVE) {
                $activeEvents[] = $attribute;
            }
        }

        usort($activeEvents, [$this, 'compareByName']);

        return $activeEvents;
    }

    /**
     * @param array $event1
     * @param array $event2
     * @return int
     */
    private function compareByName($event1, $event2)
    {
        $eventName1 = $event1[EventAttributeInterface::NAME];
        $eventName2 = $event2[EventAttributeInterface::NAME];

        return strcmp($eventName1, $eventName2);
    }

    /**
     * @param string $eventCode
     * @return string
     */
    public function getEventNameByCode($eventCode)
    {
        if (!array_key_exists($eventCode, $this->attributes)) {
            return null;
        }

        return $this->attributes[$eventCode]['name'];
    }

    /**
     * @param string $eventCode
     * @return EventEmailsGeneratorHelperInterface
     */
    public function getEventEmailGeneratorHelperByEventCode($eventCode)
    {
        if (!array_key_exists($eventCode, $this->attributes)) {
            return null;
        }

        if (empty($this->attributes[$eventCode]['class'])) {
            return null;
        }

        $className = $this->attributes[$eventCode]['class'];
        $object = $this->createObject($className);

        return $object;
    }

    /**
     * @param string $className
     * @return mixed
     */
    private function createObject($className)
    {
        return $this->objectManager->create($className);
    }

    /**
     * @param string $eventCode
     * @return bool
     */
    public function isEventEnabled($eventCode)
    {
        if (!array_key_exists($eventCode, $this->attributes)) {
            return false;
        }

        return $this->attributes[$eventCode]['status'] == StatusInterface::ACTIVE;
    }

    /**
     * @param array $eventsAttributes
     */
    protected function addEventsAttributes($eventsAttributes)
    {
        foreach ($eventsAttributes as $eventAttributes) {
            $this->addEventAttributes($eventAttributes);
        }
    }

    /**
     * @param array $eventAttributes
     */
    protected function addEventAttributes($eventAttributes)
    {
        $eventCode = $eventAttributes[EventAttributeInterface::CODE];

        $this->attributes[$eventCode] = $eventAttributes;
    }

}
