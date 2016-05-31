<?php
/**
 * Interface for DateRange objects
 */
namespace Gubler\DateRange;

/**
 * Generate Date range arrays based on start and end dates
 *
 * @package Gubler\DateRange
 */
interface DateRangeInterface
{
    /**
     * Set the return timezone
     *
     * @param \DateTimeZone $timeZone
     * @return DateRangeInterface
     */
    public function setReturnTimezone(\DateTimeZone $timeZone);

    /**
     * Set the start date for the range
     *
     * @param \DateTimeInterface $start
     * @return DateRangeInterface
     */
    public function setStart(\DateTimeInterface $start);

    /**
     * Set the end date for the range
     *
     * @param \DateTimeInterface $end
     * @return DateRangeInterface
     */
    public function setEnd(\DateTimeInterface $end);

    /**
     * Returns start DateTime as a either a DateTime object or a formatted string
     *
     * @param string|null $format date() compatible format
     * @return string|\DateTime
     **/
    public function getStart($format = null);

    /**
     * Returns end DateTime as a either a DateTime object or a formatted string
     *
     * @param string|null $format date() compatible format
     * @return string|\DateTime
     **/
    public function getEnd($format = null);

    /**
     * Returns array of years in date range
     *
     * @param string $format date() compatible format
     *
     * @return array
     */
    public function getYears($format = 'Y');

    /**
     * Returns array of months in date range
     *
     * @param string $format date() compatible format
     *
     * @return array
     */
    public function getMonths($format = 'Y-m');

    /**
     * Returns array of days in date range
     *
     * @param string $format date() compatible format
     *
     * @return array
     */
    public function getDays($format = 'Y-m-d');

    /**
     * Returns an array of DateRange objects for the current range split by month
     *
     * @return array
     */
    public function splitByMonth();

    /**
     * Return number of seconds between start and end
     *
     * @return float
     */
    public function asSeconds();

    /**
     * Return number of complete minutes between start and end
     *
     * @return float
     */
    public function asMinutes();

    /**
     * Return number of full hours between start and end
     *
     * @return float
     */
    public function asHours();

    /**
     * Return number of full days between start and end
     *
     * @return float
     */
    public function asDays();

    /**
     * Returns a DateTime of the numbered day of range of DateRange.
     * If $day is a negative number, it will count backwards from the end of the DateRange.
     * Example (2015-01-01 to 2015-01-31):
     * * 1 = first day of range: 2015-01-01
     * * 10 = tenth day of range: 2015-01-10
     * * -1 = last day of range: 2015-01-31
     * * -5 = fifth to last day: 2015-01-27
     *
     * @param int $day
     * @return \DateTime
     * @throws \Exception if $day falls outside of range
     */
    public function getDayOfRange($day);

    /**
     * Returns a DateTime of the first, second, last, etc. day of week in range.
     *
     * $weekday is 1-7 (same as PHP's date('w'), 0 = Sunday, 1 = Monday, etc.)
     * If $week is a negative number, it will count backwards from the end of the DateRange.
     *
     * @param int $week
     * @param int $weekday
     * @return \DateTime
     * @throws \Exception if $day falls outside of range
     */
    public function getWeekdayOfRange($week, $weekday);

    /**
     * Find the number of times the Month-Day values in $dates occur in the Range
     * ex. if the range is 2014-01-01 to 2016-12-01 and array('04-21','06-12', '12-31') is supplied
     * this function returns 8 (3 for each of the days except for 12-31, which occurs only twice).
     *
     * @param array $dates Array of 'Month-Day'
     * @return int
     * @throws \Exception if array of month-days not supplied
     */
    public function numberOfDatesOccurring($dates);
}