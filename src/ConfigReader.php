<?php


namespace LizardsAndPumpkins;

interface ConfigReader
{
    /**
     * @param string $configKey
     * @return bool
     */
    public function has($configKey);

    /**
     * @param string $configKey
     * @return null|string
     */
    public function get($configKey);
}