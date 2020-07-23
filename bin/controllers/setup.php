<?php

use permission\PermissionHelper;
use spitfire\core\http\URL;
use spitfire\exceptions\HTTPMethodException;
use spitfire\exceptions\PublicException;

/* 
 * The MIT License
 *
 * Copyright 2020 César de la Cal Bretschneider <cesar@magic3w.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class SetupController extends BaseController
{
	
	public function index() {
		
		if (db()->table('resource')->get('parent', null)->where('key', '_resource')->first()) {
			throw new PublicException('Setup was already executed', 400);
		}
		
		if (!$this->user) {
			$this->response->setBody('Redirect')->getHeaders()->redirect(url('user', 'login', ['returnto' => strval(URL::current())]));
			return;
		}
		
		try {
			if (!$this->request->isPost()) { throw new HTTPMethodException('Not posted', 2007231534); }
			
			$resource = db()->table('resource')->newRecord();
			$resource->parent = null;
			$resource->key    = '_resource';
			$resource->store();
			
			$permission = db()->table('resource')->newRecord();
			$permission->parent = null;
			$permission->key    = '_permission';
			$permission->store();
			
			$identity = db()->table('identity')->newRecord();
			$identity->name = '@' . $this->user->id;
			$identity->store();
			
			$grant = db()->table('grant')->newRecord();
			$grant->resource = $resource;
			$grant->identity = $identity;
			$grant->grant = GrantModel::GRANT_ALLOW;
			$grant->store();
			
			$grant2 = db()->table('grant')->newRecord();
			$grant2->resource = $permission;
			$grant2->identity = $identity;
			$grant2->grant = GrantModel::GRANT_ALLOW;
			$grant2->store();
			
			$this->response->setBody('Redirect...')->getHeaders()->redirect(url('setup', 'apps'));
			return;
		} 
		catch (HTTPMethodException$ex) {

		}
	}
	
	
	public function apps() {
		
		if (!$this->user) {
			$this->response->setBody('Redirect')->getHeaders()->redirect(url('user', 'login', ['returnto' => strval(URL::current())]));
			return;
		}
		
		$apps = $this->sso->getAppList();
		
		
		try {
			if (!$this->request->isPost()) { throw new HTTPMethodException('Not posted', 2007231534); }
			
			foreach ($_POST['apps'] as $appid) {
				$app = collect($apps)->filter(function ($e) use ($appid) { return $e->id == $appid; })->rewind();
				$pr = db()->table('resource')->get('key', '_permission')->where('parent', null)->first(true);
				$rr = db()->table('resource')->get('key', '_resource')->where('parent', null)->first(true);
				
				if (!PermissionHelper::unlock('_resource.' . $_POST['app' . $appid], '@' . $this->user->id)) {
					throw new PublicException('Insufficient permissions', 403);
				}
				
				$resource = db()->table('resource')->get('parent', null)->where('key', 'app' . $appid)->first()?: db()->table('resource')->newRecord();
				$resource->key = 'app' . $appid;
				$resource->store();
				
				$resource2 = db()->table('resource')->get('parent', $rr)->where('key', 'app' . $appid)->first()?: db()->table('resource')->newRecord();
				$resource2->key = 'app' . $appid;
				$resource2->parent = $rr;
				$resource2->store();
				
				$permission = db()->table('resource')->get('parent', $pr)->where('key', 'app' . $appid)->first()?: db()->table('resource')->newRecord();
				$permission->key = 'app' . $appid;
				$permission->parent = $pr;
				$permission->store();
				
				$identity = db()->table('identity')->get('name', ':' . $appid)->first()?: db()->table('identity')->newRecord();
				$identity->name = ':' . $appid;
				$identity->store();
				
				
				$grant = db()->table('grant')->get('identity', $identity)->where('resource', $resource)->first();
				$grant2 = db()->table('grant')->get('identity', $identity)->where('resource', $permission)->first();
				$grant3 = db()->table('grant')->get('identity', $identity)->where('resource', $resource2)->first();
				
				if (!$grant) {
					$grant = db()->table('grant')->newRecord();
					$grant->resource = $resource;
					$grant->identity = $identity;
					$grant->grant = GrantModel::GRANT_ALLOW;
					$grant->store();
				}
				
				if (!$grant2) {
					$grant2 = db()->table('grant')->newRecord();
					$grant2->resource = $permission;
					$grant2->identity = $identity;
					$grant2->grant = GrantModel::GRANT_ALLOW;
					$grant2->store();
				}
				
				if (!$grant3) {
					$grant = db()->table('grant')->newRecord();
					$grant->resource = $resource2;
					$grant->identity = $identity;
					$grant->grant = GrantModel::GRANT_ALLOW;
					$grant->store();
				}
				
				$mnemonic = db()->table('mnemonic')->get('type', 'resource')->where('id', $resource->_id)->first();
				$mnemonicPermission = db()->table('mnemonic')->get('type', 'resource')->where('id', $permission->_id)->first();
				$mnemonicIdentity   = db()->table('mnemonic')->get('type', 'identity')->where('id', $identity->_id)->first();
				
				if (!$mnemonic) {
					$mnemonic = db()->table('mnemonic')->newRecord();
					$mnemonic->type = 'resource';
					$mnemonic->id = $resource->_id;
					$mnemonic->caption = $app->name;
					$mnemonic->description = $app->name;
					$mnemonic->store();
				}
				
				if (!$mnemonicPermission) {
					$mnemonicPermission = db()->table('mnemonic')->newRecord();
					$mnemonicPermission->type = 'resource';
					$mnemonicPermission->id = $resource->_id;
					$mnemonicPermission->caption = $app->name;
					$mnemonicPermission->description = $app->name;
					$mnemonicPermission->store();
				}
				
				if (!$mnemonicIdentity) {
					$mnemonicIdentity = db()->table('mnemonic')->newRecord();
					$mnemonicIdentity->type = 'identity';
					$mnemonicIdentity->id = $identity->_id;
					$mnemonicIdentity->caption = $app->name;
					$mnemonicIdentity->description = $app->name;
					$mnemonicIdentity->store();
				}

			}
			
			$this->response->setBody('Redirect...')->getHeaders()->redirect(url());
			return;
		} 
		catch (HTTPMethodException$ex) {

		}
		
		$this->view->set('apps', $apps);
	}
	
}
