
<div class="spacer medium"></div>

<div class="row l1">
	<div class="span l1">
		<div class="material">
			<div class="row l4">
				<div class="span l1">
					<h2>Test a right</h2>
				</div>
				<div class="span l3">
					<div class="spacer small"></div>
					<form method="POST" action="">
						<div class="row l2">
							<div class="span l1" id="resources">
								<input type="text" name="0[resource]" class="frm-ctrl" placeholder="Resource...">
							</div>
							<div class="span l1" id="identities">
								<input type="text" name="0[identities][]" class="frm-ctrl" placeholder="Identity...">
							</div>
						</div>
						<div class="spacer small"></div>
						<div class="row l1">
							<div class="span l1 align-right">
								<a class="text:grey-500 no-decoration" href="#" id="add-identity">Add identity</a>
								<input type="submit" class="button">
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if (isset($result) && isset($result[0])): ?>
<div class="spacer large"></div>

<div class="row l1">
	<div class="span l1">
		<div class="material">
			<div class="row l4">
				<div class="span l1">
					<h2>Result</h2>
				</div>
				<div class="span l3">
					
					<div class="spacer medium"></div>
					<pre>
					</pre>
					<?php $v = $result[0]; ?>
					<div class="row l3 s3">
						<div class="span l2 s2">
							<span class="text:grey-200"><?= $v->getPath() ?></span>
						</div>
						<div class="span l1 s1">
							<?php if ($v->getResult() == -1): ?>
							<span style="color: #900">Denied</span>
							<?php elseif ($v->getResult() == 0): ?>
							<span style="color: #666">Undefined</span>
							<?php else: ?>
							<span style="color: #090">Granted</span>
							<?php endif; ?>
							<span title="The specificity of the result">
								(<?= strval($v->getSpecificity()) ?>)
							</span>
						</div>
					</div>
					<div class="spacer small"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<script type="text/javascript">
(function () {
	document.getElementById('add-identity').addEventListener('click', function (e) {
		var input = document.createElement('input');
		input.type = 'text';
		input.placeholder = 'Identity...';
		input.name = '0[identities][]';
		input.className = 'frm-ctrl';
		
		document.getElementById('identities').appendChild(input);
		e.stopPropagation();
		e.preventDefault();
	});
	
	document.getElementById('add-resource').addEventListener('click', function (e) {
		var input = document.createElement('input');
		input.type = 'text';
		input.placeholder = 'Resource...';
		input.name = '0[resources][]';
		input.className = 'frm-ctrl';
		
		document.getElementById('resources').appendChild(input);
		e.stopPropagation();
		e.preventDefault();
	});
}());
</script>
