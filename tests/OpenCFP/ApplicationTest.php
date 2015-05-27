<?php

namespace OpenCFP;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

/**
 * @covers OpenCFP\Application
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{

    /** @var Application */
    protected $sut;

    /**
     * @test
     */
    public function it_should_run_and_have_output()
    {
        $this->sut = new Application(BASE_PATH, Environment::testing());
        $this->sut['session'] = new Session(new MockFileSessionStorage());

        // We start an output buffer because the Application sends its response to
        // the output buffer as a Symfony Response.
        ob_start();
        $this->sut->run();
        $output = ob_get_clean();

        $this->assertNotEmpty($output);
    }

    /** @test */
    public function it_should_resolve_configuration_path_based_on_environment()
    {
        $this->sut = new Application(BASE_PATH, Environment::testing());

        $this->assertTrue($this->sut->isTesting());
        $this->assertContains('testing.yml', $this->sut->configPath());
    }

}
