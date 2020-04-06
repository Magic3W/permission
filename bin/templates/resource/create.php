<div class="spacer medium"></div>

<div class="row l1">
	<div class="span l1">
		<h1>Add a resource</h1>
		
		<form method="POST" action="">
			<input class="frm-ctrl" type="text" name="key" value="<?= $_GET['parent']? __($_GET['parent'] . '.') : '' ?>">
			<input type="submit" class="button">
		</form>
	</div>
</div>