<?php namespace permission;

use GrantModel;
use spitfire\core\Collection;
use function collect;
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

class PermissionHelper 
{
	
	public static function unlock($key, $id) : PermissionTestResult
	{
		
		
		/**
		 * These are defaults, and therefore, the specificity is always 0. This means
		 * that the rules are as generic as they get
		 */
		if ($id === true  || $id === 'true')  { return new PermissionTestResult(GrantModel::GRANT_ALLOW, null); }
		if ($id === false || $id === 'false') { return new PermissionTestResult(GrantModel::GRANT_DENY, null); }
		if ($id === null  || $id === 'null')  { return new PermissionTestResult(GrantModel::GRANT_INHERIT, null); }
		
		$result = GrantModel::GRANT_INHERIT;
		$pieces = explode('.', $key);
		$resource = null;
		$identity = db()->table('identity')->get('name', $id)->first();
		
		$specificity = null;
		
		while($pieces) {
			$fragment = array_shift($pieces);
			$parent = $resource;
			$query = db()->table('resource')->get('key', $fragment)->where('parent', $parent);
			
			$resource = $query->first();
			
			if (!$resource) {
				return new PermissionTestResult((int)$result, $specificity);
			}
			
			$grant = db()->table('grant')->get('identity', $identity)->where('resource', $resource)->first();
			
			if ($grant) {
				if ($grant->grant != GrantModel::GRANT_INHERIT) { $result = $grant->grant; }
				
				/**
				 * The specificity equals the depth of the resource. If we're looking up
				 * the string "app123.posts.create" the specificity of the rule is 1 if 
				 * the rule was applied to app123, 2 if it was applied to app123.posts,
				 * etc.
				 * 
				 * This allows clients to query rules, determining the that more elaborate
				 * rules are prioritized.
				 */
				$specificity = $resource;
			}
			
		}
		
		return new PermissionTestResult((int)$result, $specificity);
	}
	
	public function unlockAll($key, $ids) 
	{
		
		return $key->each(function ($key) use ($ids) { return self::unlock($key, $ids); });
	}
}
