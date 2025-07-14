<?php

use Cake\I18n\Time;
use Cake\Routing\Router;
use CakeTezos\Domain\Network;

$selectedNetwork = $this->request->getSession()->read('CakeTezos.Network');
$network = Network::from($selectedNetwork);
?>

<?php if ($this->Identity->isLoggedIn()) : ?>
    <div>
        Welcome, <?php echo $this->Tz->shortenAddress($this->Identity->get('address')) ?>
        (bal.
        <?php echo $this->cell(
            'CakeTezos.Balance',
            [],
            [
                'cache' => [
                    'config' => 'short',
                    'key' =>
                    sprintf(
                        'balance_%s_%s',
                        $network->network()['type'],
                        $this->Identity->get('address'),
                    ),
                ],
             ],
        ) ?>)
        <?php echo $this->Html->link(
            $this->Html->icon('power'),
            [
                'prefix' => false,
                'plugin' => 'CakeTezos',
                'controller' => 'Wallet',
                'action' => 'logout',
            ],
            [
                'class' => 'btn',
                'escape' => false,
            ],
        ) ?>
    </div>
<?php else : ?>
    <div>
        <button id="connect" class="btn">
            <?php echo $this->Html->icon('key-fill') ?>
        </button>
    </div>
<?php endif; ?>

<script type="importmap">
    {
        "imports": {
            "CakeTezos": "/cake_tezos/dist/cake-tezos.js?v=ddd"
        }
    }
</script>

<script type="module">
    import {
        connect
    } from "CakeTezos";

    const connectBtn = document.getElementById("connect");
    connectBtn?.addEventListener(
        "click",
        () => connect(
            <?= json_encode($network->network(), JSON_UNESCAPED_SLASHES) ?>,
            "<?= Router::fullBaseUrl() ?>/cake-tezos",
            "<?= $this->request->getAttribute('csrfToken') ?>",
            "<?= $network->networkId() ?>",
            "<?= $statement ?? 'I accept the Terms of Service' ?>",
            "<?= random_int(1, 100000000) ?>",
            "<?= Time::now()->format(DateTimeImmutable::ATOM) ?>",
        )
    );
</script>
