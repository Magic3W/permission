<?php 
	# In the event of the application being already posted to and having created
   # a new resource, the endpoint should redirect a user to the new resource so
	# so they can immediately work with it.

	if (isset($resource)) {
		current_context()->response->getHeaders()->redirect(URLUtil::isLocal($_GET['returnto'])?: url('resource', 'index', $resource->_id));
		echo 'Redirecting...';
		return;
	}
	
?><div class="spacer large"></div>

<div class="row l1">
	<div class="span l1">
		<h1>Add a resource</h1>
		
		<div class="spacer small"></div>
		
		<div class="material">
			
			<p class="text:grey-500">
				A resource represents any piece of data that a user or application can 
				access. Resource paths are separated by dots, all the permissions from
				"path" will be inherited by "path.additional" unless explicitly overwritten.
			</p>
			<div class="spacer small"></div>
			
			<form method="POST" action="" id="resource-form">
				<div class="frm-ctrl-outer">
					<input class="frm-ctrl" type="text" name="key" id="input-key" value="<?= $_GET['parent']? __($_GET['parent'] . '.') : '' ?>">
					<label for="input-key">Resource path</label>
				</div>
				
				<div class="spacer small"></div>
				
				<div class="align-right">
					<input type="submit" class="button" value="Create resource">
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
(function () {
	var form   = document.querySelector('#resource-form');
	var submit = form.querySelector('input[type="submit"]');
	
	form.addEventListener('submit', function (e) {
		submit.setAttribute('disabled', 'disabled');
		setTimeout(function () { submit.removeAttribute('disabled'); }, 10000);
	});
	
	window.addEventListener('load', function () {
		submit.removeAttribute('disabled');
	});
}());
</script>
