<?php
namespace Codeception\Lib\Generator;

use Codeception\Configuration;
use Codeception\Util\Shared\Namespaces;
use Codeception\Util\Template;

class Test
{
    use Namespaces;
    use Shared\Classname;

    protected $template = <<<EOF
<?php 

{{namespace}}

class {{name}}Test extends \Codeception\Test\Unit
{
{{tester}}
    protected function _before()
    {
    }

    // tests
    public function testSomeFeature()
    {

    }
}
EOF;

    protected $testerTemplate = <<<EOF

    protected {{actorClass}} \${{actor}};
    
EOF;


    protected $settings;
    protected $name;

    public function __construct($settings, $name)
    {
        $this->settings = $settings;
        $this->name = $this->removeSuffix($name, 'Test');
    }

    public function produce()
    {
        $actor = $this->settings['actor'];

        $ns = $this->getNamespaceHeader($this->settings['namespace'] . '\\' . ucfirst($this->settings['suite']) . '\\' . $this->name);

        if ($ns) {
            $ns .= "\nuse ". $this->supportNamespace() . $actor.";";
        }


        $tester = '';
        if ($this->settings['actor']) {
            $tester = (new Template($this->testerTemplate))
            ->place('actorClass', $actor)
            ->place('actor', lcfirst(Configuration::config()['actor_suffix']))
            ->produce();
        }

        return (new Template($this->template))
            ->place('namespace', $ns)
            ->place('name', $this->getShortClassName($this->name))
            ->place('tester', $tester)
            ->produce();
    }
}
