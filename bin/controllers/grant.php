<?php

use permission\PermissionHelper;
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
	//* world
	
	public function grant() {
		
		/*
		 * Check if the user or an application is available for checking.
		 */
		if (!$this->user || !$this->authapp) {
			throw new PublicException('Permission denied', 403);
		}
		
		if ($this->request->isPost()) {
			throw new HTTPMethodException();
		}
		
		/*
		 * Verify that either the user logged in, or the application do indeed have
		 * the required permission to modify the setting.
		 */
		if (!PermissionHelper::unlock('_permission.' . $_POST['resource'], '@' . $this->user->getId()) && 
			!PermissionHelper::unlock('_permission.' . $_POST['resource'], ':' . $this->authapp->getSrc()->getId())) {
			throw new PublicException('Permission denied', 403);
		}
		
		$pieces = explode('.', $_POST['resource']);
		$resource = null;
		
		while($pieces) {
			$key = array_shift($pieces);
			$query = db()->table('resource')->get('key', $key);
			$parent = $resource;
			
			if ($resource) {
				$query->where('parent', $parent);
			}
			
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
		
		if (!$grant) {
			$grant = db()->table('grant')->newRecord();
			$grant->resource = $resource;
			$grant->identity = $identity;
			$grant->grant = $_POST['grant'];
			$grant->store();
		}
		
		$this->view->set('grant', $grant);
	}
	
	

	public function test() {

		/*
		 * Check if the user or an application is available for checking.
		 */
		if (!$this->authapp) {
			throw new PublicException('Permission denied', 403);
		}
		
		/*
		 * 
		 */
		$resources = collect($_POST['resources']);
		$identities = collect($_POST['identities']);
		$identities->push('*');
		
		$results = $resources->each(function ($resource) use ($identities) {
			return max($identities->each(function ($id) use ($resource) {
				return PermissionHelper::unlock($resource, $id);
			})->toArray());
		});
		
		$this->view->set('result', array_combine($resources, $results));
		$this->response->getHeaders()->contentType('json');
		$this->response->setBody((json_encode(array_combine($resources->toArray(), $results->toArray()))));
	}
}
