<?php

namespace LogDecorator;

class LogDecorator
{
    /**
     * @var object
     */
    protected $component;

    /**
     * @var array
     */
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

    /**
     * Create new LogDecorator instance
     *
     * @param $component
     * @param $settings
     */
    public function __construct($component, $settings)
    {
        if (!is_object($component)) {
            throw new InvalidArgumentException(
                'First argument must be an object'
            );
        }

        // LogDecorator can be created with \Psr\Log\LoggerInterface instance or Settings instance.
        // If the given "settings" is a \Psr\Log\LoggerInterface instance,
        // we will create new Settings object with it.
        // This settings object will be settings by default.
        if ($settings instanceof \Psr\Log\LoggerInterface) {
            $settings = new Settings($settings);
        }
        if (!$settings instanceof Settings) {
            throw new InvalidArgumentException(
                'Second argument must be instance of \LogDecorator\Settings or \Psr\Log\LoggerInterface'
            );
        }

        $this->component = $component;

        $this->stopwatch = $settings->getStopwatch();
        $this->stopwatch->start();

        $this->formatter = $settings->getFormatter();

        $this->logger = $settings->getLogger();

        $message = $this->getMessage('Initialized with `:component` object');

        $this->context = [
            'component'  => get_class($component),
            'identifier' => uniqid(),
            'number'     => 1,
        ];

        $this->logger->debug($message, $this->context);
    }

    /**
     * Log method calls with formatted parameters
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $message = $this->getMessage('Call `:component` object `:method` method', compact('method'));

        $context               = $this->getContext();
        $context['method']     = $method;
        $context['parameters'] = $this->formatter->formatParameters($this->component, $method, $parameters);

        $this->logger->info($message, $context);

        return call_user_func_array(array($this->component, $method), $parameters);
    }

    /**
     * Log finish message with execution time
     */
    public function __destruct()
    {
        $message = $this->getMessage('Finish with `:component` object');

        $this->logger->debug($message, array_merge($this->getContext(), [
            'execution_time'            => $this->stopwatch->stop(),
            'execution_time_for_humans' => $this->stopwatch->stop(true),
        ]));
    }

    /**
     * Make the place-holder replacements on a message.
     *
     * @param $message
     * @param $params
     * @return string
     */
    protected function getMessage($message, $params = [])
    {
        $params = array_merge(['component' => get_class($this->component)], $params);
        foreach ($params as $key => $value) {
            $message = str_replace(':' . $key, $value, $message);
        }
        return '[LogDecorator] ' . $message;
    }

    /**
     * Return context array with incremented message number
     *
     * @return array
     */
    protected function getContext()
    {
        $this->context['number']++;
        return $this->context;
    }
}
