<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 8:59 PM
 */

namespace Migration\Transformers;


class CopyTransformer implements TransformerInterface
{
    private $fields = [];
    private $removeSource = false;

    /**
     * CopyTransformer constructor.
     *
     * Following options are required in $config
     *    - fields : Associative array of fields to copy. New column as key and source as value.
     *    - removeSource : If set to true, source column will be removed
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->fields = $config['fields'];
        if(isset($config['removeSource'])) {
            $this->removeSource = boolval($config['removeSource']);
        }
    }

    public function transform(array &$row = [])
    {
        foreach ($this->fields as $newCol => $from) {
            $row[$newCol] = $row[$from];

            if($this->removeSource) {
                unset($row[$from]);
            }
        }
    }
}