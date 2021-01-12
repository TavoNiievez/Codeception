<?php
class BootstrapCest
{

    protected $bootstrapPath;

    public function _before(\CliGuy $I)
    {
        $this->bootstrapPath = 'tests/data/sandbox/boot'.uniqid();
        @mkdir($this->bootstrapPath, 0777, true);
        $I->amInPath($this->bootstrapPath);
    }

    public function bootstrap(\CliGuy $I)
    {
        $I->executeCommand('bootstrap');
        $I->seeFileFound('codeception.yml');
        $this->checkFilesCreated($I);
    }

    public function bootstrapWithNamespace(\CliGuy $I)
    {
        $I->executeCommand('bootstrap --namespace Generated');

        $I->seeFileFound('codeception.yml');
        $I->seeInThisFile('namespace: Generated');
        $I->dontSeeInThisFile('namespace Generated\\');
        $this->checkFilesCreated($I);

        $I->seeFileFound('AcceptanceTester.php', 'tests/TestSupport');
        $I->seeInThisFile('namespace Generated\\TestSupport;');
    }

    public function bootstrapWithNamespaceShortcut(\CliGuy $I)
    {
        $I->executeCommand('bootstrap -s Generated');

        $I->seeFileFound('codeception.yml');
        $I->seeInThisFile('namespace: Generated');
        $I->dontSeeInThisFile('namespace Generated\\');
        $this->checkFilesCreated($I);

        $I->seeFileFound('AcceptanceTester.php', 'tests/TestSupport');
        $I->seeInThisFile('namespace Generated\\TestSupport;');
    }

    public function bootstrapWithActor(\CliGuy $I)
    {
        $I->executeCommand('bootstrap --actor Ninja');
        $I->seeFileFound('AcceptanceNinja.php', 'tests/TestSupport/');
    }


    public function bootstrapEmpty(\CliGuy $I)
    {
        $I->executeCommand('bootstrap --empty');
        $I->dontSeeFileFound('tests/acceptance');
        $I->seeFileFound('codeception.yml');
    }

    public function bootstrapFromInit(\CliGuy $I)
    {
        $I->executeCommand('init bootstrap');
        $this->checkFilesCreated($I);
    }

    public function bootstrapFromInitUsingClassName(\CliGuy $I)
    {
        $I->executeCommand('init "Codeception\Template\Bootstrap"');
        $this->checkFilesCreated($I);
    }

    protected function checkFilesCreated(\CliGuy $I)
    {
        $I->seeDirFound('tests/TestSupport');
        $I->seeDirFound('tests/TestSupport/Data');
        $I->seeDirFound('tests/_output');

        $I->seeFileFound('Functional.suite.yml', 'tests');
        $I->seeFileFound('Acceptance.suite.yml', 'tests');
        $I->seeFileFound('Unit.suite.yml', 'tests');

        $I->seeFileFound('AcceptanceTester.php', 'tests/TestSupport');
        $I->seeFileFound('FunctionalTester.php', 'tests/TestSupport');
        $I->seeFileFound('UnitTester.php', 'tests/TestSupport');
    }

}
