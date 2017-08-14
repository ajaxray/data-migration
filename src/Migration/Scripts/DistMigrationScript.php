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

class DistMigrationScript extends DBMigrationScript
{
    protected $inputOptions = [
        'connection' => 'kt',
        'table' => 'dist_map',
    ];

    protected $outputOptions = [
        'connection' => 'kms',
        'table' => 'bkash_distributors',
        'fields' => ['distributor_id', 'user_id', 'name', 'wallet_number', 'created_by', 'createdAt']
    ];

    protected function prepareTransformers()
    {
        $kms = $this->getConnection('kms');

        $this->addTransformer('seq', new SequenceTransformer([
            'column' => 'distributor_id',
            'initial' => 1000000000
        ]));

        $this->addTransformer('dateConverter', new DateTransformer([
            'from' => 'd-M-y h.i.s.u A',
            'fromCol' => 'ADDED_ON',
            'toCol' => 'createdAt'
        ]));

        $this->addTransformer('findCreatorByEmail', new DBLookupTransformer([
            'conn' => $kms,
            'table' => 'bkash_users',
            'field' => 'id',
            'matchField' => 'email',
            'matchWith' => 'ADDED_BY',
            'column' => 'created_by',
        ]));

        $this->addTransformer('findUserIdAndNameByEmail', new DBLookupTransformer([
            'conn' => $kms,
            'table' => 'bkash_users',
            'field' => ['id', 'full_name'],
            'matchField' => 'email',
            'matchWith' => 'EMAIL',
            'column' => ['user_id', 'name'],
        ]));

        $this->addTransformer('copyWallet', new CopyTransformer([
            'fields' => ['wallet_number' => 'WALLET']
        ]));

    }

}