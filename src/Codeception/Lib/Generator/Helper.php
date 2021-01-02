<?php
namespace Codeception\Lib\Generator;

use Codeception\Util\Shared\Namespaces;
use Codeception\Util\Template;

class Helper
{
    use Namespaces;

    protected $template = <<<EOF
<?php
{{namespace}}
// here you can define custom actions
// all public methods declared in helper class will be available in \$I

class {{name}} extends \\Codeception\\Module
{

}

EOF;

    protected $namespace;
    protected $name;
    private $settings;

    public function __construct($settings, $name)
    {
        $this->settings = $settings;
        $this->name = $name;
    }

    public function produce()
    {
        return (new Template($this->template))
            ->place('namespace', $this->getNamespaceHeader($this->namespace . '\\Helper\\' . $this->name))
            ->place('name', $this->getShortClassName($this->name))
            ->produce();
    }

    public function getHelperName()
    {
        return rtrim('\\' . $this->namespace, '\\') . '\\Helper\\' . $this->name;
    }
}
