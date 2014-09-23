<?php

namespace LogDecorator;

use Carbon\Carbon;
use Instantiator\Exception\InvalidArgumentException;

class LogDecorator
{
    protected $component;
    protected $context;

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

        $message = sprintf('Initialized with `%s` object', get_class($this->component));

        with($this->logger = $settings->getLogger())->debug($this->getMessage($message), $this->context);
    }

    public function __call($method, $parameters)
    {
        $context               = $this->getContext();
        $context['method']     = $method;
        $context['parameters'] = $this->formatter->formatParameters($this->component, $method, $parameters);

        $message = $this->getMessage(sprintf('Call `%s` object `%s` method', get_class($this->component), $method));
        $this->logger->info($message, $context);

        return call_user_func_array(array($this->component, $method), $parameters);
    }

    public function __destruct()
    {
        $message = $this->getMessage(sprintf('Finish with `%s` object', get_class($this->component)));
        $this->logger->debug($message, array_merge($this->getContext(), [
            'execution_time'            => $this->stopwatch->stop(),
            'execution_time_for_humans' => $this->stopwatch->stop(true),
        ]));
    }

    protected function getMessage($message)
    {
        return '[LogDecorator] ' . $message;
    }

    protected function getContext()
    {
        $this->context['number']++;
        return $this->context;
    }
}
