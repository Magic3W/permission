<?php

use permission\PermissionHelper;
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

/**
 * The resource controller allows an administrator to manage the resources available
 * to grant permissions on. 
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class ResourceController extends BaseController
{
	
	public function _onload() {
		parent::_onload();
		
		/*
		 * This endpoints are restricted to the adminsitrators of the application
		 * only. Applications can use the grant:: methods to explore and perform 
		 * all the management they wish.
		 */
		if (!$this->user && !$this->authapp) {
			$this->response->setBody('Redirecting...')->getHeaders()->redirect(url('user', 'login'));
		}
	}
	
	/**
	 * List the resources that inherit from the parent (if there's no parent, the
	 * root resources will be listed)
	 * 
	 * @param ResourceModel $parent The resource these inherit from
	 */
	public function index(ResourceModel$parent = null) {
		$query = db()->table('resource')->get('parent', $parent);
		$resources = $query->all();
		
		$next = $parent;
		$ancestors = collect($next);
		while ($next = $next->parent) { $ancestors->push($next); }
		
		$this->view->set('parent', $parent);
		$this->view->set('resources', $resources);
		$this->view->set('ancestors', $ancestors->reverse());
	}
	
	/**
	 * Provides an endpoint to programmatically create a resource. Resources are
	 * generally inferred when an application grants a resource that does not exist,
	 * but the UI can create them and navigate to them
	 */
	public function create() {
		
		
		try {
			if (!$this->request->isPost()) { throw new HTTPMethodException('Not Posted'); }
			
			$identities = [];
			
			if ($this->authapp)  { $identities = [':' . $this->authapp]; }
			elseif ($this->user) { $identities = ['@' . $this->user->id]; }
			
			/*
			 * Check if the user has the right to manage the permissions for this resource
			 */
			if (!PermissionHelper::unlock('_resource.' . $_POST['key'], $identities)) {
				throw new PublicException('Insufficient permissions', 403);
			}
			
			$pieces = explode('.', $_POST['key']);
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

			$this->view->set('resource', $resource);
		} 
		catch (HTTPMethodException$e) {
			/*The user didn't post the data, show a form*/
		}
	}
	
	/**
	 * Allows an administrator to edit a resource. This allows the administrator 
	 * to move an entire tree of resources to a new location.
	 * 
	 * @validate POST#key (string required length[3, 50])
	 * @param ResourceModel $id
	 */
	public function move(ResourceModel$id = null) {
		
		//TODO: Add check whether the user can edit the resource
		
		if ($this->request->isPost()) {
			$id->key = $_POST['key'];
			$id->store();
		}
		
		$this->view->set('resource', $id);
	}
	
	/**
	 * Mark a resource for permanent deletion. The resource can be properly aliased
	 * so it's no longer accessible. All children of it will be permanently removed.
	 * 
	 * @param ResourceModel $resource
	 */
	public function delete(ResourceModel$resource = null) {
		
		//TODO: Add check whether the user can edit the resource
		
		$resource->removed = time();
		$resource->store();
		
		$this->view->set('resource', $resource);
	}
	
}
