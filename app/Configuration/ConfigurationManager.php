<?php

namespace App\Configuration;


class ConfigurationManager implements \ArrayAccess
{

    /**
     * @var
     */
    private $configDirectory;

    /**
     * @var array
     */
    private $configuration;

    /**
     * ConfigurationManager constructor.
     * @param string $configDirectory
     */
    public function __construct(string $configDirectory)
    {
        $this->configDirectory = $configDirectory;
        $this->loadConfigs();
    }


    private function loadConfigs()
    {
        $files = scandir($this->configDirectory);
        foreach ($files as $file) {
            $fileName = basename($file, '.php');
            if (!strpos($file, ".") === false) {
                if (file_exists($this->configDirectory . '/' . $file)) {
                    $this->configuration[$fileName] = require $this->configDirectory . '/' . $file;
                }
            }
        }
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->configuration[$offset]);
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->configuration[$offset];
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->configuration[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->configuration[$offset]);
    }
}