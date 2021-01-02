<?php
namespace Codeception\Lib\Generator;

use Codeception\Util\Shared\Namespaces;
use Codeception\Util\Template;

class PageObject
{
    use Namespaces;
    use Shared\Classname;

    protected $template = <<<EOF
<?php
namespace {{namespace}};

class {{class}}
{
    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public \$usernameField = '#username';
     * public \$formSubmitButton = "#mainForm input[type=submit]";
     */

{{actions}}
}

EOF;

    protected $actionsTemplate = <<<EOF
    /**
     * @var \\{{actorClass}};
     */
    protected \${{actor}};

    public function __construct(\\{{actorClass}} \$I)
    {        
        \$this->{{actor}} = \$I;
        // you can inject other page objects here as well
    }

EOF;

    protected $actions = '';
    protected $settings;
    protected $name;
    protected $namespace;

    public function __construct($settings, $name)
    {
        $this->settings = $settings;
        $this->name = $this->getShortClassName($name);
        $this->namespace = $this->getNamespaceString($this->supportNamespace() . '\\Page\\' . $name);
    }

    public function produce()
    {
        return (new Template($this->template))
            ->place('namespace', $this->namespace)
            ->place('actions', $this->produceActions())
            ->place('class', $this->name)
            ->produce();
    }

    protected function produceActions()
    {
        if (!isset($this->settings['actor'])) {
            return ''; // global pageobject
        }

        $actor = lcfirst($this->settings['actor']);
        $actorClass = $this->supportNamespace() . '\\' . $this->settings['actor'];

        return (new Template($this->actionsTemplate))
            ->place('actorClass', $actorClass)
            ->place('actor', $actor)
            ->place('pageObject', $this->name)
            ->produce();
    }
}
