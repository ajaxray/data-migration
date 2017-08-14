<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 8:01 PM
 */

namespace Migration\Scripts;


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

    /**
     * Get the source/input data
     *
     * @param array $options
     * @return mixed
     */
    public abstract function input(array $options);

    /**
     * Apply Transformation and prepare data for output
     *
     * @param array $data
     * @param array $options
     * @return array Data rows
     */
    public abstract function prepare(array $data, array $options);

    /**
     * Push output to destination
     *
     * @param $data
     * @param array $options
     * @return int Number of rows
     */
    public abstract function output($data, array $options);

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