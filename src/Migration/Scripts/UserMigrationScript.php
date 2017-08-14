<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 8:07 PM
 */

namespace Migration\Scripts;


use Migration\Transformers\CustomTransformer;
use Migration\Transformers\DateTransformer;
use Migration\Transformers\SequenceTransformer;

class UserMigrationScript extends DBMigrationScript
{
    protected $inputOptions = [
        'connection' => 'kt',
        'table' => 'users'
    ];

    protected $outputOptions = [
        'connection' => 'kms',
        'table' => 'bkash_users',
        'fields' => ['id', 'username', 'username_canonical', 'email', 'email_canonical', 'full_name', 'enabled', 'locked', 'salt', 'password', 'roles']
    ];

    protected function prepareTransformers()
    {
        $this->addTransformer('seq', new SequenceTransformer(['column' => 'id', 'initial' => 1000000000]));

        // Input Date: 11-JUN-13 02.13.32.000000 PM
        $this->addTransformer('dateConverter', new DateTransformer([
            'from' => 'd-M-y h.i.s.u A',
            'fromCol' => 'REGISTEREDON',
            'toCol' => 'created_at'
        ]));

        $this->addTransformer('createUsername', new CustomTransformer([
            'func' => function($row) {
                $row['username'] = substr($row['EMAIL'], 0, strpos($row['EMAIL'], '@'));
                $row['username_canonical'] = $row['username'];
                $row['email'] = $row['EMAIL'];
                $row['email_canonical'] = $row['EMAIL'];
                $row['full_name'] = $row['FIRSTNAME']. ' ' .$row['LASTNAME'];
                $row['enabled'] = 1;
                $row['locked'] = 0;
                $row['salt'] = 0;
                $row['password'] = $row['PASSWORD'];
                $row['roles'] = 'a:0:{}';
                return $row;
            }
        ]));
    }
}