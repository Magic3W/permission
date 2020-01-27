<?php

class AuthDirector extends \spitfire\mvc\Director
{

	/**
	 * Regenerate the list of applications that are allowed to interact with the
	 * permission server.
	 *
	 * Once the list has been gathered, the system records the list, creates the
	 * resources for these applications and grants them access to edit the permissions.
	 */
	public function update() {
		$sso = new \auth\SSO(\spitfire\core\Environment::get('SSO'));
		$apps = $sso->getAppList();

		foreach($apps as $app) {
			/*
			 * Create a record for the application we wish to grant access to the permission
			 * server.
			 */
			$record = db()->table('application')->get('appid', $app->id)->first()?: db()->table('application')->newRecord();
			$record->appid = $app->id;
			$record->name = $app->name;
			$record->store();

			$resource = db()->table('resource')->get('parent', null)->where('key', $app->id)->first()?: db()->table('resource')->newRecord();
			$resource->parent = null;
			$resource->key = $app->id;
			$resource->store();

			$identity = db()->table('identity')->get('name', ':' . $app->id)->first()?: db()->table('identity')->newRecord();
			$identity->name = ':' . $app->id;
			$identity->store();

			/*
			 * If the application did not exist up to this point, and therefore had no
			 * grant on it. Then we grant it write access to it's own permissions.
			 */
			if (!db()->table('grant')->get('resource', $resource)->where('identity', $identity)->first()) {
				$grant = db()->table('grant')->newRecord();
				$grant->resource = $resource;
				$grant->identity = $identity;
				$grant->grant = true;
				$grant->store();
			}
		}

	}

}
