
<div class="row l1">
	<div class="span l1">
		<h1>Resources</h1>
	</div>
</div>

<?php foreach ($resources as $resource): ?>
<div class="row l1">
	<div class="span l1">
		<div class="material">
			<a href="<?= url('resource', 'index', $resource->_id) ?>"><?= $resource->key ?></a>
		</div>
	</div>
</div>

<div class="spacer medium"></div>
<?php endforeach; ?>


<?php foreach (db()->table('grant')->get('resource', $parent)->all() as $grant): ?>
<div class="row l1">
	<div class="span l1">
		<div class="material">
			<a href="<?= url('grant', 'edit', $grant->_id) ?>"><?= $grant->identity->name ?> (<?= $grant->grant ?>)</a>
		</div>
	</div>
</div>

<div class="spacer medium"></div>
<?php endforeach; ?>
