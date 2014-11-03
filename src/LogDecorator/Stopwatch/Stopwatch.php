<?php
/**
 * Created by Oleg Popadko
 * Date: 9/23/14
 * Time: 2:01 PM
 */

namespace LogDecorator\Stopwatch;

class Stopwatch implements StopwatchInterface
{
    /**
     * @var array
     */
    private static $times = array(
        'hour'   => 3600000,
        'minute' => 60000,
        'second' => 1000
    );

    /**
     * @var
     */
    protected $start;

    /**
     * Start the timer
     */
    public function start()
    {
        $this->start = microtime(true);
    }

    /**
     * Stops the timer and returns the elapsed time
     *
     * @param bool $for_humans
     * @return float|string
     */
    public function stop($for_humans = false)
    {
        $diff = microtime(true) - $this->start;
        if ($for_humans) {
            $diff = $this->secondsToTimeString($diff);
        }
        return $diff;
    }

    /**
     * Formats the elapsed time as a string.
     *
     * @param  float $time
     * @return string
     */
    protected static function secondsToTimeString($time)
    {
        $ms = round($time * 1000);

        foreach (self::$times as $unit => $value) {
            if ($ms >= $value) {
                $time = floor($ms / $value * 100.0) / 100.0;
                return $time . ' ' . ($time == 1 ? $unit : $unit . 's');
            }
        }

        return $ms . ' ms';
    }
}
