<?php 
	# In the event of the application being already posted to and having created
   # a new resource, the endpoint should redirect a user to the new resource so
	# so they can immediately work with it.

	if (isset($result) && $result == 'success') {
		current_context()->response->getHeaders()->redirect(URLUtil::isLocal($_GET['returnto'])?: url('resource', 'index', $mnemonic->id));
		echo 'Redirecting...';
		return;
	}
	
?><div class="spacer medium"></div>

<?php if (isset($messages) && !empty($messages)): ?>

	<?php foreach($messages as $message): ?>
	<div class="row l1">
		<div class="span l1">
			<div class="message error">
				<div><?= __($message->getMessage()) ?></div>
			</div>
		</div>
	</div>
	<div class="spacer small"></div>
	<?php endforeach; ?>

<div class="spacer small"></div>
<?php endif; ?>

<div class="row l1">
	<div class="span l1">
		<h1>Editing mnemonic for <?= $resource->path() ?></h1>
	</div>
</div>

<div class="spacer medium"></div>

<div class="row l1">
	<div class="span l1">
		<div class="material">
			<div class="row l4">
				<div class="span l1"><h2>Mnemonic</h2></div>
				<div class="span l3">
					<form method="POST" action="">
						<div class="spacer small"></div>
						<div class="row l1">
							<div class="span l1">
								<input class="frm-ctrl" type="text" placeholder="Caption" name="caption" value="<?= $mnemonic? __($mnemonic->caption) : '' ?>" required minlength="3" maxlength="50">
								<div class="spacer small"></div>
								<textarea class="frm-ctrl" placeholder="Description" name="description" required minlength="3" maxlength="500"><?= $mnemonic? __($mnemonic->description) : '' ?></textarea>
								<div class="spacer small"></div>
								<div class="align-right">
									<input class="button" type="submit">
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>