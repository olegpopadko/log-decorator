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

    public function __construct(\Psr\Log\LoggerInterface $logger, $component)
    {
        $this->begin     = Carbon::now();
        $this->component = $component;
        $this->context   = [
            'name'       => get_class($component),
            'identifier' => new \MongoId(),
            'number'     => 1,
        ];
        $this->message   = implode(' ', \array_only($this->context, ['name', 'identifier']));
        with($this->logger = $logger)->debug($this->message, $this->context);
    }

    public function __call($method, $parameters)
    {
        $context               = $this->getContext();
        $context['method']     = $method;
        $context['parameters'] = $this->formatParameters($method, $parameters);
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

    protected function formatParameters($method, $parameters)
    {
        $result               = [];
        $reflector            = new \ReflectionMethod($this->component, $method);
        $reflector_parameters = $reflector->getParameters();
        foreach ($parameters as $index => $parameter) {
            $key = $index;
            if (isset($reflector_parameters[$index])) {
                $key = $reflector_parameters[$index]->getName();
            }
            $result[$key] = $this->formatParameterValue($parameter);
        }
        $count           = count($parameters);
        $reflector_count = count($reflector_parameters);
        if ($count < $reflector_count && $reflector_parameters[$count]->isDefaultValueAvailable()) {
            for ($i = $count; $i < $reflector_count; $i++) {
                $reflector_parameter = $reflector_parameters[$i];

                $result[$reflector_parameter->getName()]
                    = $this->formatParameterValue($reflector_parameter->getDefaultValue());
            }
        }
        return $result;
    }

    protected function formatParameterValue($value)
    {
        if ($value instanceof Loggerable) {
            return $value->introduce();
        }
        $length = 50;
        $result = substr(json_encode($value), 0, $length);
        return $result . ($result === $length ? '...' : '');
    }
}
