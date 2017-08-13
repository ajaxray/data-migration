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

class DistMigrationScript extends BaseMigrationScript
{
    public function execute()
    {
        $kt = $this->getConnection('kt');
        $kms = $this->getConnection('kms');

        $dist = $kt->executeQuery("SELECT * FROM dist_map");

        $data = $dist->fetchAll();

        $newData = [];

        // Prepare Transformers
        $seq = new SequenceTransformer(['column' => 'distributor_id', 'initial' => 1000000000]);
        $dateConverter = new DateTransformer([
            'from' => 'd-M-y h.i.s.u A',
            'fromCol' => 'ADDED_ON',
            'toCol' => 'createdAt'
        ]);

        $findCreatorByEmail = new DBLookupTransformer([
            'conn' => $kms,
            'table' => 'bkash_users',
            'field' => 'id',
            'matchField' => 'email',
            'matchWith' => 'ADDED_BY',
            'column' => 'created_by',
        ]);

        $findUserIdAndNameByEmail = new DBLookupTransformer([
            'conn' => $kms,
            'table' => 'bkash_users',
            'field' => ['id', 'full_name'],
            'matchField' => 'email',
            'matchWith' => 'EMAIL',
            'column' => ['user_id', 'name'],
        ]);

        foreach ($data as $row) {
            $newRow = $row;

            $seq->transform($newRow);
            $dateConverter->transform($newRow);
            $findUserIdAndNameByEmail->transform($newRow);
            $findCreatorByEmail->transform($newRow);

            $newRow['wallet_number'] = $newRow['WALLET'];

            $newData[] = $newRow;
        }

        $kms->beginTransaction();
        foreach ($newData as $row) {
            $row = $this->trim($row, ['distributor_id', 'user_id', 'name', 'wallet_number', 'created_by', 'createdAt']);
            $kms->insert('bkash_distributors', $row);
        }
        $kms->commit();

        return count($data);
    }
}