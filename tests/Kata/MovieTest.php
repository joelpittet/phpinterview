<?php

namespace Kata\Tests;

use Kata\DaysOfWeek;
use Kata\Movie;

class MovieTest extends \PHPUnit_Framework_TestCase {

  /**
   * Data provider for movies.
   *
   * @return array
   */
  public function movieProvider() {
    return [
      [[72, DaysOfWeek::TUE, TRUE, FALSE], 44.0, "2D, parquet, Tuesday"],
      [[72, DaysOfWeek::TUE, TRUE, TRUE], 56.0, "3D, parquet, Tuesday"],
      [[72, DaysOfWeek::TUE, FALSE, TRUE], 64.0, "3D, loge, Tuesday"],
      [[120, DaysOfWeek::THU, FALSE, TRUE], 56.0, "3D, loge, movie day"],
      [[72, DaysOfWeek::SAT, FALSE, TRUE], 70.0, "3D, loge, Saturday"],
      [[72, DaysOfWeek::SUN, FALSE, TRUE], 70.0, "3D, loge, Sunday"],
      [[145, DaysOfWeek::WED, TRUE, FALSE], 50.0, "overlength, 2D, parquet, Wednesday"],
    ];
  }

  /**
   * Tests middle age non student buyers.
   *
   * @dataProvider movieProvider
   */
  public function testMiddleAgeNonStudents($movie, $expectedResult, $message) {
    $tickets = [[35, FALSE], [35, FALSE], [35, FALSE], [35, FALSE]];
    $movie[] = $tickets;
    $result = call_user_func_array([$this, 'calc'], $movie);
    $this->assertEquals($expectedResult, $result, $message . ", mid-age, no student");
  }

  protected function applyTickets(Movie $cashReg, $runtime, $day, $isParquet, $is3D, $tickets) {
    $cashReg->startPurchase($runtime, $day, $isParquet, $is3D);
    foreach ($tickets as $ticket) {
      $cashReg->addTicket($ticket[0], $ticket[1]);
    }

    return $cashReg;
  }

  /**
   * Calculate helper.
   *
   * @param int $runtime
   *   The runtime in minutes.
   * @param int $day
   *   The day of the week in integer value.
   * @param bool $isParquet
   *   If the movie is Parquet or Lode.
   * @param bool $is3D
   *   Whether the movie is in 3D or 2D.
   * @param array $tickets
   *   A list of tickets.
   *
   * @return float
   */
  protected function calc($runtime, $day, $isParquet, $is3D, $tickets) {
    $cashReg = new Movie();
    $this->applyTickets($cashReg, $runtime, $day, $isParquet, $is3D, $tickets);
    return $cashReg->finishPurchase();
  }

  public function testNoTicketsCostsZero() {
    $result = $this->calc(0, DaysOfWeek::MON, FALSE, FALSE, []);
    $this->assertSame(0, $result, "No tickets costs zero");
  }

  public function testOverlength2DParquetWednesdayMidAgeStudents() {
    $tickets = [ [35, FALSE], [35, FALSE], [64, TRUE], [35, TRUE] ];
    $result = $this->calc(121, DaysOfWeek::WED, TRUE, FALSE, $tickets);
    $this->assertEquals(44.0, $result, "overlength, 2D, parquet, wednesday, mid-age, students");
  }

  public function testOverlength2DParquetMondaySeniorNoStudents() {
    $tickets = [ [35, FALSE], [35, FALSE], [64, FALSE], [65, FALSE] ];
    $result = $this->calc(123, DaysOfWeek::MON, TRUE, FALSE, $tickets);
    $this->assertEquals(45.0, $result, "overlength, 2D, parquet, monday, senior, no students");
  }

  public function testOverlength2DParquetTuesdaySeniorStudents() {
    $tickets = [ [35, FALSE], [35, FALSE], [64, FALSE], [68, TRUE] ];
    $result = $this->calc(145, DaysOfWeek::TUE, TRUE, FALSE, $tickets);
    $this->assertEquals(45.0, $result, "overlength, 2D, parquet, tuesday, senior students");
  }

