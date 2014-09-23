<?php

namespace LogDecorator;

use Carbon\Carbon;
use Instantiator\Exception\InvalidArgumentException;

class LogDecorator
{
    protected $component;
    protected $context;
    protected $message;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var Formatter\FormatterInterface
     */
    protected $formatter;

    /**
     * @var Stopwatch\StopwatchInterface
     */
    protected $stopwatch;

    public function __construct($component, $settings)
    {
        if ($settings instanceof \Psr\Log\LoggerInterface) {
            $settings = new Settings($settings);
        }
        if (!$settings instanceof Settings) {
            throw new InvalidArgumentException();
        }

        $this->component = $component;

        $this->stopwatch = $settings->getStopwatch();
        $this->stopwatch->start();

        $this->formatter = $settings->getFormatter();

        $this->context = [
            'name'       => get_class($component),
            'identifier' => new \MongoId(),
            'number'     => 1,
        ];
        $this->message   = implode(' ', \array_only($this->context, ['name', 'identifier']));
        with($this->logger = $settings->getLogger())->debug($this->message, $this->context);
    }

    public function __call($method, $parameters)
    {
        $context               = $this->getContext();
        $context['method']     = $method;
        $context['parameters'] = $this->formatter->formatParameters($this->component, $method, $parameters);

        $this->logger->info($this->message, $context);

        return call_user_func_array(array($this->component, $method), $parameters);
    }

    public function __destruct()
    {
        $this->logger->debug($this->message, array_merge($this->getContext(), [
            'execution_time'            => $this->stopwatch->stop(),
            'execution_time_for_humans' => $this->stopwatch->stop(true),
        ]));
    }

    protected function getContext()
    {
        $this->context['number']++;
        return $this->context;
    }
}
