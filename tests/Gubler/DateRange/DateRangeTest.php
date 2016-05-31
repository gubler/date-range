<?php
/**
 * Date Range Tests
 */
namespace Gubler\DateRange\Tests;

use Gubler\DateRange\DateRange;

/**
 * Class DateRangeTest
 * @package Odev\AppBundle\Tests\Utility
 */
class DateRangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference Calendar for Tests
     *
     *     August 2014
     * Su Mo Tu We Th Fr Sa
     *                 1  2
     *  3  4  5  6  7  8  9
     * 10 11 12 13 14 15 16
     * 17 18 19 20 21 22 23
     * 24 25 26 27 28 29 30
     * 31
     */

    /** @var DateRange */
    protected $dateRange;

    /**
     * Set up before each test
     */
    protected function setUp()
    {
        $this->dateRange = new DateRange(
            (new \DateTime('2014-08-05'))->setTimezone(new \DateTimeZone('Asia/Tokyo')),
            (new \DateTime('2014-08-28'))->setTimezone(new \DateTimeZone('America/Vancouver')),
            new \DateTimeZone('America/New_York')
        );
    }

    /**
     * Fast test to make sure class can be instantiated
     */
    public function testClassCanBeConstructed()
    {
        $this->assertInstanceOf(DateRange::class, $this->dateRange);
    }

    // <editor-fold desc="Setter tests">

    /**
     * @test
     */
    public function testCanSetStartDate()
    {
        $newDate = (new \DateTime('2014-08-10'))->setTimezone(new \DateTimeZone('Europe/Paris'));
        $expected = new DateRange(
            $newDate,
            (new \DateTime('2014-08-28'))->setTimezone(new \DateTimeZone('America/Vancouver')),
            new \DateTimeZone('America/New_York')
        );

        $actual = $this->dateRange->setStart($newDate);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testCanSetEndDate()
    {
        $newDate = (new \DateTime('2014-08-10'))->setTimezone(new \DateTimeZone('Europe/Paris'));
        $expected = new DateRange(
            (new \DateTime('2014-08-05'))->setTimezone(new \DateTimeZone('Asia/Tokyo')),
            $newDate,
            new \DateTimeZone('America/New_York')
        );

        $actual = $this->dateRange->setEnd($newDate);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testCanSetReturnTimeZone()
    {
        $expected = new DateRange(
            (new \DateTime('2014-08-05'))->setTimezone(new \DateTimeZone('Asia/Tokyo')),
            (new \DateTime('2014-08-28'))->setTimezone(new \DateTimeZone('America/Vancouver')),
            new \DateTimeZone('Europe/Paris')
        );

        $actual = $this->dateRange->setReturnTimezone(new \DateTimeZone('Europe/Paris'));

        $this->assertEquals($expected, $actual);
    }

    // </editor-fold>

    // <editor-fold desc="GetFromDate tests">

    /**
     * GetFromDate returns DateTime object with no format argument
     */
    public function testReturnsStartDateAsDateTime()
    {
        $result = $this->dateRange->getStart();
        $this->assertInstanceOf('DateTime', $result);
    }

    /**
     * GetFromDate returns DateTime object with no format argument
     */
    public function testReturnsStartDateWithFormatAsCorrectString()
    {
        $expected = '2014-08-05 00:00:00';
        $result = $this->dateRange->getStart('Y-m-d H:i:s');
        $this->assertEquals($expected, $result);
    }

    // </editor-fold>

    // <editor-fold desc="GetToDate tests">

    /**
     * GetToDate returns DateTime object with no format argument
     */
    public function testReturnsEndDateAsDateTime()
    {
        $result = $this->dateRange->getEnd();
        $this->assertInstanceOf('DateTime', $result);
    }

    /**
     * GetToDate returns DateTime object with no format argument
     */
    public function testReturnsEndDateWithFormatAsCorrectString()
    {
        $expected = '2014-08-28 00:00:00';
        $result = $this->dateRange->getEnd('Y-m-d H:i:s');
        $this->assertEquals($expected, $result);
    }

    // </editor-fold>

    // <editor-fold desc="GetYears tests">

    /**
     * GetYears return correct years for DateRange without format argument
     */
    public function testGetYearsReturnsCorrectArrayOfYearsWithoutFormat()
    {
        $expected = array(2014);
        $result = $this->dateRange->getYears();
        $this->assertEquals($expected, $result);

        $newDateRange = new DateRange(
            new \DateTime('2011-08-05'),
            new \DateTime('2015-08-28')
        );
        $expected = array(2011, 2012, 2013, 2014, 2015);
        $result = $newDateRange->getYears();
        $this->assertEquals($expected, $result);

    }

    /**
     * GetYears return correct years for DateRange with format argument
     */
    public function testGetYearsReturnsCorrectArrayOfYearsWithFormat()
    {
        $expected = array('2014-01-01 12:00 am');
        $result = $this->dateRange->getYears('Y-m-d h:i a');
        $this->assertEquals($expected, $result);

        $newDateRange = new DateRange(
            new \DateTime('2011-08-05'),
            new \DateTime('2015-08-28')
        );
        $expected = array(
            '2011-01-01 00:00:00',
            '2012-01-01 00:00:00',
            '2013-01-01 00:00:00',
            '2014-01-01 00:00:00',
            '2015-01-01 00:00:00',
        );
        $result = $newDateRange->getYears('Y-m-d H:i:s');
        $this->assertEquals($expected, $result);
    }

    // </editor-fold>

    // <editor-fold desc="GetMonths tests">

    /**
     * GetMonths return correct months for DateRange without format argument
     */
    public function testGetMonthsReturnsCorrectArrayOfMonthsWithoutFormatForSingleMonth()
    {
        $expected = array('2014-08');
        $result = $this->dateRange->getMonths();
        $this->assertEquals($expected, $result);
    }

    /**
     * GetMonths return correct months for DateRange without format argument
     */
    public function testGetMonthsReturnsCorrectArrayOfMonthsWithoutFormatForMultipleMonthsInSingleYear()
    {
        $newDateRange = new DateRange(
            new \DateTime('2011-08-05'),
            new \DateTime('2011-12-28')
        );
        $expected = array(
            '2011-08',
            '2011-09',
            '2011-10',
            '2011-11',
            '2011-12',
        );
        $result = $newDateRange->getMonths();
        $this->assertEquals($expected, $result);
    }

    /**
     * GetMonths return correct months for DateRange without format argument
     */
    public function testGetMonthsReturnsCorrectArrayOfMonthsWithoutFormatForMultipleMonthsInSpanningYears()
    {
        $newDateRange = new DateRange(
            new \DateTime('2011-11-05'),
            new \DateTime('2012-02-28')
        );
        $expected = array(
            '2011-11',
            '2011-12',
            '2012-01',
            '2012-02',
        );
        $result = $newDateRange->getMonths();
        $this->assertEquals($expected, $result);

    }

    /**
     * GetMonths return correct months for DateRange with format argument
     */
    public function testGetMonthsReturnsCorrectArrayOfMonthsWithFormatForSingleMonth()
    {
        $expected = array('2014-08-01 12:00 am');
        $result = $this->dateRange->getMonths('Y-m-d h:i a');
        $this->assertEquals($expected, $result);
    }
    /**
     * GetMonths return correct months for DateRange with format argument
     */
    public function testGetMonthsReturnsCorrectArrayOfMonthsWithFormatForMultipleMonthsInSingleYear()
    {
        $newDateRange = new DateRange(
            new \DateTime('2011-08-05'),
            new \DateTime('2011-12-28')
        );
        $expected = array(
            '2011-08-01 00',
            '2011-09-01 00',
            '2011-10-01 00',
            '2011-11-01 00',
            '2011-12-01 00',
        );
        $result = $newDateRange->getMonths('Y-m-d H');
        $this->assertEquals($expected, $result);
    }
    /**
     * GetMonths return correct months for DateRange with format argument
     */
    public function testGetMonthsReturnsCorrectArrayOfMonthsWithFormatForMultipleMonthsSpanningYears()
    {
        $newDateRange = new DateRange(
            new \DateTime('2011-11-05'),
            new \DateTime('2012-02-28')
        );
        $expected = array(
            '2011-11-01 00:00:00',
            '2011-12-01 00:00:00',
            '2012-01-01 00:00:00',
            '2012-02-01 00:00:00',
        );
        $result = $newDateRange->getMonths('Y-m-d H:i:s');
        $this->assertEquals($expected, $result);
    }


    // </editor-fold>

    // <editor-fold desc="GetDays tests">

    /**
     * GetDays return correct days for DateRange without format argument
     */
    public function testGetDaysReturnsCorrectArrayOfDaysWithoutFormatForSingleDay()
    {
        $newDateRange = new DateRange(
            new \DateTime('2011-08-01'),
            new \DateTime('2011-08-01')
        );
        $expected = array('2011-08-01');
        $result = $newDateRange->getDays();
        $this->assertEquals($expected, $result);
    }

    /**
     * GetDays return correct days for DateRange without format argument
     */
    public function testGetDaysReturnsCorrectArrayOfDaysWithoutFormatForMultipleDaysInSingleMonth()
    {
        $newDateRange = new DateRange(
            new \DateTime('2011-08-05'),
            new \DateTime('2011-08-10')
        );
        $expected = array(
            '2011-08-05',
            '2011-08-06',
            '2011-08-07',
            '2011-08-08',
            '2011-08-09',
            '2011-08-10',
        );
        $result = $newDateRange->getDays();
        $this->assertEquals($expected, $result);
    }

    /**
     * GetDays return correct days for DateRange without format argument
     */
    public function testGetDaysReturnsCorrectArrayOfDaysWithoutFormatForMultipleDaysInSpanningMonthsAndYears()
    {
        $newDateRange = new DateRange(
            new \DateTime('2011-12-30'),
            new \DateTime('2012-01-02')
        );
        $expected = array(
            '2011-12-30',
            '2011-12-31',
            '2012-01-01',
            '2012-01-02',
        );
        $result = $newDateRange->getDays();
        $this->assertEquals($expected, $result);

    }

    /**
     * GetDays return correct days for DateRange with format argument
     */
    public function testGetDaysReturnsCorrectArrayOfDaysWithFormatForSingleMonth()
    {
        $newDateRange = new DateRange(
            new \DateTime('2011-08-01'),
            new \DateTime('2011-08-01')
        );
        $expected = array('2011-08-01 12:00 am');
        $result = $newDateRange->getDays('Y-m-d h:i a');
        $this->assertEquals($expected, $result);
    }
    /**
     * GetDays return correct days for DateRange with format argument
     */
    public function testGetDaysReturnsCorrectArrayOfDaysWithFormatForMultipleDaysInSingleYear()
    {
        $newDateRange = new DateRange(
            new \DateTime('2011-08-05'),
            new \DateTime('2011-08-10')
        );
        $expected = array(
            '2011-08-05 00',
            '2011-08-06 00',
            '2011-08-07 00',
            '2011-08-08 00',
            '2011-08-09 00',
            '2011-08-10 00',
        );
        $result = $newDateRange->getDays('Y-m-d H');
        $this->assertEquals($expected, $result);
    }
    /**
     * GetDays return correct days for DateRange with format argument
     */
    public function testGetDaysReturnsCorrectArrayOfDaysWithFormatForMultipleDaysSpanningMonthsAndYears()
    {
        $newDateRange = new DateRange(
            new \DateTime('2011-12-30'),
            new \DateTime('2012-01-02')
        );
        $expected = array(
            '2011-12-30 00:00:00',
            '2011-12-31 00:00:00',
            '2012-01-01 00:00:00',
            '2012-01-02 00:00:00',
        );
        $result = $newDateRange->getDays('Y-m-d H:i:s');
        $this->assertEquals($expected, $result);
    }

    // </editor-fold>

    // <editor-fold desc="SplitByMonth tests">

    /**
     * SplitByMonth returns correct array of DateRanges when DateRange is within a single month
     */
    public function testSplitByMonthWithSingleMonth()
    {
        $expected = array(
            new DateRange(
                (new \DateTime('2014-08-05'))->setTimezone(new \DateTimeZone('Asia/Tokyo')),
                (new \DateTime('2014-08-28'))->setTimezone(new \DateTimeZone('America/Vancouver')),
                new \DateTimeZone('America/New_York')
            )
        );
        $result = $this->dateRange->splitByMonth();
        $this->assertEquals($expected, $result);
    }

    /**
     * SplitByMonth returns correct array of DateRanges when DateRange spans multiple months
     */
    public function testSplitByMonthWithMultipleMonth()
    {
        $this->dateRange = new DateRange(
            new \DateTime('2014-11-05'),
            new \DateTime('2015-02-28')
        );

        $expected = array(
            new DateRange(new \DateTime('2014-11-05'), new \DateTime('2014-11-30 23:59:59')),
            new DateRange(new \DateTime('2014-12-01'), new \DateTime('2014-12-31 23:59:59')),
            new DateRange(new \DateTime('2015-01-01'), new \DateTime('2015-01-31 23:59:59')),
            new DateRange(new \DateTime('2015-02-01'), new \DateTime('2015-02-28 00:00:00')),
        );
        $result = $this->dateRange->splitByMonth();
        $this->assertEquals($expected, $result);
    }

    // </editor-fold>

    // <editor-fold desc="AsSeconds tests">

    /**
     * AsSeconds returns correct count
     */
    public function testAsSecondsReturnsCorrectCount()
    {
        $this->dateRange = new DateRange(
            new \DateTime('2014-08-01 00:00:00'),
            new \DateTime('2014-08-01 01:01:01')
        );
        $expected = 3661;
        $result = $this->dateRange->asSeconds();
        $this->assertEquals($expected, $result);
    }

    // </editor-fold>

    // <editor-fold desc="AsMinutes tests">
    /**
     * AsMinutes returns correct count
     */
    public function testAsMinutesReturnsCorrectCount()
    {
        $this->dateRange = new DateRange(
            new \DateTime('2014-08-01 00:00:00'),
            new \DateTime('2014-08-01 01:01:01')
        );
        $expected = 61;
        $result = $this->dateRange->asMinutes();
        $this->assertEquals($expected, $result);
    }

    // </editor-fold>

    // <editor-fold desc="AsHours tests">
    /**
     * AsHours returns correct count
     */
    public function testAsHoursReturnsCorrectCount()
    {
        $this->dateRange = new DateRange(
            new \DateTime('2014-08-01 00:00:00'),
            new \DateTime('2014-08-01 05:59:01')
        );
        $expected = 5;
        $result = $this->dateRange->asHours();
        $this->assertEquals($expected, $result);
    }

    // </editor-fold>

    // <editor-fold desc="AsDays tests">
    /**
     * AsDays returns correct count
     */
    public function testAsDaysReturnsCorrectCount()
    {
        $this->dateRange = new DateRange(
            new \DateTime('2014-08-01 00:00:00'),
            new \DateTime('2014-08-10 05:59:01')
        );
        $expected = 9;
        $result = $this->dateRange->asDays();
        $this->assertEquals($expected, $result);
    }

    // </editor-fold>

    // <editor-fold desc="GetDayOfRange tests">
    /**
     * Test
     */
    public function testGetDayOfRangeFromStartOfRangeWithinRange()
    {
        $expected = new \DateTime('2014-08-09');
        $result = $this->dateRange->getDayOfRange(5);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Requested day falls outside of DateRange
     */
    public function testGetDayOfRangeFromStartOfRangeThrowsErrorIfOutsideOfRange()
    {
        $this->dateRange->getDayOfRange(100000);
    }

    /**
     * Test
     */
    public function testGetDayOfRangeFromEndOfRangeWithinRange()
    {
        $expected = new \DateTime('2014-08-24');
        $result = $this->dateRange->getDayOfRange(-5);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Requested day falls outside of DateRange
     */
    public function testGetDayOfRangeFromEndOfRangeThrowsErrorIfOutsideOfRange()
    {
        $this->dateRange->getDayOfRange(-100000);
    }

    // </editor-fold>

    // <editor-fold desc="WeekdayFromStart tests">
    /**
     * Test
     */
    public function testWeekdayFromStartForStartOnWeekday()
    {
        $expected = new \DateTime('2014-08-05');
        $actual = $this->dateRange->getWeekdayOfRange(1, 2);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test
     */
    public function testWeekdayFromStartForStartAfterWeekday()
    {
        $expected = new \DateTime('2014-08-11');
        $actual = $this->dateRange->getWeekdayOfRange(1, 1);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test
     */
    public function testWeekdayFromStartForStartBeforeWeekday()
    {
        $expected = new \DateTime('2014-08-07');
        $actual = $this->dateRange->getWeekdayOfRange(1, 4);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test
     */
    public function testWeekdayFromStartForStartOnWeekdayWithWeekCountGreaterThan1()
    {
        $expected = new \DateTime('2014-08-12');
        $actual = $this->dateRange->getWeekdayOfRange(2, 2);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test
     */
    public function testWeekdayFromStartForStartAfterWeekdayWithWeekCountGreaterThan1()
    {
        $expected = new \DateTime('2014-08-18');
        $actual = $this->dateRange->getWeekdayOfRange(2, 1);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test
     */
    public function testWeekdayFromStartForStartBeforeWeekdayWithWeekCountGreaterThan1()
    {
        $expected = new \DateTime('2014-08-14');
        $actual = $this->dateRange->getWeekdayOfRange(2, 4);
        $this->assertEquals($expected, $actual);
    }

    // </editor-fold>

    // <editor-fold desc="WeekdayFromEnd tests">
    /**
     * Test
     */
    public function testWeekdayFromEndForEndOnWeekday()
    {
        $expected = new \DateTime('2014-08-28');
        $actual = $this->dateRange->getWeekdayOfRange(-1, 4);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test
     */
    public function testWeekdayFromEndForEndAfterWeekday()
    {
        $expected = new \DateTime('2014-08-26');
        $actual = $this->dateRange->getWeekdayOfRange(-1, 2);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test
     */
    public function testWeekdayFromEndForEndBeforeWeekday()
    {
        $expected = new \DateTime('2014-08-22');
        $actual = $this->dateRange->getWeekdayOfRange(-1, 5);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test
     */
    public function testWeekdayFromEndForEndOnWeekdayWithWeekCountGreaterThan1()
    {
        $expected = new \DateTime('2014-08-21');
        $actual = $this->dateRange->getWeekdayOfRange(-2, 4);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test
     */
    public function testWeekdayFromEndForEndAfterWeekdayWithWeekCountGreaterThan1()
    {
        $expected = new \DateTime('2014-08-19');
        $actual = $this->dateRange->getWeekdayOfRange(-2, 2);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test
     */
    public function testWeekdayFromEndForEndBeforeWeekdayWithWeekCountGreaterThan1()
    {
        $expected = new \DateTime('2014-08-15');
        $actual = $this->dateRange->getWeekdayOfRange(-2, 5);
        $this->assertEquals($expected, $actual);
    }

    // </editor-fold>

    // <editor-fold desc="NumberOfDateOccurring tests">
    /**
     * Test
     */
    public function testNumberOfDatesOccurringReturnsCorrectCount()
    {
        $this->dateRange = new DateRange(
            new \DateTime('2014-01-01'),
            new \DateTime('2016-12-01')
        );

        $find = array('01-12', '06-16', '12-21');
        $expected = 8;
        $actual = $this->dateRange->numberOfDatesOccurring($find);

        $this->assertEquals($expected, $actual, 'Did not find 8 occurrences');
    }

    /**
     * Test
     * @expectedException        \Exception
     * @expectedExceptionMessage Array of 'mm-dd' not supplied
     */
    public function testNumberOfDatesOccurringThrowsExceptionWithNoArray()
    {
        $this->dateRange->numberOfDatesOccurring('moo');
    }

    /**
     * Test
     *
     * @expectedException        \Exception
     * @expectedExceptionMessage Submitted data does not match 'mm-dd' format
     */
    public function testNumberOfDatesOccurringThrowsExceptionWithArrayWithBadValue()
    {
        $find = array('01-12', '06-16', 'moo');
        $this->dateRange->numberOfDatesOccurring($find);
    }
    // </editor-fold>
}
