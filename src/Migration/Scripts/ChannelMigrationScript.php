<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 8:07 PM
 */

namespace Migration\Scripts;


use Migration\Transformers\CopyTransformer;
use Migration\Transformers\DateTransformer;
use Migration\Transformers\SequenceTransformer;

class ChannelMigrationScript extends DBMigrationScript
{
    protected $inputOptions = [
        'connection' => 'kt',
        'table' => 'channel_tree',
    ];

    protected $outputOptions = [
        'connection' => 'kms',
        'table' => 'bkash_channel',
        'fields' => ['id', 'agent_wallet_number', 'dso_wallet_number', 'master_wallet_number', 'agent_channel', 'agent_type', 'createdAt']
    ];

    protected function prepareTransformers()
    {
        $this->transformers['seq'] = new SequenceTransformer(['column' => 'id', 'initial' => 1000000000]);

        // Inout Date: 5/9/2016 7:00:36 AM
        $this->transformers['dateConverter'] = new DateTransformer([
            'from'    => 'j/n/Y g:i:s A',
            'fromCol' => 'INSERT_DT',
            'toCol'   => 'createdAt'
        ]);

        $this->transformers['copyToDestFields'] = new CopyTransformer([
            'fields'       => [
                'agent_wallet_number'  => 'AGENT',
                'dso_wallet_number'    => 'RA',
                'master_wallet_number' => 'MA_WALLET',
                'agent_channel'        => 'AGENT_CHANNEL',
                'agent_type'           => 'AGENT_TYPE',
            ],
            'removeSource' => true,
        ]);
    }
}