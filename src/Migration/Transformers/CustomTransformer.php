<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 8:59 PM
 */

namespace Migration\Transformers;


class CustomTransformer implements TransformerInterface
{
    /**
     * @var \Closure
     */
    private $func;

    /**
     * CustomTransformer constructor.
     *
     * Following options are required in $config
     *    - func : A closure function that receives a row and returns updated row
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->func = $config['func'];
    }

    public function transform(array &$row = [])
    {
        $row = call_user_func($this->func, $row);
    }
}