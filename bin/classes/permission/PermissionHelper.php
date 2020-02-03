<?php namespace permission;

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
	
	public static function unlock($key, $id) {
		$result = \GrantModel::GRANT_DENY;
		$pieces = explode('.', $key);
		$resource = null;
		$identity = db()->table('identity')->get('name', $id)->first();
		
		while($pieces) {
			$fragment = array_shift($pieces);
			$query = db()->table('resource')->get('key', $fragment);
			$parent = $resource;
			
			if ($resource) {
				$query->where('parent', $parent);
			}
			
			$resource = $query->first();
			
			if (!$resource) {
				return $result;
			}
			
			$grant = db()->table('grant')->get('identity', $identity)->where('resource', $resource)->first();
			
			if (!$grant || $grant->grant == \GrantModel::GRANT_INHERIT) {
				continue;
			}
			else {
				$result = $grant->grant;
			}
		}
		
		return (int)$result;
	}
}
