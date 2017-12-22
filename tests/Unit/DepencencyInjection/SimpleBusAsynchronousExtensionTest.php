<?php

namespace SimpleBus\AsynchronousBundle\Tests\Unit\DependencyInjection;


use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use SimpleBus\AsynchronousBundle\DependencyInjection\SimpleBusAsynchronousExtension;

class SimpleBusAsynchronousExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return array(
            new SimpleBusAsynchronousExtension('simple_bus_asynchronous')
        );
    }

    protected function getMinimalConfiguration()
    {
        return ['object_serializer_service_id'=>'my_serializer', 'commands'=>['publisher_service_id'=>'pusher'], 'events'=>['publisher_service_id'=>'pusher']];
    }


    /**
     * @test
     */
    public function it_uses_strategy_always_by_default()
    {
        $this->container->setParameter('kernel.bundles', ['SimpleBusCommandBusBundle'=>true, 'SimpleBusEventBusBundle'=>true]);
        $this->load();

        $this->assertContainerBuilderHasServiceDefinitionWithTag('simple_bus.asynchronous.always_publishes_messages_middleware', 'event_bus_middleware', ['priority'=>0]);
    }

    /**
     * @test
     */
    public function it_uses_strategy_predefined_when_configured()
    {
        $this->container->setParameter('kernel.bundles', ['SimpleBusCommandBusBundle'=>true, 'SimpleBusEventBusBundle'=>true]);
        $this->load(['events'=>['strategy'=>'predefined']]);

        $this->assertContainerBuilderHasServiceDefinitionWithTag('simple_bus.asynchronous.publishes_predefined_messages_middleware', 'event_bus_middleware', ['priority'=>0]);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @expectedExceptionMessageRegExp ".*custom_strategy.*"
     */
    public function it_uses_custom_strategy_when_configured()
    {
        $this->container->setParameter('kernel.bundles', ['SimpleBusCommandBusBundle'=>true, 'SimpleBusEventBusBundle'=>true]);
        $this->load(['events'=>['strategy'=>['strategy_service_id'=>'custom_strategy']]]);
    }

    /**
     * @test
     * @expectedException \LogicException
     * @expectedExceptionMessageRegExp ".*SimpleBusCommandBusBundle.*"
     */
    public function it_throws_exception_if_command_bus_bundle_is_missing()
    {
        $this->container->setParameter('kernel.bundles', ['SimpleBusEventBusBundle'=>true]);
        $this->load(['events'=>['strategy'=>'predefined']]);
    }

    /**
     * @test
     * @expectedException \LogicException
     * @expectedExceptionMessageRegExp ".*SimpleBusEventBusBundle.*"
     */
    public function it_throws_exception_if_event_bus_bundle_is_missing()
    {
        $this->container->setParameter('kernel.bundles', ['SimpleBusCommandBusBundle'=>true]);
        $this->load(['events'=>['strategy'=>'predefined']]);
    }
}
