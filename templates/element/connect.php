<?php

use Cake\I18n\Time;
use Cake\Routing\Router;
use CakeTezos\Domain\Network;

$selectedNetwork = $this->request->getSession()->read('CakeTezos.Network');
$networkId = Network::from($selectedNetwork)->networkId();
$network = Network::from($selectedNetwork)->network();
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
                    'balance_' .
                        $this->Identity->get('address'),
                ],
            ],
        ) ?>)
        <?php echo $this->Html->link(
            $this->Html->icon('power'),
            [
                'prefix' => false,
                'plugin' => 'CakeTezos',
                'controller' => 'Wallet',
                'action' => 'logout'
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
            "CakeTezos": "/cake_tezos/dist/cake-tezos.js?v=bbb"
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
            <?= json_encode($network, JSON_UNESCAPED_SLASHES) ?>,
            "<?= Router::fullBaseUrl() ?>/cake-tezos",
            "<?= $this->request->getAttribute('csrfToken') ?>",
            "<?= $networkId ?>",
            "<?= $statement ?? 'I accept the Terms of Service' ?>",
            "<?= random_int(1, 100000000) ?>",
            "<?= Time::now()->format(DateTimeImmutable::ATOM) ?>",
        )
    );
</script>
