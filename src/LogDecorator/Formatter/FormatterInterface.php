<?php

namespace LogDecorator\Formatter;

interface FormatterInterface
{
    /**
     * Format parameters
     *
     * @param $component
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function formatParameters($component, $method, $parameters);
}
