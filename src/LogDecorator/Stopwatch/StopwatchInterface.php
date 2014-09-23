<?php

namespace LogDecorator\Stopwatch;

interface StopwatchInterface
{
    /**
     * Start stopwatch
     * @return null
     */
    public function start();

    /**
     * @param $for_humans
     * @return float|string time from start
     */
    public function stop($for_humans = false);
}
