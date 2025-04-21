<?php

use Cake\Routing\Router;
?>

<?php if ($this->Identity->isLoggedIn()): ?>
	<?= $this->Identity->get('pkh') ?>
<?php else: ?>
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

	const button = document.getElementById("connect");
	button?.addEventListener(
		"click",
		() => connect(
			"<?= Router::fullBaseUrl() ?>",
			"<?= $this->request->getAttribute('csrfToken') ?>"
		)
	);
</script>
