<?php
use Cake\I18n\Time;
use Cake\Routing\Router;
?>

<?php if ($this->Identity->isLoggedIn()) : ?>
    Welcome, <?= $this->Identity->get('address') ?>
    <?= $this->Html->link(
        $this->Html->icon('power'),
        ['plugin' => 'CakeTezos', 'controller' => 'Users', 'action' => 'logout'],
        [
            'class' => 'btn',
            'escape' => false,
        ],
    ) ?>
<?php else : ?>
    <button id="connect" class="btn">
        <?= $this->Html->icon('key-fill') ?>
    </button>
<?php endif; ?>

<script type="importmap">
    {
        "imports": {
            "CakeTezos": "/cake_tezos/dist/cake-tezos.js?v=ff"
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
            "<?= Router::fullBaseUrl() ?>/cake-tezos",
            "<?= $this->request->getAttribute('csrfToken') ?>",
            "NetXdQprcVkpaWU",
            "<?= $statement ?? 'I accept the Terms of Service' ?>",
            "<?= random_int(1, 100000000) ?>",
            "<?= Time::now()->format(DateTimeImmutable::ATOM) ?>",
        )
    );
</script>
