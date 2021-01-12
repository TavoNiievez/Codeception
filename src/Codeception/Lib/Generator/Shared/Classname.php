<?php
namespace Codeception\Lib\Generator\Shared;

trait Classname
{
    protected function removeSuffix($classname, $suffix)
    {
        $classname = preg_replace('~\.php$~', '', $classname);
        return preg_replace("~$suffix$~", '', $classname);
    }

    protected function supportNamespace()
    {
        if (!isset($this->settings)) {
            return "\\";
        }

        $namespace = "";

        if ($this->settings['namespace']) {
            $namespace .= '\\' . $this->settings['namespace'];
        }

        if (isset($this->settings['support_namespace'])) {
            $namespace .= '\\' . $this->settings['support_namespace'];
        }
        return rtrim($namespace, '\\') . '\\';
    }
}
