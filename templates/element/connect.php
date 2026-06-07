<?php
/**
 * @var \App\View\AppView $this
 * @var mixed $statement
 * @var mixed $redirectUrl
 */

use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\Routing\Router;
use CakeTezos\Domain\Network;

$selectedNetwork = $this->request->getSession()->read('CakeTezos.Network');
$network = Network::from($selectedNetwork);
$statement = $statement ?? Configure::read('CakeTezos.siwt.statement');
$redirectUrl = $redirectUrl ?? Configure::read('CakeTezos.redirect.afterLogin');
?>

<?php if ($this->Identity->isLoggedIn()) : ?>
    <div>
        Welcome, <span id="address-display">
            <?= $this->Tz->shortenAddress($this->Identity->get('address')) ?>
        </span>
        <button id="copy-address" class="btn p-0" title="Copy address"
            data-address="<?= h($this->Identity->get('address')) ?>">
            <?= $this->Html->icon('files') ?>
        </button>
        (bal.
        <?= $this->cell('CakeTezos.Balance') ?>)
        <?= $this->Form->postLink(
            $this->Html->icon('arrow-repeat'),
            [
                'prefix' => false,
                'plugin' => 'CakeTezos',
                'controller' => 'Wallet',
                'action' => 'refreshBalance',
                '_method' => 'post',
            ],
            [
                'class' => 'btn',
                'escape' => false,
                'title' => 'Refresh balance',
            ],
        ) ?>
        <?= $this->Html->link(
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
            <?= $this->Html->icon('key-fill') ?>
        </button>
    </div>
<?php endif; ?>

<script type="module">
    import {
        connect
    } from "CakeTezos";

    const copyBtn = document.getElementById("copy-address");
    copyBtn?.addEventListener("click", () => {
        navigator.clipboard.writeText(copyBtn.dataset.address).catch(() => {});
    });

    const connectBtn = document.getElementById("connect");
    connectBtn?.addEventListener(
        "click",
        () => connect(
            <?= json_encode($network->network(), JSON_UNESCAPED_SLASHES) ?>,
            "<?= Router::fullBaseUrl() ?>/cake-tezos",
            "<?= $this->request->getAttribute('csrfToken') ?>",
            "<?= $network->networkId() ?>",
            "<?= $statement ?>",
            "<?= random_int(1, 100000000) ?>",
            "<?= Time::now()->format(DateTimeImmutable::ATOM) ?>",
            "<?= $redirectUrl ?>",
        )
    );
</script>
