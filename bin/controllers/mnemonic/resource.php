<?php namespace mnemonic;

use BaseController;
use permission\PermissionHelper;
use ResourceModel;
use spitfire\exceptions\HTTPMethodException;
use spitfire\exceptions\PublicException;
use spitfire\validation\ValidationException;
use function db;

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

class ResourceController extends BaseController
{
	
	/**
	 * This endpoint allows a user to define a menmonic for a resource. This makes 
	 * it easier for the user to manage said mnemonics.
	 * 
	 * @validate >> POST#caption (string length[3, 500])
	 * @validate >> POST#description (string length[3, 500])
	 * 
	 * @param ResourceModel $resource The resource that should receive the metadata
	 */
	public function set(ResourceModel$resource) {
		
		if ($this->user && PermissionHelper::unlock('_resource.' . $resource->path(), '@' . $this->user->id)) {
			//Go on
		}
		elseif ($this->authapp && PermissionHelper::unlock('_resource.' . $resource->path(), ':' . $this->authapp->getSrc()->getId())) {
			//Go on
		}
		else {
			throw new PublicException('Insufficient permissions', 403);
		}
		
		$mnemonic = db()->table('mnemonic')->get('type', 'resource')->where('id', $resource->_id)->first();
		
		try {
			if (!$this->request->isPost()) { throw new HTTPMethodException(); }
			if (!$this->validation->isEmpty()) { throw new ValidationException('Validation failure', 2002271120, $this->validation->toArray()); }
			

			if (!$mnemonic) {
				$mnemonic = db()->table('mnemonic')->newRecord();
				$mnemonic->type = 'resource';
				$mnemonic->id = $resource->_id;
			}

			$mnemonic->caption = $_POST['caption'];
			$mnemonic->description = $_POST['description'];
			$mnemonic->store();
			
			$this->view->set('result', 'success');
		} 
		catch (HTTPMethodException$ex) {
			//Show the form
		}
		
		$this->view->set('resource', $resource);
		$this->view->set('mnemonic', $mnemonic);
	}
	
	public function get(ResourceModel$resource) {
		$this->view->set('resource', $resource);
		$this->view->set('mnemonic', db()->table('mnemonic')->get('type', 'resource')->where('id', $resource->_id)->first());
	}
	
	public function remove(ResourceModel$resource) {
		
		if ($this->user && PermissionHelper::unlock('_resource.' . $resource->path(), '@' . $this->user->id)) {
			//Go on
		}
		elseif ($this->authapp && PermissionHelper::unlock('_resource.' . $resource->path(), ':' . $this->authapp->getSrc()->getId())) {
			//Go on
		}
		else {
			throw new PublicException('Insufficient permissions', 403);
		}
		
		$mnemonic = db()->table('mnemonic')->get('type', 'resource')->where('id', $resource->_id)->first();
		$mnemonic->delete();
		
		$this->view->set('success', true);
	}
	
}