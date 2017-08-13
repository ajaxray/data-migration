<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 8:59 PM
 */

namespace Migration\Transformers;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;

class DBLookupTransformer implements TransformerInterface
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var Statement
     */
    private $stmt;

    private $table, $field, $matchField, $matchWith, $column;

    /**
     * DBLookupTransformer constructor.
     *
     * Following options are required in $config
     *    - conn : The Connection object
     *    - table : Name of the table select from
     *    - field : Field name(s) to select from table, single or array
     *    - matchField : The field name to use with WHERE clause
     *    - matchWith : The key of current row to be used as value of WHERE clause
     *    - column : New column(s) to create from matched row. single are array (must be same with "field")
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->conn = $config['conn'];
        $this->table = $config['table'];
        $this->field = $config['field'];
        $this->matchField = $config['matchField'];
        $this->matchWith = $config['matchWith'];
        $this->column = $config['column'];

        $field = is_array($this->field) ? implode(', ', $this->field) : $this->field;
        $this->stmt = $this->conn->prepare("SELECT $field FROM {$this->table} WHERE {$this->matchField} = ? LIMIT 1");
    }

    public function transform(array &$row = [])
    {
        $this->stmt->bindValue(1, $row[$this->matchWith]);
        $this->stmt->execute();

        if(is_array($this->column)) {
            // Create columns with names in $this->column sequentially
            foreach ($this->stmt->fetch(\PDO::FETCH_NUM) as $i => $val) {
                $row[$this->column[$i]] = $val;
            }
        } else {
            $row[$this->column] = $this->stmt->fetchColumn();
        }


    }
}