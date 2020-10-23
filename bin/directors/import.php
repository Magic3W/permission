<?php

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

class ImportDirector extends \spitfire\mvc\Director
{
	
	/**
	 * This endpoint generates a CSV output of the current database, allowing the 
	 * application to back up grants to an external file.
	 */
	public function grants() {
		
		while ($raw = fgetcsv(STDIN)) {
			
			$pieces = explode('.', $raw[0]);
			$resource = null;
			
			/*
			 * Perform some basic sanity checks on the data coming into the system.
			 * This should make the importer way more resilient against invalid data,
			 * or data that was accidentally imported from a wrong file.
			 */
			if (!isset($raw[2]) || !in_array($raw[2], ['allow', 'deny', 'inherit'])) {
				console()->error('Skipping invalid grant')->ln();
				continue;
			}
			
			/*
			 * Check whether the resource path is valid, this also reduces the likelihood
			 * of data being corrupted when being imported. This also greatly reduces the
			 * risk of applications accidentally importing PHP errors.
			 */
			if (!isset($raw[0]) || !preg_match('/^[A-Za-z0-9\.\-\_]+$/', $raw[0])) {
				console()->error('Invalid resource path: ' . $raw[0])->ln();
			}

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
			
			$identity = db()->table('identity')->get('name', $raw[1])->first();
			$grant = db()->table('grant')->get('identity', $identity)->where('resource', $resource)->first();
			
			if ($this->parameters->get('dry-run')) {
				console()->info('Would ' . $raw[2] . ' identity' . $identity->_id . ' access to ' . $resource->_id)->ln();
				continue;
			}
			
			/*
			 * If the identity could not be found, it can be created so the system
			 * can aggregate all the identity's permissions with an integer reference.
			 */
			if (!$identity) {
				$identity = db()->table('identity')->newRecord();
				$identity->name = $raw[1];
				$identity->store();
			}
			
			/*
			 * Convert the grant type back to the values the database uses to manage
			 * the permissions. While in transit, the system uses deny, allow and inherit,
			 * but once it landed, the system will switch to integers.
			 */
			switch ($raw[2]) {
				case 'deny' : 
					$allow = GrantModel::GRANT_DENY; break;
				case 'allow' : 
					$allow = GrantModel::GRANT_ALLOW; break;
				case 'inherit':
					$allow = GrantModel::GRANT_INHERIT; break;
			}
			
			if (!$grant) {
				$grant = db()->table('grant')->newRecord();
				$grant->resource = $resource;
				$grant->identity = $identity;
				$grant->grant = $allow;
				$grant->store();
			}
			else {
				$grant->grant = $allow;
				$grant->store();
			}
		}
	}
}