  public function testOverlength2DParquetTuesday1ChildNoStudents() {
    $tickets = [ [35, FALSE], [35, FALSE], [64, FALSE], [10, FALSE] ];
    $result = $this->calc(145, DaysOfWeek::TUE, TRUE, FALSE, $tickets);
    $this->assertEquals(44.5, $result, "overlength, 2D, parquet, tuesday, 1 child, no students");
  }

  public function test2DparquetTuesdayGroupNoStudents() {
    $tickets = [];
    for ($i = 0; $i < 23; $i++) {
      $tickets[] = [35, FALSE];
    }
    $result = $this->calc(72, DaysOfWeek::TUE, TRUE, FALSE, $tickets);
    $this->assertEquals(138.0, $result, "2D, parquet, tuesday, group, no students");
  }

  public function test3DParquetTuesdayGroupNoStudents() {
    $tickets = [];
    for ($i = 0; $i < 23; $i++) {
      $tickets[] = [35, FALSE];
    }
    $result = $this->calc(72, DaysOfWeek::TUE, TRUE, TRUE, $tickets);
    $this->assertEquals(207.0, $result, "3D, parquet, tuesday, group, no students");
  }

  public function test2DGroupOfKidsWithTwoAdults() {
    $tickets = [];
    for ($i = 0; $i < 24; $i++) {
      $tickets[] = [12, FALSE];
    }
    $tickets[] = [45, FALSE];
    $tickets[] = [27, FALSE];
    $result = $this->calc(72, DaysOfWeek::FRI, TRUE, FALSE, $tickets);
    $this->assertEquals(144.0, $result, "2D, group of kids with two adults");
  }

  public function test2D17KidsWithTwoAdults() {
    $tickets = [];
    for ($i = 0; $i < 17; $i++) {
      $tickets[] = [12, FALSE];
    }
    $tickets[] = [45, FALSE];
    $tickets[] = [27, FALSE];
    $result = $this->calc(72, DaysOfWeek::WED, TRUE, FALSE, $tickets);
    $this->assertEquals(115.5, $result, "2D, 17 kids with two adults");
  }

  public function testOverlengthLoge3DMovieDayGroup() {
    $tickets = [];
    for ($i = 0; $i < 5; $i++) {
      $tickets[] = [12, FALSE];
    }
    for ($i = 0; $i < 7; $i++) {
      $tickets[] = [45, FALSE];
    }
    for ($i = 0; $i < 4; $i++) {
      $tickets[] = [75, FALSE];
    }
    for ($i = 0; $i < 8; $i++) {
      $tickets[] = [27, TRUE];
    }
    $result = $this->calc(125, DaysOfWeek::THU, FALSE, TRUE, $tickets);
    $this->assertEquals(297.5, $result, "overlength, loge, 3D, movie-day group");
  }

  public function testOverlengthLoge3DmovieDayNonGroup() {
    $tickets = [];
    for ($i = 0; $i < 2; $i++) {
      $tickets[] = [12, FALSE];
    }
    for ($i = 0; $i < 7; $i++) {
      $tickets[] = [45, FALSE];
    }
    for ($i = 0; $i < 4; $i++) {
      $tickets[] = [75, FALSE];
    }
    for ($i = 0; $i < 4; $i++) {
      $tickets[] = [27, TRUE];
    }
    $result = $this->calc(125, DaysOfWeek::THU, FALSE, TRUE, $tickets);
    $this->assertEquals(220.5, $result, "overlength, loge, 3D, movie-day non-group");
  }

  public function testMultipleTransactionSameRegister() {
    $cashReg = new Movie();
    $tickets = [ [35, FALSE], [35, FALSE], [35, FALSE], [35, FALSE], [35, FALSE] ];

    $this->applyTickets($cashReg, 90, DaysOfWeek::MON, TRUE, TRUE, $tickets);
    $this->assertEquals(70.0, $cashReg->finishPurchase(), "multiple transactions, same register");

    $this->applyTickets($cashReg, 90, DaysOfWeek::MON, TRUE, TRUE, $tickets);
    $this->assertEquals(70.0, $cashReg->finishPurchase(), "multiple transactions, same register");
  }

}
