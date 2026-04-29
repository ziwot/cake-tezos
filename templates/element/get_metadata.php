<?php
/**
 * @var \App\View\AppView $this
 * @var string $address
 * @var string $callBackUrl
 * @var string $csrfToken
 */

use CakeTezos\Domain\Network;

$selectedNetwork = $this->request->getSession()->read('CakeTezos.Network');
$network = Network::from($selectedNetwork);
?>

<?= $this->Form->button('Fetch metadata', [
    'id' => 'get_metadata',
    'class' => 'btn btn-primary',
]) ?>

<script type="module">
    import {
        getMetadata
    } from "CakeTezos";

    const connectBtn = document.getElementById("get_metadata");
    connectBtn?.addEventListener(
        "click",
        () => getMetadata(
            "<?= $address ?>",
            "<?= $callBackUrl ?>",
            "<?= $csrfToken ?>",
        )
    );
</script>
