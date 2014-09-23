<?php

namespace LogDecorator;

interface SettingsInterface
{
    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger();

    /**
     * @param \Psr\Log\LoggerInterface $formatter
     * @return mixed
     */
    public function setLogger(\Psr\Log\LoggerInterface $formatter);

    /**
     * @return Formatter\FormatterInterface
     */
    public function getFormatter();

    /**
     * @param Formatter\FormatterInterface $formatter
     * @return mixed
     */
    public function setFormatter(Formatter\FormatterInterface $formatter);

    /**
     * @return Stopwatch\StopwatchInterface
     */
    public function getStopwatch();

    /**
     * @param Stopwatch\StopwatchInterface $stopwatch
     * @return mixed
     */
    public function setStopwatch(Stopwatch\StopwatchInterface $formatter);
} 
