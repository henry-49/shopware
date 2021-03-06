<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */

/**
 * Allows to register multiple plugins from a namespace over an prefix path.
 *
 * The Enlight_Plugin_Namespace_Loader iterates over the specified directory and
 * checks whether bootstrap files are present. If a bootstrap is found loaded via
 * the Enlight_Loader the corresponding class.
 *
 * @package    Enlight_Plugin
 */
class Enlight_Plugin_Namespace_Loader extends Enlight_Plugin_Namespace
{
    /**
     * @var array List of all added prefix paths
     */
    protected $prefixPaths = [];

    /**
     * Returns the instance of the passed plugin name.
     *
     * @param string $name
     * @param bool   $throwException
     *
     * @return Enlight_Plugin_Bootstrap|null
     */
    public function get($name, $throwException = true)
    {
        if (!$this->plugins->offsetExists($name)) {
            $this->load($name, $throwException);
        }

        $plugin = $this->plugins->offsetGet($name);
        if ($plugin instanceof Enlight_Plugin_Bootstrap) {
            return $plugin;
        }

        if ($throwException) {
            throw new Enlight_Exception('Plugin "' . $name . '" in namespace "' . $this->getName() . '" not found');
        }

        return null;
    }

    /**
     * Adds a prefix path to the plugin namespace. Is used to autoload plugins.
     *
     * @param string $prefix
     * @param string $path
     *
     * @return Enlight_Plugin_Namespace
     */
    public function addPrefixPath($prefix, $path)
    {
        $prefix = trim($prefix, '_');
        $path = Enlight_Loader::realpath($path) . DIRECTORY_SEPARATOR;
        $this->prefixPaths[$path] = $prefix;

        return $this;
    }

    /**
     * Loads a plugin in the plugin namespace by name.
     *
     * @param string $name
     * @param bool   $throwException
     *
     * @throws Enlight_Exception
     *
     * @return Enlight_Plugin_PluginCollection
     */
    public function load($name, $throwException = true)
    {
        if ($this->plugins->offsetExists($name)) {
            return $this;
        }
        foreach ($this->prefixPaths as $path => $prefix) {
            $file = $path . $name . DIRECTORY_SEPARATOR . 'Bootstrap.php';
            if (!file_exists($file)) {
                continue;
            }
            $this->initPlugin($name, $prefix, $file);

            return $this;
        }
        if ($throwException) {
            throw new Enlight_Exception('Plugin "' . $name . '" in namespace "' . $this->getName() . '" not found');
        }
    }

    /**
     * Loads all plugins in the plugin namespace. Iterate the prefix paths and looking for bootstrap files.
     */
    public function loadAll()
    {
        foreach ($this->prefixPaths as $path => $prefix) {
            foreach (new DirectoryIterator($path) as $dir) {
                if (!$dir->isDir() || $dir->isDot()) {
                    continue;
                }
                $file = $dir->getPathname() . DIRECTORY_SEPARATOR . 'Bootstrap.php';
                if (!file_exists($file)) {
                    continue;
                }
                $name = $dir->getFilename();
                $this->initPlugin($name, $prefix, $file);
            }
        }

        return $this;
    }

    /**
     * @return Enlight_Controller_Plugins_Json_Bootstrap
     */
    public function Json()
    {
        $jsonPlugin = $this->get(__FUNCTION__);
        if (!$jsonPlugin instanceof Enlight_Controller_Plugins_Json_Bootstrap) {
            throw new Enlight_Exception('Plugin "Enlight_Controller_Plugins_Json_Bootstrap" not found');
        }

        return $jsonPlugin;
    }

    /**
     * @return Enlight_Controller_Plugins_ViewRenderer_Bootstrap
     */
    public function ViewRenderer()
    {
        $viewRendererPlugin = $this->get(__FUNCTION__);
        if (!$viewRendererPlugin instanceof Enlight_Controller_Plugins_ViewRenderer_Bootstrap) {
            throw new Enlight_Exception('Plugin "Enlight_Controller_Plugins_ViewRenderer_Bootstrap" not found');
        }

        return $viewRendererPlugin;
    }

    /**
     * @return Enlight_Controller_Plugins_ScriptRenderer_Bootstrap
     */
    public function ScriptRenderer()
    {
        $scriptRendererPlugin = $this->get(__FUNCTION__);
        if (!$scriptRendererPlugin instanceof Enlight_Controller_Plugins_ScriptRenderer_Bootstrap) {
            throw new Enlight_Exception('Plugin "Enlight_Controller_Plugins_ScriptRenderer_Bootstrap" not found');
        }

        return $scriptRendererPlugin;
    }

    /**
     * @return Enlight_Controller_Plugins_JsonRequest_Bootstrap
     */
    public function JsonRequest()
    {
        $jsonRequestPlugin = $this->get(__FUNCTION__);
        if (!$jsonRequestPlugin instanceof Enlight_Controller_Plugins_JsonRequest_Bootstrap) {
            throw new Enlight_Exception('Plugin "Enlight_Controller_Plugins_JsonRequest_Bootstrap" not found');
        }

        return $jsonRequestPlugin;
    }

    /**
     * Instantiates a plugin from the plugin namespace and adds it to the internal plugins array.
     *
     * @param string      $name
     * @param string      $prefix
     * @param string|null $file
     *
     * @return Enlight_Plugin_Namespace_Loader
     */
    protected function initPlugin($name, $prefix, $file = null)
    {
        /** @var class-string<Enlight_Plugin_Bootstrap|Enlight_Plugin_Namespace> $class */
        $class = implode('_', [$prefix, $name, 'Bootstrap']);
        if (!class_exists($class, false)) {
            Shopware()->Loader()->loadClass($class, $file);
        }

        $plugin = new $class($name, $this);
        $this->plugins[$name] = $plugin;

        return $this;
    }
}
