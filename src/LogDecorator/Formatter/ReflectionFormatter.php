<?php

namespace LogDecorator\Formatter;

class ReflectionFormatter implements FormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function formatParameters($component, $method, $parameters)
    {
        $result               = [];
        $reflector            = new \ReflectionMethod($component, $method);
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
