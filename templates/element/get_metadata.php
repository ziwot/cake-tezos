<?php
/**
 * @var \App\View\AppView $this
 * @var string $address
 * @var string $callBackUrl
 * @var string $csrfToken
 * @var string $successHandler
 * @var string $errorHandler
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

    const fetchMetadataBtn = document.getElementById('get_metadata');
    fetchMetadataBtn?.addEventListener(
        "click",
        async () => {
            try {
                const response = await getMetadata(
                    "<?= $network->rpcUrl() ?>",
                    "<?= $address ?>",
                    "<?= $callBackUrl ?>",
                    "<?= $csrfToken ?>",
            )
                <?= $successHandler ?>(response);
            } catch (error) {
                <?= $errorHandler ?>(error);
            }
        }
    );
</script>
