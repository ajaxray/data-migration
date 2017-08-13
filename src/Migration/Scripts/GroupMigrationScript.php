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

class GroupMigrationScript extends BaseMigrationScript
{
    public function execute()
    {
        $kt = $this->getConnection('kt');
        $kms = $this->getConnection('kms');

        $users = $kms->executeQuery("SELECT id, email FROM bkash_users");

        $data = $users->fetchAll();

        $newData = [];

        // Prepare Transformers
        $findGroupName = new DBLookupTransformer([
            'conn' => $kt,
            'table' => 'users_groups',
            'field' => 'GROUPNAME',
            'matchField' => 'EMAIL',
            'matchWith' => 'email',
            'column' => 'GROUPNAME',
        ]);

        $findGroupId = new DBLookupTransformer([
            'conn' => $kms,
            'table' => 'bkash_user_group',
            'field' => 'id',
            'matchField' => 'name',
            'matchWith' => 'GROUPNAME',
            'column' => 'group_id',
        ]);

        foreach ($data as $row) {
            $newRow = $row;

            $findGroupName->transform($newRow);
            $findGroupId->transform($newRow);

            $newRow['user_id'] = $row['id'];
            $newData[] = $newRow;
        }

        $kms->beginTransaction();
        foreach ($newData as $row) {
            $row = $this->trim($row, ['group_id', 'user_id']);
            $kms->insert('bkash_user_groups', $row);
        }
        $kms->commit();

        return count($data);
    }
}