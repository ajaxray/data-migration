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

class ChannelMigrationScript extends BaseMigrationScript
{
    public function execute()
    {
        $kms = $this->getConnection('kms');
        $kt = $this->getConnection('kt');

        $channels = $kt->executeQuery("SELECT * FROM channel_tree");

        $data = $channels->fetchAll();
        $newData = [];

        $seq = new SequenceTransformer(['column' => 'id', 'initial' => 1000000000]);
        // Inout Date: 5/9/2016 7:00:36 AM
        $dateConverter = new DateTransformer([
            'from' => 'j/n/Y g:i:s A',
            'fromCol' => 'INSERT_DT',
            'toCol' => 'createdAt'
        ]);

        $copyToDestFields = new CopyTransformer([
            'fields' => [
                'agent_wallet_number' => 'AGENT',
                'dso_wallet_number' => 'RA',
                'master_wallet_number' => 'MA_WALLET',
                'agent_channel' => 'AGENT_CHANNEL',
                'agent_type' => 'AGENT_TYPE',
            ],
            'removeSource' => true,
        ]);
        foreach ($data as $row) {
            $newRow = $row;

            $seq->transform($newRow);
            $dateConverter->transform($newRow);
            $copyToDestFields->transform($newRow);

            $newData[] = $newRow;
        }


        $kms->beginTransaction();
        foreach ($newData as $row) {
            $row = $this->trim($row, ['id', 'agent_wallet_number', 'dso_wallet_number', 'master_wallet_number', 'agent_channel', 'agent_type', 'createdAt']);
            $kms->insert('bkash_channel', $row);
        }
        $kms->commit();

        return count($data);
    }
}