<?php

use LogDecorator\LogDecorator;

class Component
{
    public function method1(stdClass $tmp = null, $_ = '123')
    {

    }
}

class LogDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Monolog\Logger $monolog
     */
    protected $monolog;

    /**
     * @var Monolog\Handler\TestHandler $handler
     */
    protected $handler;

    public function setUp()
    {
        parent::setUp();

        $this->monolog = new Monolog\Logger('log-decorator');

        $this->handler = new Monolog\Handler\TestHandler();

        $this->monolog->pushHandler($this->handler);
    }

    public function testDecoratorConstruct()
    {
        $component = new LogDecorator($this->monolog, new Component());

        $this->assertTrue($this->handler->hasDebugRecords());

        $this->assertCount(1, $this->handler->getRecords());
    }

    public function testDecoratorMethod()
    {
        $component = new LogDecorator($this->monolog, new Component());

        $component->method1((object)(array)'23423', 234234, $component);

        $this->assertTrue($this->handler->hasDebugRecords());

        $this->assertTrue($this->handler->hasInfoRecords());

        $this->assertCount(2, $this->handler->getRecords());
    }

    public function testDecoratorDestruct()
    {
        new LogDecorator($this->monolog, new Component());

        $this->assertTrue($this->handler->hasDebugRecords());

        $this->assertFalse($this->handler->hasInfoRecords());

        $this->assertCount(2, $this->handler->getRecords());
    }
}
