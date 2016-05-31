<?php
/**
 * Date Range
 *
 * @author Daryl Gubler <daryl@dev88.co>
 */

namespace Gubler\DateRange;

/**
 * Generate Date range arrays based on start and end dates
 *
 * @package Gubler\DateRange
 */
class DateRange implements DateRangeInterface
{
    /**
     * Start of DateRange as UTC
     * @var \DateTimeImmutable
     */
    protected $start;

    /**
     * Start TimeZone
     * @var \DateTimeZone
     */
    protected $startTimezone;

    /**
     * End of DateRange as UTC
     * @var \DateTimeImmutable
     */
    protected $end;

    /**
     * End TimeZone
     * @var \DateTimeZone
     */
    protected $endTimezone;

    /**
     * Converts parameters to DateTimeImmutable in UTC and stores TimeZones
     *
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     * @param \DateTimeZone|null $returnTimezone Timezone to return results in. Defaults to same timezone as $start.
     */
    public function __construct(\DateTimeInterface $start, \DateTimeInterface $end, \DateTimeZone $returnTimezone = null)
    {
        $this->startTimezone = $start->getTimezone();
        $this->start = (new \DateTimeImmutable($start->format('c')))->setTimezone(new \DateTimeZone('UTC'));
        $this->endTimezone = $end->getTimezone();
        $this->end = (new \DateTimeImmutable($end->format('c')))->setTimezone(new \DateTimeZone('UTC'));
        $this->returnTimezone = ($returnTimezone) ? $returnTimezone : $this->startTimezone;
    }

    /**
     * Set the return timezone
     *
     * @param \DateTimeZone $timeZone
     * @return DateRange
     */
    public function setReturnTimezone(\DateTimeZone $timeZone)
    {
        $this->returnTimezone = $timeZone;

        return $this;
    }

    /**
     * Set the start date for the range
     *
     * @param \DateTimeInterface $start
     * @return DateRange
     */
    public function setStart(\DateTimeInterface $start)
    {
        $this->startTimezone = $start->getTimezone();
        $this->start = (new \DateTimeImmutable($start->format('c')))->setTimezone(new \DateTimeZone('UTC'));

        return $this;
    }

    /**
     * Set the end date for the range
     *
     * @param \DateTimeInterface $end
     * @return DateRange
     */
    public function setEnd(\DateTimeInterface $end)
    {
        $this->endTimezone = $end->getTimezone();
        $this->end = (new \DateTimeImmutable($end->format('c')))->setTimezone(new \DateTimeZone('UTC'));

        return $this;
    }

    /**
     * Returns start DateTime as a either a DateTime object or a formatted string
     *
     * @param string|null $format date() compatible format
     * @return string|\DateTime
     **/
    public function getStart($format = null)
    {
        if ($format === null) {
            return new \DateTime($this->start->setTimezone($this->returnTimezone)->format('c'));
        }

        return $this->start->setTimezone($this->returnTimezone)->format($format);
    }

    /**
     * Returns end DateTime as a either a DateTime object or a formatted string
     *
     * @param string|null $format date() compatible format
     * @return string|\DateTime
     **/
    public function getEnd($format = null)
    {
        if ($format === null) {
            return new \DateTime($this->end->setTimezone($this->returnTimezone)->format('c'));
        }

        return $this->end->setTimezone($this->returnTimezone)->format($format);
    }

    /**
     * Returns array of years in date range
     *
     * @param string $format date() compatible format
     *
     * @return array
     */
    public function getYears($format = 'Y')
    {
        $start = $this->getStart('Y');
        $end = $this->getEnd('Y');

        $return = array();

        while ($start <= $end) {
            $year = new \DateTime($start.'-01-01 00:00:00');
            $return[] = $year->format($format);
            $start++;
        }

        return $return;
    }

    /**
     * Returns array of months in date range
     *
     * @param string $format date() compatible format
     *
     * @return array
     */
    public function getMonths($format = 'Y-m')
    {
        $start = new \DateTime($this->getStart('Y-m-01 00:00:00'));
        $end = new \DateTime($this->getEnd('Y-m-01 00:00:00'));

        $return = array();

        while ($start <= $end) {
            $return[] = $start->format($format);
            $start->add(new \DateInterval('P1M'));
        }

        return $return;
    }

    /**
     * Returns array of days in date range
     *
     * @param string $format date() compatible format
     *
     * @return array
     */
    public function getDays($format = 'Y-m-d')
    {
        $start = new \DateTime($this->getStart('Y-m-d 00:00:00'));
        $end = new \DateTime($this->getEnd('Y-m-d 00:00:00'));

        $return = array();

        while ($start <= $end) {
            $return[] = $start->format($format);
            $start->add(new \DateInterval('P1D'));
        }

        return $return;
    }

