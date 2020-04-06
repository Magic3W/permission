
<div class="spacer huge"></div>

<div class="row l1">
	<div class="span l1">
		<form method="POST" action="">
			<div class="row l3 ng-lr">
				<div class="span l1">
					<input class="frm-ctrl" type="text" placeholder="resource" name="resource" <?php if (isset($_GET['resource'])): ?>value="<?= __($_GET['resource']) ?>"<?php endif; ?>>
				</div>
				<div class="span l1">
					<input class="frm-ctrl" type="text" placeholder="identity" name="identity">
				</div>
				<div class="span l1">
					<select class="frm-ctrl" name="grant">
						<option value="<?= GrantModel::GRANT_INHERIT ?>">Inherit</option>
						<option value="<?= GrantModel::GRANT_ALLOW ?>">Grant</option>
						<option value="<?= GrantModel::GRANT_DENY ?>">Deny</option>
					</select>
				</div>
			</div>
			
			<div class="spacer medium"></div>
			
			<div class="align-right">
				<input class="button" type="submit" value="Save">
			</div>
		</form>
	</div>
</div>