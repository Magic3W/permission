<?php

use permission\PermissionHelper;
use spitfire\exceptions\PublicException;
use spitfire\exceptions\HTTPMethodException;

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

class GrantController extends BaseController 
{
	
	//@user
	//#group
	//:app
	//~role
	//* world
	
	public function create() {
		
		/*
		 * Check if the user or an application is available for checking.
		 */
		if (!$this->user && !$this->authapp) {
			throw new PublicException('Permission denied', 403);
		}
		
		try {
			if (!$this->request->isPost()) {
				throw new HTTPMethodException();
			}

			/*
			 * Verify that either the user logged in, or the application do indeed have
			 * the required permission to modify the setting.
			 */
			$appid = $this->authapp? $this->authapp->getSrc()->getId() : '';
			
			if (PermissionHelper::unlock('_permission.' . $_POST['resource'], '@' . $this->user->id) !== GrantModel::GRANT_ALLOW && 
				PermissionHelper::unlock('_permission.' . $_POST['resource'], ':' . $appid) !== GrantModel::GRANT_ALLOW) {
				throw new PublicException('Permission denied', 403);
			}

			$pieces = explode('.', $_POST['resource']);
			$resource = null;

			while($pieces) {
				$key = array_shift($pieces);
				$parent = $resource;
				$query = db()->table('resource')->get('key', $key)->where('parent', $parent);

				$resource = $query->first();

				if (!$resource) {
					$resource = db()->table('resource')->newRecord();
					$resource->parent = $parent;
					$resource->key = $key;
					$resource->store();
				}
			}

			$identity = db()->table('identity')->get('name', $_POST['identity'])->first();
			$grant = db()->table('grant')->get('identity', $identity)->where('resource', $resource)->first();
			
			if (!$identity) {
				$identity = db()->table('identity')->newRecord();
				$identity->name = $_POST['identity'];
				$identity->store();
			}

			if (!$grant) {
				$grant = db()->table('grant')->newRecord();
				$grant->resource = $resource;
				$grant->identity = $identity;
				$grant->grant = $_POST['grant'];
				$grant->store();
			}
			else {
				$grant->grant = $_POST['grant'];
				$grant->store();
			}
			
			$this->view->set('grant', $grant);
		} 
		catch (spitfire\exceptions\HTTPMethodException$e) {}
	}
	
	public function allow(GrantModel$grant) {
		
		/*
		 * Check if the user or an application is available for checking.
		 */
		if (!$this->user && !$this->authapp) {
			throw new PublicException('Permission denied', 403);
		}
		
		$grant->grant = 1;
		$grant->store();
		
		$this->response->setBody('Redirect')->getHeaders()->redirect(url('resource', 'index', $grant->resource->_id));
	}
	
	public function revoke(GrantModel$grant) {
		
		/*
		 * Check if the user or an application is available for checking.
		 */
		if (!$this->user && !$this->authapp) {
			throw new PublicException('Permission denied', 403);
		}
		
		$grant->grant = GrantModel::GRANT_DENY;
		$grant->store();
		
		$this->response->setBody('Redirect')->getHeaders()->redirect(url('resource', 'index', $grant->resource->_id));
	}
	
	public function delete(GrantModel$grant) {
		
		/*
		 * Check if the user or an application is available for checking.
		 */
		if (!$this->user && !$this->authapp) {
			throw new PublicException('Permission denied', 403);
		}
		
		$appid = $this->authapp? $this->authapp->getSrc()->getId() : '';
		
		if (PermissionHelper::unlock('_permission.' . $grant->resource->path(), '@' . $this->user->id) !== GrantModel::GRANT_ALLOW && 
			PermissionHelper::unlock('_permission.' . $grant->resource->path(), ':' . $appid) !== GrantModel::GRANT_ALLOW) {
			throw new PublicException('Permission denied', 403);
		}
		
		$grant->delete();
		
		$this->response->setBody('Redirect')->getHeaders()->redirect(url('resource', 'index', $grant->resource->_id));
	}

	public function eval() {
		
		/*
		 * Check if the user or an application is available for checking.
		 */
		if (!$this->authapp && !$this->user) {
			throw new PublicException('Permission denied', 403);
		}
		
		if ($this->request->isPost()) {
			/*
			 * 
			 */
			$resources = collect($_POST['resources']);
			$identities = collect(array_merge(['*'], $_POST['identities']));

			$results = $resources->each(function ($resource) use ($identities) {
				return PermissionHelper::unlock($resource, $identities);
			});
			
			$this->view->set('result', array_combine($resources->toArray(), $results->toArray()));
		}
		
		
	}
	
	/**
	 * 
	 * @param GrantModel $grant
	 */
	public function edit(GrantModel$grant) {
		
		
		$appid = $this->authapp? $this->authapp->getSrc()->getId() : '';
		
		if (PermissionHelper::unlock('_permission.' . $grant->resource->path(), '@' . $this->user->id) !== GrantModel::GRANT_ALLOW && 
			PermissionHelper::unlock('_permission.' . $grant->resource->path(), ':' . $appid) !== GrantModel::GRANT_ALLOW) {
			throw new PublicException('Permission denied', 403);
		}
		
		try {
			if (!$this->request->isPost()) { throw new HTTPMethodException('Not posted'); }
			
			$grant->grant = $_POST['grant'];
			$grant->store();
			$this->view->set('updated', true);
		} 
		catch (spitfire\exceptions\HTTPMethodException$e) {
			/*Show the form*/
		}
		
		$this->view->set('grant', $grant);
	}
	
}
