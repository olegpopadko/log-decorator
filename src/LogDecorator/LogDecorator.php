<?php

namespace LogDecorator;

use Carbon\Carbon;

class LogDecorator
{
    protected $begin;
    protected $component;
    protected $context;
    protected $message;
    protected $logger;

    public function __construct($component, SettingsInterface $settings)
    {
        $this->begin     = Carbon::now();
        $this->component = $component;
        $this->formatter = $settings->getFormatter();
        $this->context   = [
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
            'execution_time'            => Carbon::now()->timestamp - $this->begin->timestamp,
            'execution_time_for_humans' => Carbon::now()->diffForHumans($this->begin),
        ]));
    }

    protected function getContext()
    {
        $this->context['number']++;
        return $this->context;
    }
}
