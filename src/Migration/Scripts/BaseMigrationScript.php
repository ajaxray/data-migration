<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 8:01 PM
 */

namespace Migration\Scripts;


use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

abstract class BaseMigrationScript
{
    protected $config;
    protected $connections = [];

    /**
     * ScriptCommand constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }


    abstract function execute();

    /**
     * @param string $name
     *
     * @return Connection
     */
    protected function getConnection($name)
    {
        if(isset($this->connections[$name])) {
            return $this->connections[$name];
        } elseif (isset($this->config['connections'][$name])) {
            $config = new Configuration();
            $this->connections[$name] = DriverManager::getConnection($this->config['connections'][$name], $config);

            return $this->connections[$name];
        }

        throw new \InvalidArgumentException("Connection name '$name' was not defined in configuration.");
    }

    /**
     * @param array $data
     * @param array $fields
     * @param bool $keepFields if true, keep only mentioned fields. opposite otherwise
     *
     * @return array
     */
    protected function trim(array $data, array $fields, $keepFields = true)
    {
        if($keepFields) {
            return array_intersect_key($data, array_flip($fields));
        } else {
            return array_diff_key($data, array_flip($fields));
        }
    }

}