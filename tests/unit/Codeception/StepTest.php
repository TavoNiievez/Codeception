<?php

declare(strict_types=1);

use Codeception\Stub;
use Facebook\WebDriver\WebDriverBy;
use Codeception\Step;

class StepTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param $args
     * @return Codeception\Step
     */
    protected function getStep($args)
    {
        return $this->getMockBuilder(\Codeception\Step::class)->setConstructorArgs($args)->setMethods(null)->getMock();
    }

    public function testGetArguments(): void
    {
        //facebook/php-webdriver is no longer a dependency of core so this behaviour can't be tested anymore
        //$by = WebDriverBy::cssSelector('.something');
        //$step = $this->getStep([null, [$by]]);
        //$this->assertEquals('"' . Locator::humanReadableString($by) . '"', $step->getArgumentsAsString());

        $step = $this->getStep([null, [['just', 'array']]]);
        $this->assertEquals('["just","array"]', $step->getArgumentsAsString());

        $step = $this->getStep([null, [function () {
        }]]);
        $this->assertEquals('"Closure"', $step->getArgumentsAsString());

        $step = $this->getStep([null, [[$this, 'testGetArguments']]]);
        $this->assertEquals('["StepTest","testGetArguments"]', $step->getArgumentsAsString());

        $step = $this->getStep([null, [[\PDO::class, 'getAvailableDrivers']]]);
        $this->assertEquals('["PDO","getAvailableDrivers"]', $step->getArgumentsAsString());

        $step = $this->getStep([null, [[Stub::make($this, []), 'testGetArguments']]]);
        $this->assertEquals('["StepTest","testGetArguments"]', $step->getArgumentsAsString());

        $mock = $this->createMock(get_class($this));
        $step = $this->getStep([null, [[$mock, 'testGetArguments']]]);
        $className = get_class($mock);
        $this->assertEquals('["' . $className . '","testGetArguments"]', $step->getArgumentsAsString());
    }

    public function testGetHtml(): void
    {
        $step = $this->getStep(['Do some testing', ['arg1', 'arg2']]);
        $this->assertSame('I do some testing <span style="color: #732E81">&quot;arg1&quot;,&quot;arg2&quot;</span>', $step->getHtml());

        $step = $this->getStep(['Do some testing', []]);
        $this->assertSame('I do some testing', $step->getHtml());

        $argument = str_repeat("A string with a length exceeding Step::DEFAULT_MAX_LENGTH.", Step::DEFAULT_MAX_LENGTH);
        $step = $this->getStep(['Do some testing', [$argument]]);
        $this->assertSame('I do some testing <span style="color: #732E81">&quot;' . $argument . '&quot;</span>', $step->getHtml());
    }

    public function testLongArguments(): void
    {
        $step = $this->getStep(['have in database', [str_repeat('a', 2000)]]);
        $output = $step->toString(200);
        $this->assertLessThan(201, strlen($output), 'Output is too long: ' . $output);

        $step = $this->getStep(['have in database', [str_repeat('a', 100), str_repeat('b', 100)]]);
        $output = $step->toString(50);
        $this->assertEquals(50, strlen($output), 'Incorrect length of output: ' . $output);
        $this->assertEquals('have in database "aaaaaaaaaaa...","bbbbbbbbbbb..."', $output);

        $step = $this->getStep(['have in database', [1, str_repeat('b', 100)]]);
        $output = $step->toString(50);
        $this->assertEquals('have in database 1,"bbbbbbbbbbbbbbbbbbbbbbbbbb..."', $output);

        $step = $this->getStep(['have in database', [str_repeat('b', 100), 1]]);
        $output = $step->toString(50);
        $this->assertEquals('have in database "bbbbbbbbbbbbbbbbbbbbbbbbbb...",1', $output);
    }

    public function testArrayAsArgument(): void
    {
        $step = $this->getStep(['see array', [[1,2,3], 'two']]);
        $output = $step->toString(200);
        $this->assertEquals('see array [1,2,3],"two"', $output);
    }

    public function testSingleQuotedStringAsArgument(): void
    {
        $step = $this->getStep(['see array', [[1,2,3], "'two'"]]);
        $output = $step->toString(200);
        $this->assertEquals('see array [1,2,3],"\'two\'"', $output);
    }

    public function testSeeUppercaseText(): void
    {
        $step = $this->getStep(['see', ['UPPER CASE']]);
        $output = $step->toString(200);
        $this->assertEquals('see "UPPER CASE"', $output);
    }

    public function testMultiByteTextLengthIsMeasuredCorrectly(): void
    {
        $step = $this->getStep(['see', ['ŽŽŽŽŽŽŽŽŽŽ', 'AAAAAAAAAAA']]);
        $output = $step->toString(30);
        $this->assertEquals('see "ŽŽŽŽŽŽŽŽŽŽ","AAAAAAAAAAA"', $output);
    }

    public function testAmOnUrl(): void
    {
        $step = $this->getStep(['amOnUrl', ['http://www.example.org/test']]);
        $output = $step->toString(200);
        $this->assertEquals('am on url "http://www.example.org/test"', $output);
    }

    public function testNoArgs(): void
    {
        $step = $this->getStep(['acceptPopup', []]);
        $output = $step->toString(200);
        $this->assertEquals('accept popup ', $output);
        $output = $step->toString(-5);
        $this->assertEquals('accept popup ', $output);

    }

    public function testSeeMultiLineStringInSingleLine(): void
    {
        $step = $this->getStep(['see', ["aaaa\nbbbb\nc"]]);
        $output = $step->toString(200);
        $this->assertEquals('see "aaaa\nbbbb\nc"', $output);
    }

    public function testFormattedOutput(): void
    {
        $argument = Stub::makeEmpty(\Codeception\Step\Argument\FormattedOutput::class);
        $argument->method('getOutput')->willReturn('some formatted output');

        $step = $this->getStep(['argument', [$argument]]);
        $output = $step->toString(200);
        $this->assertEquals('argument "some formatted output"', $output);
    }
}
