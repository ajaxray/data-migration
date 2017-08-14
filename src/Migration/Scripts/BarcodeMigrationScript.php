<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 8:07 PM
 */

namespace Migration\Scripts;


use Migration\Transformers\CustomTransformer;
use Migration\Transformers\DateTransformer;
use Migration\Transformers\DBLookupTransformer;
use Migration\Transformers\SequenceTransformer;

class BarcodeMigrationScript extends DBMigrationScript
{
    protected $inputOptions = [
        'connection' => 'kt',
        'table' => 'authorized_tracking_no',
    ];

    protected $outputOptions = [
        'connection' => 'kms',
        'table' => 'bkash_barcodes',
        'fields' => ['barcode_number', 'created_by', 'createdAt', 'registered']
    ];

    protected function prepareTransformers()
    {
        $kms = $this->getConnection('kms');

        // Input Date: 11-JUN-13 02.13.32.000000 PM
        $this->addTransformer('dateConverter', new DateTransformer([
            'from' => 'd-M-y h.i.s.u A',
            'fromCol' => 'CREATE_TIME',
            'toCol' => 'createdAt'
        ]));

        $this->addTransformer('findCreatorByEmail', new DBLookupTransformer([
            'conn' => $kms,
            'table' => 'bkash_users',
            'field' => 'id',
            'matchField' => 'email',
            'matchWith' => 'CREATED_BY',
            'column' => 'created_by',
            'onFail' => DBLookupTransformer::ON_FAIL_NULL
        ]));

        $this->addTransformer('createUsername', new CustomTransformer([
            'func' => function($row) {
                $row['barcode_number'] = $row['KYC_TRACKING_NO'];
                $row['registered'] = ('Y' == $row['REGISTERED']) ? 1 : 0;
                return $row;
            }
        ]));
    }

}