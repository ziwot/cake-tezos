<?php
/**
 * @var \App\View\AppView $this
 */

use CakeTezos\Domain\Network;

$selectedNetwork = $this->request->getSession()->read('CakeTezos.Network');

$networks = [];
foreach (Network::configured() as $key => $config) {
    $networks[$key] = $config['label'];
}
?>

<?= $this->Form->create(null, ['url' => [
    'prefix' => false,
    'plugin' => 'CakeTezos',
    'controller' => 'Network',
    'action' => 'select',
    '_method' => 'post',
]]) ?>

<?= $this->Form->select('network', $networks, [
    'onchange' => 'this.form.submit()',
    'default' => $selectedNetwork,
]); ?>
<?= $this->Form->end();
