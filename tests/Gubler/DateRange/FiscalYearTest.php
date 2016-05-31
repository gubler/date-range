<?php
/**
 * Tests for Fiscal Year
 */

namespace Gubler\DateRange\Tests;
use Gubler\DateRange\DateRange;
use Gubler\DateRange\FiscalYear;

/**
 * Class FiscalYearTest
 *
 * @package Gubler\DateRange\Tests
 */
class FiscalYearTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test object can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $fiscalYear = new FiscalYear(10, 1);
        $this->assertInstanceOf(FiscalYear::class, $fiscalYear);
    }

    /**
     * @test invalid dates throw error
     * @expectedException \Exception
     */
    public function testCanNotInstatiateInvalidDate()
    {
        new FiscalYear(13, 1);
    }

    /**
     * @test can get FY string from date
     */
    public function testReturnsStringFromDate()
    {
        $testDate = new \DateTime('2015-06-13 16:30:22');

        // within fiscal year
        $fiscalYear = new FiscalYear(1, 1);
        $actual = $fiscalYear->dateToFyString($testDate);
        $expected = '2015';
        $this->assertEquals($expected, $actual);

        // split year, in start calendar year
        $fiscalYear = new FiscalYear(5, 5);
        $actual = $fiscalYear->dateToFyString($testDate);
        $expected = '2016';
        $this->assertEquals($expected, $actual);

        // split year, in end calendar year
        $fiscalYear = new FiscalYear(7, 15);
        $actual = $fiscalYear->dateToFyString($testDate);
        $expected = '2015';
        $this->assertEquals($expected, $actual);

        // 4 year with prefix
        $fiscalYear = new FiscalYear(1, 1);
        $actual = $fiscalYear->dateToFyString($testDate, 4, 'FY');
        $expected = 'FY2015';
        $this->assertEquals($expected, $actual);

        // 2 year
        $actual = $fiscalYear->dateToFyString($testDate, 2);
        $expected = '15';
        $this->assertEquals($expected, $actual);

        // 2 year with prefix
        $actual = $fiscalYear->dateToFyString($testDate, 2, 'FY');
        $expected = 'FY15';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test can create a date range from a date
     */
    public function testCanCreateDateRangeFromDate()
    {
        $testDate = new \DateTime('2015-06-13 16:30:22');

        // within fiscal year
        $fiscalYear = new FiscalYear(1, 1);
        $actual = $fiscalYear->dateToFyDateRange($testDate);
        $expected = new DateRange(
            new \DateTime('2015-01-01 00:00:00'),
            new \DateTime('2015-12-31 23:59:59')
        );
        $this->assertEquals($expected, $actual);

        // split year, in end calendar year
        $fiscalYear = new FiscalYear(5, 5);
        $expected = new DateRange(
            new \DateTime('2015-05-05 00:00:00'),
            new \DateTime('2016-05-04 23:59:59')
        );
        $actual = $fiscalYear->dateToFyDateRange($testDate);
        $this->assertEquals($expected, $actual);

        // split year, in start calendar year
        $fiscalYear = new FiscalYear(7, 15);
        $expected = new DateRange(
            new \DateTime('2014-07-15 00:00:00'),
            new \DateTime('2015-07-14 23:59:59')
        );
        $actual = $fiscalYear->dateToFyDateRange($testDate);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testCanCreateDateRangeFromString()
    {
        // single year
        $fiscalYear = new FiscalYear(1, 1);
        $actual = $fiscalYear->yearToFyDateRange(2015);
        $expected = new DateRange(
            new \DateTime('2015-01-01 00:00:00'),
            new \DateTime('2015-12-31 23:59:59')
        );
        $this->assertEquals($expected, $actual);

        // split year
        $fiscalYear = new FiscalYear(5, 5);
        $expected = new DateRange(
            new \DateTime('2015-05-05 00:00:00'),
            new \DateTime('2016-05-04 23:59:59')
        );
        $actual = $fiscalYear->yearToFyDateRange(2016);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testDateInFiscalYear()
    {
        $testDate = new \DateTime('2015-06-13 16:30:22');

        // within fiscal year
        $fiscalYear = new FiscalYear(1, 1);
        $inYear = $fiscalYear->dateInFy($testDate, '2015');
        $this->assertTrue($inYear);
        $beforeYear = $fiscalYear->dateInFy($testDate, '2016');
        $this->assertFalse($beforeYear);
        $afterYear = $fiscalYear->dateInFy($testDate, '2014');
        $this->assertFalse($afterYear);

        // split year, in end calendar year
        $fiscalYear = new FiscalYear(5, 5);
        $inYear = $fiscalYear->dateInFy($testDate, '2016');
        $this->assertTrue($inYear);
        $beforeYear = $fiscalYear->dateInFy($testDate, '2015');
        $this->assertFalse($beforeYear);
        $afterYear = $fiscalYear->dateInFy($testDate, '2017');
        $this->assertFalse($afterYear);

        // split year, in start calendar year
        $fiscalYear = new FiscalYear(7, 15);
        $inYear = $fiscalYear->dateInFy($testDate, '2015');
        $this->assertTrue($inYear);
        $beforeYear = $fiscalYear->dateInFy($testDate, '2016');
        $this->assertFalse($beforeYear);
        $afterYear = $fiscalYear->dateInFy($testDate, '2014');
        $this->assertFalse($afterYear);
    }
}
