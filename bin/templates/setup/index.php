

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
					
					<div class="text:grey-400">Welcome <?= __($authUser->username) ?>,</div>
					<div class="spacer small"></div>
					<div class="text:grey-400">
						You are setting up a permission server. This server allows applications
						that are part of your network to check whether users and other apps 
						on your network have authority to perform certain operations.
					</div>
					<div class="spacer small"></div>
					<div class="text:grey-400">
						When clicking continue, you will be registered as the root owner
						of this server, and you will therefore be in charge of granting 
						permissions to applications and users.
					</div>
					<div class="spacer small"></div>
					<div class="text:grey-100">
						If you're not the company's system administrator, then you should 
						not continue with setup and proceed to contact your administrator.
					</div>
					<div class="spacer large"></div>
					<form method="POST" action="" class="align-right">
						<input type="submit" class="button" value="Continue">
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
