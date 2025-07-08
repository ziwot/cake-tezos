<?php

use CakeTezos\Domain\Network;

$selectedNetwork = $this->request->getSession()->read('CakeTezos.Network');
?>

<?= $this->Form->create(null, ['url' => [
    'plugin' => 'CakeTezos',
    'controller' => 'Network',
    'action' => 'select',
    '_method' => 'post'
]]) ?>

<?= $this->Form->select('network', [
    Network::Mainnet->value,
    Network::Ghostnet->value,
    Network::Local->value,
], [
    'onchange' => 'this.form.submit()',
    'default' => $selectedNetwork,
]); ?>
<?= $this->Form->end() ?>
