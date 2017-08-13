<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 8:07 PM
 */

namespace Migration\Scripts;


use Migration\Transformers\DateTransformer;
use Migration\Transformers\DBLookupTransformer;
use Migration\Transformers\SequenceTransformer;

class BarcodeMigrationScript extends BaseMigrationScript
{
    public function execute()
    {
        $kms = $this->getConnection('kms');
        $kt = $this->getConnection('kt');

        $numbers = $kt->executeQuery("SELECT * FROM authorized_tracking_no");

        $data = $numbers->fetchAll();
        $newData = [];

        // Input Date: 11-JUN-13 02.13.32.000000 PM
        $dateConverter = new DateTransformer([
            'from' => 'd-M-y h.i.s.u A',
            'fromCol' => 'CREATE_TIME',
            'toCol' => 'createdAt'
        ]);
        $findCreatorByEmail = new DBLookupTransformer([
            'conn' => $kms,
            'table' => 'bkash_users',
            'field' => 'id',
            'matchField' => 'email',
            'matchWith' => 'CREATED_BY',
            'column' => 'created_by',
        ]);

        foreach ($data as $row) {
            $newRow = $row;

            $dateConverter->transform($newRow);
            $findCreatorByEmail->transform($newRow);

            $newRow['barcode_number'] = $row['KYC_TRACKING_NO'];
            $newRow['registered'] = ('Y' == $row['REGISTERED']) ? 1 : 0;

            $newData[] = $newRow;
        }

        $kms->beginTransaction();
        foreach ($newData as $row) {
            $row = $this->trim($row, ['barcode_number', 'created_by', 'createdAt', 'registered']);
            $kms->insert('bkash_barcodes', $row);
        }
        $kms->commit();

        return count($data);
    }
}