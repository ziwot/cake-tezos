<?php

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
            "CakeTezos": "/js/cake-tezos.js"
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
            "<?= Router::fullBaseUrl() ?>",
            "<?= $this->request->getAttribute('csrfToken') ?>"
        )
    );
</script>
