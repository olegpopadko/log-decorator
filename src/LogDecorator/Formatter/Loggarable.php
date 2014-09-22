<?php

namespace LogDecorator\Formatter;

interface Loggerable
{
    /**
     * Object introducing sentence
     *
     * @return string
     */
    public function introduce();
}
