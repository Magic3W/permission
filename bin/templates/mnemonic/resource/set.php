
<div class="spacer medium"></div>

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
								<input class="frm-ctrl" type="text" placeholder="Caption" name="caption" value="<?= $mnemonic? __($mnemonic->caption) : '' ?>">
								<div class="spacer small"></div>
								<textarea class="frm-ctrl" placeholder="Description" name="description"><?= $mnemonic? __($mnemonic->description) : '' ?></textarea>
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