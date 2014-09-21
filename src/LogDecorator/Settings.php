<?php

namespace LogDecorator;

class Settings implements SettingsInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var Formatter\FormatterInterface
     */
    protected $formatter;

    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->setLogger($logger);
    }

    /**
     * {@inheritdoc}
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogger(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatter()
    {
        if (is_null($this->formatter)) {
            $this->formatter = $this->getDefaultFormatter();
        }
        return $this->formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormatter(Formatter\FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Gets default formatter
     *
     * @param Formatter\FormatterInterface $formatter
     */
    protected function getDefaultFormatter()
    {
        return new Formatter\ReflectionFormatter;
    }


} 
