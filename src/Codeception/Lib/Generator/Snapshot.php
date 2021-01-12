<?php
namespace Codeception\Lib\Generator;

use Codeception\Lib\Generator\Shared\Classname;
use Codeception\Util\Shared\Namespaces;
use Codeception\Util\Template;

class Snapshot
{
    use Namespaces;
    use Classname;

    protected $template = <<<EOF
<?php

namespace {{namespace}};

class {{name}} extends \\Codeception\\Snapshot
{

{{actions}}

    protected function fetchData()
    {
        // TODO: return a value which will be used for snapshot 
    }
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
    }
EOF;

    protected $namespace;
    protected $name;
    protected $settings;

    public function __construct($settings, $name)
    {
        $this->settings = $settings;
        $this->name = $this->getShortClassName($name);
        $this->namespace = $this->getNamespaceString($this->supportNamespace() . 'Snapshot\\' . $name);
    }

    public function produce()
    {
        return (new Template($this->template))
            ->place('namespace', $this->namespace)
            ->place('actions', $this->produceActions())
            ->place('name', $this->name)
            ->produce();
    }

    protected function produceActions()
    {
        if (!isset($this->settings['actor'])) {
            return ''; // no actor in suite
        }

        $actor = lcfirst($this->settings['actor']);
        $actorClass = rtrim($this->supportNamespace(), '\\') . $this->settings['actor'];

        return (new Template($this->actionsTemplate))
            ->place('actorClass', $actorClass)
            ->place('actor', $actor)
            ->produce();
    }
}
