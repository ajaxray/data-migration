<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 9:05 PM
 */

namespace Migration\Transformers;


interface TransformerInterface
{
    public function transform(array &$row = []);
}