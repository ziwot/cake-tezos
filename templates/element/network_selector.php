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
    Network::Mainnet->value => Network::Mainnet->label(),
    Network::Ghostnet->value => Network::Ghostnet->label(),
    Network::Local->value => Network::Local->label(),
], [
    'onchange' => 'this.form.submit()',
    'default' => $selectedNetwork,
]); ?>
<?= $this->Form->end() ?>
