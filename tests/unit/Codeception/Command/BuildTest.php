<?php

declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'BaseCommandRunner.php';

class BuildTest extends BaseCommandRunner
{

    /**
     * @var array
     */
    public $log = [];

    protected function _setUp(): void
    {
        $this->makeCommand(\Codeception\Command\Build::class);
        $this->config = [
            'actor' => 'HobbitGuy',
            'path' => 'tests/shire/',
            'modules' => ['enabled' => ['Filesystem', 'EmulateModuleHelper']],
            'include' => []
        ];
    }

    public function testBuild(): void
    {
        $this->execute();
        $this->assertStringContainsString('class HobbitGuy extends \Codeception\Actor', $this->content);
        // inherited methods from Actor
        $this->assertStringContainsString('@method void wantTo($text)', $this->content);
        $this->assertStringContainsString('@method void expectTo($prediction)', $this->content);

        $this->content = $this->log[0]['content'];
        // methods from Filesystem module
        $this->assertStringContainsString('public function amInPath($path)', $this->content);
        $this->assertStringContainsString('public function copyDir($src, $dst)', $this->content);
        $this->assertStringContainsString('public function seeInThisFile($text)', $this->content);

        // methods from EmulateHelper
        $this->assertStringContainsString('public function seeEquals($expected, $actual)', $this->content);

        $this->assertStringContainsString('HobbitGuyActions.php generated successfully.', $this->output);
        $this->assertIsValidPhp($this->content);
    }

    public function testBuildNamespacedActor(): void
    {
        $this->config['namespace'] = 'Shire';
        $this->execute();
        $this->assertStringContainsString('namespace Shire;', $this->content);
        $this->assertStringContainsString('class HobbitGuy extends \Codeception\Actor', $this->content);
        $this->assertStringContainsString('use _generated\HobbitGuyActions;', $this->content);
        $this->assertIsValidPhp($this->content);
    }
}
