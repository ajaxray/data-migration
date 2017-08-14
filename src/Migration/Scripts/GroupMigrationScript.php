<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 8:07 PM
 */

namespace Migration\Scripts;


use Migration\Transformers\CopyTransformer;
use Migration\Transformers\DateTransformer;
use Migration\Transformers\DBLookupTransformer;
use Migration\Transformers\SequenceTransformer;

class GroupMigrationScript extends DBMigrationScript
{
    protected $inputOptions = [
        'connection' => 'kms',
        'table' => 'bkash_users',
        'fields' => ['id', 'email']
    ];

    protected $outputOptions = [
        'connection' => 'kms',
        'table' => 'bkash_user_groups',
        'fields' => ['group_id', 'user_id']
    ];

    protected function prepareTransformers()
    {
        $kt = $this->getConnection('kt');
        $kms = $this->getConnection('kms');

        $this->addTransformer('findGroupName', new DBLookupTransformer([
            'conn' => $kt,
            'table' => 'users_groups',
            'field' => 'GROUPNAME',
            'matchField' => 'EMAIL',
            'matchWith' => 'email',
            'column' => 'GROUPNAME',
            'onFail' => DBLookupTransformer::ON_FAIL_IGNORE
        ]));

        $this->addTransformer('findGroupId', new DBLookupTransformer([
            'conn' => $kms,
            'table' => 'bkash_user_group',
            'field' => 'id',
            'matchField' => 'name',
            'matchWith' => 'GROUPNAME',
            'column' => 'group_id',
        ]));

        $this->addTransformer('rename', new CopyTransformer([
            'fields' => ['user_id' => 'id'],
            'removeSource' => true
        ]));
    }
}