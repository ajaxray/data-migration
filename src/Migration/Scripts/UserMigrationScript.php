<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 8:07 PM
 */

namespace Migration\Scripts;


use Migration\Transformers\DateTransformer;
use Migration\Transformers\SequenceTransformer;

class UserMigrationScript extends BaseMigrationScript
{
    public function execute()
    {
        $source = $this->getConnection('kt');
        $users = $source->executeQuery("SELECT * FROM users");

        $data = $users->fetchAll();

        $newData = [];

        // Prepare Transformers
        $seq = new SequenceTransformer(['column' => 'id', 'initial' => 1000000000]);
        // Input Date: 11-JUN-13 02.13.32.000000 PM
        $dateConverter = new DateTransformer([
            'from' => 'd-M-y h.i.s.u A',
            'fromCol' => 'REGISTEREDON',
            'toCol' => 'created_at'
        ]);

        foreach ($data as $row) {
            $newRow = $row;

            $seq->transform($newRow);
            $dateConverter->transform($newRow);

            $newRow['username'] = substr($row['EMAIL'], 0, strpos($row['EMAIL'], '@'));
            $newRow['username_canonical'] = $newRow['username'];
            $newRow['email'] = $newRow['EMAIL'];
            $newRow['email_canonical'] = $newRow['EMAIL'];
            $newRow['full_name'] = $row['FIRSTNAME']. ' ' .$row['LASTNAME'];
            $newRow['enabled'] = 1;
            $newRow['locked'] = 0;
            $newRow['salt'] = 0;
            $newRow['password'] = $row['PASSWORD'];
            $newRow['roles'] = 'a:0:{}';

            $newData[] = $newRow;
        }

        $dest = $this->getConnection('kms');
        $dest->beginTransaction();
        foreach ($newData as $row) {
            $row = $this->trim($row, ['PASSWORD', 'FIRSTNAME', 'LASTNAME', 'EMAIL', 'REGISTEREDON'], false);
            $dest->insert('bkash_users', $row);
        }
        $dest->commit();
    }
}