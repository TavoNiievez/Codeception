<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'BaseCommandRunner.php';

class GenerateSuiteTest extends BaseCommandRunner
{
    public $config = ['actor_suffix' => 'Guy'];

    protected function _setUp()
    {
        $this->makeCommand('\Codeception\Command\GenerateSuite');
    }

    public function testBasic()
    {
        $this->execute(array('suite' => 'shire', 'actor' => 'Hobbit'), false);

        $configFile = $this->log[0];

        $this->assertEquals(\Codeception\Configuration::projectDir().'tests/Shire.suite.yml', $configFile['filename']);
        $conf = \Symfony\Component\Yaml\Yaml::parse($configFile['content']);
        $this->assertEquals('Hobbit', $conf['actor']);
        $this->assertStringContainsString('Suite shire generated', $this->output);

        $actor = $this->log[1];
        $this->assertEquals(\Codeception\Configuration::supportDir().'Hobbit.php', $actor['filename']);
        $this->assertStringContainsString('class Hobbit extends \Codeception\Actor', $actor['content']);

    }

    public function testGuyWithSuffix()
    {
        $this->execute(array('suite' => 'shire', 'actor' => 'HobbitTester'), false);

        codecept_debug($this->log);
        
        $configFile = $this->log[0];
        $conf = \Symfony\Component\Yaml\Yaml::parse($configFile['content']);
        $this->assertEquals('HobbitTester', $conf['actor']);

    }
}
