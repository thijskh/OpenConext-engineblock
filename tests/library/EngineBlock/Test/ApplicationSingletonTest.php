<?php

class EngineBlock_Test_ApplicationSingletonTest extends PHPUnit_Framework_TestCase
{
    public function testIpAddress()
    {
        $application = EngineBlock_ApplicationSingleton::getInstance();
        $configuration = $application->getConfiguration();

        $_SERVER['REMOTE_ADDR'] = 'REMOTE_ADDR_TEST';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = 'X_FORWARDED_TEST,REMOTE_ADDR_TEST';

        // X_FORWARDED from untrusted proxy
        $this->assertEquals($_SERVER['REMOTE_ADDR'], $application->getClientIpAddress());

        // X_FORWARDED from trusted proxy
        $configuration->trustedProxyIps = array('REMOTE_ADDR_TEST');
        $this->assertEquals('X_FORWARDED_TEST', $application->getClientIpAddress());

        // No X_FORWARDED, use REMOTE_ADDR
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        $this->assertEquals($_SERVER['REMOTE_ADDR'], $application->getClientIpAddress());

        // If neither and running on CLI mode (like unit testing) get special IP
        unset($_SERVER['REMOTE_ADDR']);
        $this->assertEquals(EngineBlock_ApplicationSingleton::IP_ADDRESS_CLI, $application->getClientIpAddress());
    }
}