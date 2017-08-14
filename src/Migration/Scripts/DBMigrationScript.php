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
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;

abstract class DBMigrationScript extends BaseMigrationScript
{
    protected $config;
    protected $connections = [];

    protected $transformers = [];
    protected $inputOptions = [];
    protected $outputOptions = [];
    protected $offset = 0;
    protected $batch = 0;

    /**
     * @var QueryBuilder
     */
    protected $qb;

    /**
     * @var Statement
     */
    protected $stmt;

    /**
     * ScriptCommand constructor.
     * @param $config
     */
    public function __construct($config)
    {
        parent::__construct($config);
        $this->prepareTransformers();
    }

    protected function prepareTransformers()
    {
        // OPTIONAL Initialize your transformers and put in $this->transformers
        // This will help to transform all data rows using default DBMigrationScript::prepare
    }

    /**
     * @inheritdoc
     */
    public function input(array $options)
    {
        $options = $this->inputOptions + $options;
        $this->batch = isset($options['batch']) ? intval($options['batch']) : false;

        $data = $this->fetchData($options);

        if($this->batch) {
            $this->offset += $this->batch;
        }

        return $data;
    }

    /**
     * Apply Transformation and prepare data for output
     *
     * You can Initiate your transformers by overwriting DBMigrationScript::prepareTransformers
     * to be used by this method.
     * Otherwise you can totally overwrite this function as per your need
     *
     * @param array $data Input data as array
     * @param array $options
     * @return array Data rows
     */
    public function prepare(array $data, array $options)
    {
        $newData = [];

        foreach ($data as $row) {
            $newRow = $row;
            foreach ($this->transformers as $transformer) {
                $transformer->transform($newRow);
            }
            $newData[] = $newRow;
        }

        return $newData;
    }

    /**
     * @inheritdoc
     */
    public function output($data, array $options)
    {
        $options = $this->outputOptions + $options;
        return $this->insert($options['connection'], $data, $options);
    }

    protected function fetchData(array $options)
    {
        if(is_null($this->qb)) {
            $this->qb = $this->createQueryBuilder($options);
        }

        if($this->batch) {
            $this->qb
                ->setFirstResult($this->offset)
                ->setMaxResults($this->batch);
        }

        return $this->qb->execute()->fetchAll();
    }

    /**
     * Create simple query builder with database and table
     * If you need complex query or joins, you can modify returned QueryBuilder
     * or can make a QueryBuilder yourself
     *
     * @param array $options Required - table. Optional - fields
     *
     * @return QueryBuilder
     */
    protected function createQueryBuilder(array $options)
    {
        $conn = $this->getConnection($options['connection']);
        $queryBuilder = $conn->createQueryBuilder();

        $fields = isset($options['fields']) ? $options['fields'] : ['*'];

        return $queryBuilder
            ->select(...$fields)
            ->from($options['table'])
        ;
    }

    /**
     * @param $connName
     * @param $data
     * @param array $options
     *
     * @return int Effected rows
     */
    protected function insert($connName, $data, array $options)
    {
        $conn = $this->getConnection($connName);
        $effected = 0;

        $conn->beginTransaction();
        foreach ($data as $row) {
            $row = $this->trim($row, $options['fields']);
            $effected += $conn->insert($options['table'], $row);
        }
        $conn->commit();

        return $effected;
    }


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
}