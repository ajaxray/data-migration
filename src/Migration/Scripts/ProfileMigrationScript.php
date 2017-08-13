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

class ProfileMigrationScript extends BaseMigrationScript
{
    public function execute()
    {
        $kms = $this->getConnection('kms');

        $users = $kms->executeQuery("SELECT id FROM bkash_users");

        $data = $users->fetchAll();

        $newData = [];
        foreach ($data as $row) {
            $newRow = $row;

            $newRow['user_id'] = $row['id'];
            $newData[] = $newRow;
        }

        $kms->beginTransaction();
        foreach ($newData as $row) {
            $row = $this->trim($row, ['user_id']);
            $kms->insert('bkash_profiles', $row);
        }
        $kms->commit();

        return count($data);
    }
}