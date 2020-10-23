<?php 
	# In the event of the application being already posted to and having created
   # a new resource, the endpoint should redirect a user to the new resource so
	# so they can immediately work with it.

	if (isset($updated)) {
		current_context()->response->getHeaders()->redirect(URLUtil::isLocal($_GET['returnto'])?: url('resource', 'index', $grant->resource->_id));
		echo 'Redirecting...';
		return;
	}
	
?><div class="spacer large"></div>

<div class="row l1">
	<div class="span l1">
		<div class="material">
			<form method="POST" action="">
				<div class="row l4">
					<div class="span l1">
						<h2>Editing grant for <?= $grant->identity->name ?></h2>
					</div>
					<div class="span l3">
						<div class="spacer medium"></div>
						<select name="grant" class="frm-ctrl">
							<option value="<?= GrantModel::GRANT_ALLOW ?>" <?= $grant->grant == GrantModel::GRANT_ALLOW? 'selected' : '' ?>>Allow</option>
							<option value="<?= GrantModel::GRANT_DENY ?>" <?= $grant->grant == GrantModel::GRANT_DENY? 'selected' : '' ?>>Deny</option>
							<option value="<?= GrantModel::GRANT_INHERIT ?>" <?= $grant->grant == GrantModel::GRANT_INHERIT? 'selected' : '' ?>>Inherit</option>
						</select>
						
						<div class="spacer medium"></div>
						
						<p class="text:grey-600">
							At this point you can only edit whether the user should be granted or
							denied access. In order to change the user or the resource you will need 
							to create a new grant and revoke this one.
						</p>
						
						
						<div class="spacer medium"></div>
						<div class="separator"></div>
						<div class="spacer medium"></div>
						
						<div class="align-right">
							<a href="<?= url('grant', 'delete', $grant->_id) ?>" style="color: #900; text-decoration: none" onclick="return confirm('Really delete?')">Delete</a>
							<input type="submit" class="button" value="Save">
						</div>
						
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
