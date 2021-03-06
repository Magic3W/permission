<!DOCTYPE html>
<html>
	<head>
		<title><?= isset(${'page.title'}) && ${'page.title'}? ${'page.title'} : 'Permission - User Access Control' ?></title>
		<meta name="twitter:card"  content="summary_large_image">
		<meta name="og:title" value="<?= isset(${'page.title'}) && ${'page.title'}? ${'page.title'} : 'Permission - User Access Control' ?>">
		<meta name="twitter:title" value="<?= isset(${'page.title'}) && ${'page.title'}? ${'page.title'} : 'Permission - User Access Control' ?>">
		<meta name="twitter:site" value="@commishes">
		
		<!-- Meta viewport for the mobile and tablet users-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="_scss" content="<?= \spitfire\SpitFire::baseUrl() ?>/assets/scss/_/js/">
		
		<?php if (isset(${'robots.index'}) && !${'robots.index'}): ?> 
		<!--Robots crawling settings -->
		<meta name="robots" content="noindex">
		<?php endif; ?> 
		
		<?php if (isset(${'page.description'}) && ${'page.description'}): ?> 
		<!--The page's description -->
		<meta name="description" content="<?= __(str_replace(["\n", "\r"], '', ${'page.description'}), 175) ?>">
		<meta name="og:description" content="<?= __(str_replace(["\n", "\r"], '', ${'page.description'}), 175) ?>">
		<meta name="twitter:description" content="<?= __(str_replace(["\n", "\r"], '', ${'page.description'}), 140) ?>">
		<?php endif; ?> 
		
		<?php if (isset(${'page.image'}) && ${'page.image'}): ?> 
		<!--The page's image. Please make sure that this contains only URL escaped characters -->
		<meta name="og:image" content="<?= ${'page.image'} ?>">
		<meta name="twitter:image" content="<?= ${'page.image'} ?>">
		<?php endif; ?> 
		
		<link rel="stylesheet" type="text/css" href="<?= spitfire\core\http\URL::asset('css/app.css') ?>">
		
		<!-- Include the dependency injector -->
		<script src="<?= spitfire\core\http\URL::asset('js/m3/depend.js') ?>" type="text/javascript"></script>
		<script src="<?= spitfire\core\http\URL::asset('js/m3/depend/router.js') ?>" type="text/javascript"></script>
		
		<script type="text/javascript">
		(function () {
			depend(['m3/depend/router'], function(router) {
				router.all().to(function(e) { return '<?= \spitfire\SpitFire::baseUrl() . '/assets/js/' ?>' + e + '.js'; });
				router.equals('_scss').to( function() { return '<?= \spitfire\SpitFire::baseUrl() ?>/assets/scss/_/js/_.scss.js'; });
				
				
				var location = document.querySelector('meta[name="_scss"]').getAttribute('content') || '/assets/scss/_/js/';

				router.startsWith('_scss/').to(function(str) {
					return location + str.substring(6) + '.js';
				});
			});
		}());
		</script>
	</head>
	<body>
		
		<div class="navbar">
			<div class="left">
				<span class="toggle-button dark"></span>
			</div>
			<div class="right">
				<?php if(isset($authUser) && $authUser): ?>
					<div class="has-dropdown" style="display: inline-block">
						<a href="<?= url('user', 'profile', $authUser->username) ?>" class="app-switcher" data-toggle="app-drawer">
							<img src="<?= $authUser->avatar ?>" width="32" height="32" style="border-radius: 50%; vertical-align: middle" >
						</a>
						<div class="dropdown right-bound unpadded" data-dropdown="app-drawer">
							<div class="app-drawer" id="app-drawer">
								<div class="navigation vertical">
									<a class="navigation-item" href="<?= url('user', 'logout') ?>">Logout</a>
								</div>
							</div>
						</div>
					</div>
					<span class="h-spacer"></span>
				<?php else: ?>
					<a class="menu-item" href="<?= url('account', 'login', Array('returnto' => $_SERVER['REQUEST_URI'])) ?>">Login</a>
				<?php endif; ?>
			</div>
			<div class="center">
				<a href="<?= url() ?>">
					<?php $app = collect($sso->getAppList())->filter(function ($e) use ($sso) { return $e->id == $sso->getAppId(); })->rewind(); ?>
					<img src="<?= $app->icon->m ?>" width="32"> 
					<span class="desktop-only" style="vertical-align: .6rem"><?= $app->name ?></span>
				</a>
			</div>
		</div>
		
		<div class="auto-extend" data-sticky-context>
			<?= $this->content() ?>
		</div>
		
		<footer>
			<div class="row l1">
				<div class="span l1 align-center">
					Permission is open source software - Licensed under MIT License - &copy; <?= date('Y') ?>
				</div>
			</div>
		</footer>
		
		<!--Sidebar -->
		<div class="contains-sidebar collapsed">
			<div class="sidebar">
				
				<div class="navbar">
					<div class="left">
						<a href="<?= url() ?>">
							<?php $app = collect($sso->getAppList())->filter(function ($e) use ($sso) { return $e->id == $sso->getAppId(); })->rewind(); ?>
							<img src="<?= $app->icon->m ?>" width="32"> 
							<span style="vertical-align: .6rem"><?= $app->name ?></span>
						</a>
					</div>
				</div>
				
				<?php if(isset($authUser) && $authUser): ?>
				<div class="menu-title"> Permission</div>
				<div class="menu-entry"><a href="<?= url() ?>">Overview</a></div>
				
				<div class="menu-title"> Administration</div>
				<div class="menu-entry"><a href="<?= url('resource') ?>">Resources</a></div>
				<div class="menu-entry"><a href="<?= url('identity') ?>">Identities</a></div>
				<?php else: ?>
				<div class="menu-title"> Account</div>
				<div class="menu-entry"><a href="<?= url('user', 'login') ?>"   >Login</a></div>
				<?php endif; ?>
				
				<div class="spacer medium"></div>

				<div class="menu-title">Our network</div>
				<div id="appdrawer"></div>
			</div>
		</div>
		
		<script type="text/javascript">
			
			depend(['ui/dropdown'], function (dropdown) {
				dropdown('.app-switcher');
			});
			
			depend(['_scss'], function() {
				console.log('Loaded _scss');
			});
			
			/*
			 * Load the applications into the sidebar
			 */
			depend(['m3/core/request'], function (Request) {
				var request = new Request('<?= $sso->getEndpoint() ?>/appdrawer.json');
				request
					.then(JSON.parse)
					.then(function (e) {
						e.forEach(function (i) {
							var entry = document.createElement('div');
							var link  = entry.appendChild(document.createElement('a'));
							var icon  = link.appendChild(document.createElement('img'));
							entry.className = 'menu-entry';
							
							link.href = i.url;
							link.appendChild(document.createTextNode(i.name));
							
							icon.src = i.icon.m;
							document.getElementById('appdrawer').appendChild(entry);
						});
					})
					.catch(console.log);
			});
		</script>
	</body>
</html>