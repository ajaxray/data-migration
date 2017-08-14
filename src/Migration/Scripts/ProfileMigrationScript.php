<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 8:07 PM
 */

namespace Migration\Scripts;


use Migration\Transformers\CopyTransformer;

class ProfileMigrationScript extends DBMigrationScript
{
    protected $inputOptions = [
        'connection' => 'kms',
        'table' => 'bkash_users',
        'fields' => ['id']
    ];

    protected $outputOptions = [
        'connection' => 'kms',
        'table' => 'bkash_profiles',
        'fields' => ['user_id']
    ];

    protected function prepareTransformers()
    {
        $this->addTransformer('rename', new CopyTransformer([
            'fields' => ['user_id' => 'id'],
            'removeSource' => true
        ]));
    }
}