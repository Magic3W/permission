<div class="spacer medium"></div>

<?php if ($parent): ?>
<div class="row l1">
	<div class="span l1">
		<a class="text:grey-500 no-decoration" href="<?= url('resource', 'index') ?>">Root</a>
		<?php foreach($ancestors as $next): ?>
		<span class="text:grey-600">&raquo;</span>
		<a class="text:grey-500 no-decoration" href="<?= url('resource', 'index', $next->_id) ?>"><?= $next->mnemonic()? $next->mnemonic()->caption : $next->key ?></a>
		<?php endforeach;?>
		<h1>
			<?= $parent->mnemonic()? $parent->mnemonic()->caption : $parent->key ?> 
			
			<?php if(!$parent || \permission\PermissionHelper::unlock('_resource.' . $parent->path(), '@' . $authUser->id)): ?>
			<a href="<?= url('mnemonic', 'resource', 'set', $parent->_id) ?>" class="button small outline">Edit</a>
			<?php endif; ?>
		</h1>
	</div>
</div>
<?php else : ?>
<div class="row l1">
	<div class="span l1">
		<h1>Root</h1>
	</div>
</div>
<?php endif; ?>

<div class="spacer medium"></div>

<div class="row l1">
	<div class="span l1">
		<div class="material">
			<div class="row l4">
				<div class="span l1"><h2>Resources</h2></div>
				<div class="span l3">
					<div class="spacer medium"></div>
					<?php foreach ($resources as $resource): ?>
					<a class="text:grey-400 no-decoration" href="<?= url('resource', 'index', $resource->_id) ?>">
						<?= $resource->mnemonic()? $resource->mnemonic()->caption : $resource->key ?>
						<span class="text:grey-600">(<?= $resource->path(); ?>)</span>
					</a>
					<div class="spacer small"></div>
					<?php endforeach; ?>
					
					<?php if(!$parent || \permission\PermissionHelper::unlock('_resource.' . $parent->path(), '@' . $authUser->id)): ?>
					<a class="text:grey-100 no-decoration" href="<?= url('resource', 'create', $parent? ['parent' => $parent->path()] : []) ?>">Add a resource</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="spacer huge"></div>


<div class="row l1">
	<div class="span l1">
		<div class="material">
			<div class="row l4">
				<div class="span l1"><h2>Grants</h2></div>
				<div class="span l3">
					<div class="spacer medium"></div>
					<?php foreach (db()->table('grant')->get('resource', $parent)->all() as $grant): ?>
					<?php $identity = $grant->identity; ?>
					<div class="row l4 s3 ng-lr">
						<div class="span l2 s1">
							<?php if(!$parent || \permission\PermissionHelper::unlock('_resource._identity', '@' . $authUser->id)): ?>
							<a class="text:grey-300 no-decoration" href="<?= url('mnemonic', 'identity', 'set', $identity->_id) ?>"><?= $identity->mnemonic()? $identity->mnemonic()->caption : $identity->name ?></a>
							<?php endif; ?>
							<span class="text:grey-600 no-decoration">(<?= $identity->name ?>)</span>
						</div>
						<div class="span l1 s1">
							<?php if ($grant->grant == 1): ?>
							<a class="no-decoration" style="color: #090" href="<?= url('grant', 'revoke', $grant->_id) ?>">Allowed</a>
							<?php elseif ($grant->grant == -1): ?>
							<a class="no-decoration" style="color: #900" href="<?= url('grant', 'allow', $grant->_id) ?>">Denied</a>
							<?php else: ?>
							<a class="no-decoration" style="color: #666" href="<?= url('grant', 'allow', $grant->_id) ?>">Undefined</a>
							<?php endif; ?>
						</div>
						<div class="span l1 s1">
							<a class="button small outline" href="<?= url('grant', 'edit', $grant->_id) ?>">Permissions</a>
						</div>
					</div>
					<div class="spacer small"></div>
					<?php endforeach; ?>
					
					<?php if(!$parent || \permission\PermissionHelper::unlock('_permission.' . $parent->path(), '@' . $authUser->id)): ?>
					<a class="text:grey-100 no-decoration" href="<?= url('grant', 'create', ['resource' => $parent? $parent->path() : null]) ?>">Grant a user</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="spacer medium"></div>