    /**
     * Returns an array of DateRange objects for the current range split by month
     *
     * @return array
     */
    public function splitByMonth()
    {
        if ($this->getStart('Y-m') == $this->getEnd('Y-m')) {
            return array($this);
        }
        $return = array();
        $months = $this->getMonths();
        $firstMonth = array_shift($months);
        $lastMonth = array_pop($months);

        $firstMonthEnd = new \DateTime($firstMonth);
        $firstMonthEnd->modify('last day of')->setTime(23, 59, 59);
        $lastMonthStart = new \DateTime($lastMonth);

        $return[] = new DateRange($this->start->setTimezone($this->returnTimezone), $firstMonthEnd);

        foreach ($months as $month) {
            $start = new \DateTime($month);
            $end = new \DateTime($month);
            $end->modify('last day of')->setTime(23, 59, 59);
            $return[] = new DateRange($start, $end);
        }

        $return[] = new DateRange($lastMonthStart, $this->end->setTimezone($this->returnTimezone));

        return $return;
    }

    /**
     * Return number of seconds between start and end
     *
     * @return float
     */
    public function asSeconds()
    {
        $seconds = $this->getEnd('U') - $this->getStart('U');

        return (float) $seconds;
    }

    /**
     * Return number of complete minutes between start and end
     *
     * @return float
     */
    public function asMinutes()
    {
        $seconds = $this->getEnd('U') - $this->getStart('U');

        return floor($seconds / 60.0);
    }

    /**
     * Return number of full hours between start and end
     *
     * @return float
     */
    public function asHours()
    {
        $seconds = $this->getEnd('U') - $this->getStart('U');

        return floor($seconds / 3600.0);
    }

    /**
     * Return number of full days between start and end
     *
     * @return float
     */
    public function asDays()
    {
        $seconds = $this->getEnd('U') - $this->getStart('U');

        return floor($seconds / 86400.0);
    }

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
    public function getDayOfRange($day)
    {
        $return = $this->start->setTimezone($this->returnTimezone);

        if ($day > 1) {
            // subtract 1 from the day because 1 should be the first day of the range, not 1 day after start of range
            $daysToAdd = new \DateInterval('P'.($day - 1).'D');
            $return = $return->add($daysToAdd);
        }

        if ($day < 0) {
            $return = $this->end->setTimezone($this->returnTimezone);
            // subtract 1 from the day because 1 should be the last day of the range, not 1 day before end of range
            $return = $return->sub(new \DateInterval('P'.(abs($day) - 1).'D'));
        }

        if ($return < $this->start->setTimezone($this->returnTimezone) || $return > $this->end->setTimezone($this->returnTimezone)) {
            throw new \Exception('Requested day falls outside of DateRange');
        }

        return new \DateTime($return->format('Y-m-d'));
    }

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
    public function getWeekdayOfRange($week, $weekday)
    {
        if ($week < 0) {
            return $this->getLastWeekdayOfRange($week, $weekday);
        }

        return $this->getFirstWeekdayOfRange($week, $weekday);
    }

    /**
     * Find the number of times the Month-Day values in $dates occur in the Range
     * ex. if the range is 2014-01-01 to 2016-12-01 and array('04-21','06-12', '12-31') is supplied
     * this function returns 8 (3 for each of the days except for 12-31, which occurs only twice).
     *
     * @param array $dates Array of 'Month-Day'
     * @return int
     * @throws \Exception if array of month-days not supplied
     */
    public function numberOfDatesOccurring($dates)
    {
        if (!is_array($dates)) {
            throw new \Exception("Array of 'mm-dd' not supplied");
        }

        foreach ($dates as $day) {
            if (!preg_match('/^\d\d-\d\d$/', $day)) {
                throw new \Exception("Submitted data does not match 'mm-dd' format");
            }
        }

        $oneDay = new \DateInterval('P1D');
        $current = new \DateTime($this->getStart('Y-m-d'));
        $end = new \DateTime($this->getEnd('Y-m-d'));
        $found = 0;

        while ($current <= $end->setTimezone($this->returnTimezone)) {
            if (in_array($current->format('m-d'), $dates)) {
                $found++;
            }
            $current->add($oneDay);
        }

        return $found;
    }

    /**
     * Get First Weekday of range
     * @param int $week
     * @param int $weekday
     * @return \DateTime
     */
    private function getFirstWeekdayOfRange($week, $weekday)
    {
        $weekInterval = new \DateInterval('P'.($week - 1).'W');
        $startWeekday = $this->getStart('w');
        $days = ($weekday >= $startWeekday) ? $weekday - $startWeekday : (7 + $weekday) - $startWeekday;
        $dayInterval = new \DateInterval('P'.$days.'D');

        $returnDate = new \DateTime($this->getStart()->add($dayInterval)->add($weekInterval)->format('Y-m-d H:i:s'));

        return $returnDate;
    }

    /**
     * Get last weekday of Range
     *
     * @param int $week
     * @param int $weekday
     * @return \DateTime
     */
    private function getLastWeekdayOfRange($week, $weekday)
    {
        $weekInterval = new \DateInterval('P'.(abs($week) - 1).'W');
        $startWeekday = (int) $this->getEnd('w');
        $days = ($startWeekday >= $weekday) ? $startWeekday - $weekday : (7 + $startWeekday) - $weekday;
        $dayInterval = new \DateInterval('P'.$days.'D');

        $returnDate = new \DateTime($this->getEnd()->sub($dayInterval)->sub($weekInterval)->format('Y-m-d H:i:s'));

        return $returnDate;
    }
}
