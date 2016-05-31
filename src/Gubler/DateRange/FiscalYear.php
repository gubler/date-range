<?php
/**
 * A simple class to calculate Fiscal Years based off of dates
 *
 * Fiscal years are calculated off of the end date (so a fiscal year ending on June 1, 2015 is Fiscal Year 2015)
 *
 * This class also only handles fiscal years that map to a static calendar dates. It does not handle fiscal years
 * that end on variable day (such as the last friday of the year or similar).
 *
 * This class also does not handle historical changes of fiscal years, such as if your fiscal year changed from ending
 * on March 15 to ending on April 1 at some point in the past.
 *
 * @author Daryl Gubler <daryl@dev88.co>
 */

namespace Gubler\DateRange;

/**
 * Fiscal Year class
 *
 * @package Gubler\DateRange
 */
class FiscalYear
{
    /** @var integer */
    protected $fiscalYearStartMonth;
    /** @var integer */
    protected $fiscalYearStartDay;
    /** @var integer */
    protected $fiscalYearEndMonth;
    /** @var integer */
    protected $fiscalYearEndDay;

    /**
     * FiscalYear constructor. End Month and day will be calculated as one day before the provided start month and day
     *
     * @param int  $fiscalYearStartMonth Calendar month fiscal year starts (1 = Jan, 2 = Feb, etc.)
     * @param int  $fiscalYearStartDay   Day of month fiscal year starts
     */
    public function __construct($fiscalYearStartMonth, $fiscalYearStartDay)
    {
        $this->fiscalYearStartMonth = $fiscalYearStartMonth;
        $this->fiscalYearStartDay = $fiscalYearStartDay;
        $fyEnd = new \DateTime('2000-'.$fiscalYearStartMonth.'-'.$fiscalYearStartDay.' 00:00:00');
        $fyEnd->sub(new \DateInterval('P1D'));
        $this->fiscalYearEndMonth = $fyEnd->format('n');
        $this->fiscalYearEndDay = $fyEnd->format('j');
    }

    /**
     * Convert DateTime to Fiscal Year
     *
     * @param \DateTime $date      Date to convert
     * @param int       $numDigits Return 2 or 4 digit year
     * @param string    $prefix    String to prefix (example: FY)
     *
     * @return string
     */
    public function dateToFyString(\DateTime $date, $numDigits = 4, $prefix = null)
    {
        $fiscalYear = $this->getFiscalYear($date);

        $fiscalYear = ($numDigits == 2) ? substr($fiscalYear, 2) : $fiscalYear;

        return $prefix.$fiscalYear;
    }

    /**
     * Date to FY Range
     * @param \DateTimeInterface $dateTime
     * @return DateRange
     */
    public function dateToFyDateRange(\DateTimeInterface $dateTime)
    {
        $fiscalYear = $this->getFiscalYear($dateTime);

        $end = (new \DateTime())
            ->setDate($fiscalYear, $this->fiscalYearEndMonth, $this->fiscalYearEndDay)
            ->setTime(23, 59, 59);
        $start = clone $end;
        $start->sub(new \DateInterval('P1Y'))->add(new \DateInterval('PT1S'));

        return new DateRange($start, $end);
    }

    /**
     * Converts 4-digit fiscal year to a DateRange
     *
     * @param int $fiscalYear 4-digit fiscal year to convert
     * @return DateRange
     */
    public function yearToFyDateRange($fiscalYear)
    {
        $end = (new \DateTime())
            ->setDate($fiscalYear, $this->fiscalYearEndMonth, $this->fiscalYearEndDay)
            ->setTime(23, 59, 59);
        $start = clone $end;
        $start->sub(new \DateInterval('P1Y'))->add(new \DateInterval('PT1S'));

        return new DateRange($start, $end);
    }

    /**
     * Checks if DateTime is in FY
     *
     * @param \DateTime $date       DateTime to check
     * @param int       $fiscalYear 4-digit fiscal year
     * @return bool
     */
    public function dateInFy(\DateTime $date, $fiscalYear)
    {
        $dateRange = $this->yearToFyDateRange($fiscalYear);

        return ($date >= $dateRange->getStart() && $date <= $dateRange->getEnd());
    }

    /**
     * Get Fiscal Year for date
     *
     * @param \DateTimeInterface $date
     * @return int
     */
    private function getFiscalYear(\DateTimeInterface $date)
    {
        $fiscalYear = (int) $date->format('Y');

        $endOfFiscalYear = (new \DateTime())
            ->setDate($date->format('Y'), $this->fiscalYearEndMonth, $this->fiscalYearEndDay)
            ->setTime(23, 59, 59);


        return ($date < $endOfFiscalYear) ? $fiscalYear : $fiscalYear + 1;
    }
}
