<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 8:59 PM
 */

namespace Migration\Transformers;


class DateTransformer implements TransformerInterface
{
    private $from, $to, $fromCol, $toCol;
    /**
     * DateTransformer constructor.
     *
     * Following options are expected in $config
     *    - from : format of input string. To be used in \DateTime::createFromFormat
     *    - to : output date format, default is 'Y-m-d H:i:s'
     *    - fromCol : Input column name
     *    - toCol : new column name. If not provided, value of fromCol will be overridden
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->from = $config['from'];
        $this->to = isset($config['to']) ? $config['to'] : 'Y-m-d H:i:s';

        $this->fromCol = $config['fromCol'];
        $this->toCol = isset($config['toCol']) ? $config['toCol'] : $this->fromCol;
    }

    public function transform(array &$row = [])
    {
        $date = \DateTime::createFromFormat($this->from, $row[$this->fromCol]);
        $row[$this->toCol] = $date->format($this->to);
    }
}