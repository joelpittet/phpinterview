<?php

namespace Kata;

/**
 * Class Movie.
 */
class Movie {

  /**
   * Begins a transaction.
   *
   * @param int $runtime
   *   The minutes of the user.
   * @param int $day
   *   The integer value from DaysOfWeek constants.
   * @param bool $isParquet
   *   Is either parquet or lode.
   * @param bool $is3D
   *   Is the film in 3D or 2D.
   */
  public function startPurchase($runtime, $day, $isParquet, $is3D) {}

  /**
   * Adds a ticket to the transaction.
   *
   * @param int $age
   *   The age of the ticket holder.
   * @param bool $isStudent
   *   Whether the ticket is for a student.
   */
  public function addTicket($age, $isStudent) {}

  /**
   * Get the final price.
   *
   * @return float
   *   The total price of the transaction.
   */
  public function finishPurchase() {}

}
