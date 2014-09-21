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
     * @return FormatterInterface
     */
    public function getFormatter();

    /**
     * @param Formatter\FormatterInterface $formatter
     * @return mixed
     */
    public function setFormatter(Formatter\FormatterInterface $formatter);
} 
