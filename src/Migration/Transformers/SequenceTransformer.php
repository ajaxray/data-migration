<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 8:59 PM
 */

namespace Migration\Transformers;


class SequenceTransformer implements TransformerInterface
{
    private $val, $initial, $increment = 0;
    private $column;

    /**
     * SequenceTransformer constructor.
     *
     * Following options are acceptable in $config
     *    - initial : Starting value of sequence (default 1)
     *    - increment : Increment by (default 1)
     *    - column : Column name to set value (default 'id')
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->initial = isset($config['initial'])? $config['initial'] : 1;
        $this->increment = isset($config['increment'])? $config['increment'] : 1;
        $this->column = isset($config['column'])? $config['column'] : 'id';

        $this->val = $this->initial - $this->increment;
    }

    public function transform(array &$row = [])
    {
        $this->val += $this->increment;
        $row[$this->column] = $this->val;
    }
}