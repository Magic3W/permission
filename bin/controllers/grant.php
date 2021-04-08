<?php

use permission\PermissionHelper;
use permission\PermissionTestResult;
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

class GrantController extends BaseController 
{
	
	//@user
	//#group
	//:app
	//~role
	//$relation: How the user is connected to the resource (like &owner or &creator)
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
		catch (HTTPMethodException$e) {}
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
			//throw new PublicException('Permission denied', 403);
		}
		
		if ($this->request->isPost()) {
			
			$_ret = [];
			
			/*
			 * Loop over the query that was sent.
			 */
			foreach ($_POST as $index => $query) {
				
				if (count($query) !== 2) { 
					throw new PublicException('Bad query: The query contains more keys than expected', 400);
				}
				
				list($resource, $identities) = [$query['resource'], $query['identities']];
				
				if (!is_string($resource)) {
					throw new PublicException('Bad query: The query submitted multiple resources. This is not permitted.', 400);
				}
				
				$result = collect($identities)->reduce(function (PermissionTestResult $carry = null, $identity) use ($resource) {
					
					/**
					 * Perform the query to check whether the identity can access the resource.
					 */
					$result = PermissionHelper::unlock($resource, $identity);
					
					/**
					 * If the carry was just as specific or more, we keep that and continue testing the other
					 * results.
					 */
					if ($carry && $carry->getSpecificity() >= $result->getSpecificity()) { return $carry; }
					
					return $result;
				}, null);
				
				$_ret[$index] = $result;
			}
			
			$this->view->set('result', $_ret);
		}
		
		
	}
	
	/**
	 * 
	 * @param GrantModel $grant
	 */
	public function edit(GrantModel$grant = null) {
		
		
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
		catch (HTTPMethodException$e) {
			/*Show the form*/
		}
		
		$this->view->set('grant', $grant);
	}
	
}
