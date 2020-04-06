<div class="spacer medium"></div>

<div class="row l1">
	<div class="span l1">
		<h1>Identities</h1>
	</div>
</div>

<div class="spacer medium"></div>

<div class="row l1">
	<div class="span l1">
		<div class="material">
			<div class="row l4">
				<div class="span l1"><h2>Identities</h2></div>
				<div class="span l3">
					<div class="spacer medium"></div>
					<?php foreach ($identities as $identity): ?>
					<a class="text:grey-400 no-decoration" href="<?= url('mnemonic', 'identity', 'set', $identity->_id) ?>">
						<?= $identity->mnemonic()? $identity->mnemonic()->caption : $identity->name ?>
						<span class="text:grey-600">(<?= $identity->name; ?>)</span>
					</a>
					<div class="spacer small"></div>
					<?php endforeach; ?>
				</div>
			</div>
			
			<div class="spacer medium"></div>
			<div class="separator"></div>
			<div class="spacer small"></div>
			
			<?= $pages ?>
		</div>
	</div>
</div>

<div class="spacer huge"></div>
