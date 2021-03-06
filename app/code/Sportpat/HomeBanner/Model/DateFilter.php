<?php
namespace Sportpat\HomeBanner\Model;

use Magento\Framework\Stdlib\DateTime\Filter\Date;

class DateFilter
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    private $dateFilter;

    /**
     * DataFilter constructor.
     * @param Date $dateFilter
     */
    public function __construct(Date $dateFilter)
    {
        $this->dateFilter = $dateFilter;
    }

    /**
     * @param array $data
     * @param array $dateFields
     * @return array
     */
    public function filterDates(array $data, array $dateFields)
    {
        $rules = [];
        foreach ($dateFields as $dateField) {
            if (!empty($data[$dateField])) {
                $rules[$dateField] = $this->dateFilter;
            }
        }
        $inputFilter = new \Zend_Filter_Input($rules, [], $data);
        return $inputFilter->getUnescaped();
    }
}
