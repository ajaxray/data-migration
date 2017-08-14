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
    const ON_FAIL_IGNORE = 'ignore';
    const ON_FAIL_EMPTY = '';
    const ON_FAIL_ZERO = 0;
    const ON_FAIL_NULL = null;

    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var Statement
     */
    private $stmt;

    private $table, $field, $matchField, $matchWith, $column, $onFail;

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
     *    - onFail : What should do if lookup failed? Should be one of self::ON_FAIL_* constants
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

        $this->onFail = isset($config['onFail'])? $config['onFail'] : self::ON_FAIL_NULL;

        $field = is_array($this->field) ? implode(', ', $this->field) : $this->field;
        $this->stmt = $this->conn->prepare("SELECT $field FROM {$this->table} WHERE {$this->matchField} = ? LIMIT 1");
    }

    public function transform(array &$row = [])
    {
        $this->stmt->bindValue(1, $row[$this->matchWith]);
        $this->stmt->execute();

        if($this->stmt->rowCount()) {
            // Found lookup row

            if(is_array($this->column)) {
                // Create columns with names in $config['column'] sequentially
                foreach ($this->stmt->fetch(\PDO::FETCH_NUM) as $i => $val) {
                    $row[$this->column[$i]] = $val;
                }
            } else {
                $row[$this->column] = $this->stmt->fetchColumn();
            }
        } else {

            // No matching row found for lookup
            if($this->onFail == self::ON_FAIL_IGNORE) {
                $row = null;
            } else {
                $row[$this->column] = $this->onFail;
            }
        }




    }
}