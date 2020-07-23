

<div class="spacer medium"></div>

<div class="row l1">
	<div class="span l1">
		<div class="material">
			<div class="row l4">
				<div class="span l1">
					<h2>Setup</h2>
				</div>
				<div class="span l3">
					<div class="spacer medium"></div>
					
					
					<div class="text:grey-300">
						Select the applications that you would like to initialize with
						permissions. When you initialize an application, this application
						will receive a resource and permissions to write to the resource 
						and grant permissions to the resource.
					</div>
					
					
					<div class="spacer medium"></div>
					
					<form method="POST" action="">
						<?php foreach ($apps as $app): ?>
						<div class="row l10">
							<div class="span l1">
								<img src="<?= $app->icon->xl ?>">
							</div>
							<div class="span l8">
								<?= __($app->name) ?>
								<div class="text:grey-500" style="font-size: .8rem"><?= $app->id ?></div>
							</div>
							<div class="span l1 align-right">
								<label>
									<input type="checkbox" name="apps[]" value="<?= $app->id ?>" class="frm-ctrl">
									<span class="frm-ctrl-chk"></span>
								</label>
							</div>
						</div>
						<div class="spacer small"></div>
						<?php endforeach; ?>
						<div class="spacer small"></div>
						<div class="align-right">
							<input type="submit" value="Initialize" class="button">
						</div>
					</form>
					
					<div class="spacer large"></div>
					
				</div>
			</div>
			<div class="text:grey-500">
				You can return to this dialog at a later point by heading to 
				<?= url('setup', 'apps')->absolute() ?> at a later point.
			</div>
		</div>
	</div>
</div>