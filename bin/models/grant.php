<?php

use spitfire\Model;
use spitfire\storage\database\Schema;

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
 * A grant determines whether the resource is accessible to the user. The default 
 * behavior is to inherit the permissions for the user from the parent resource.
 * 
 * @property ResourceModel $resource The resource the grant is providing access to
 * @property IdentityModel $identity The identity being granted
 * @property int $grant Indicates whether the grant is granting, denying or inheriting access
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class GrantModel extends Model
{
	
	/*
	 * Allows the application to decide whether the user should be granted, denied
	 * or inherited access rights to the resource.
	 * 
	 * Denying or allowing an identity access will override the grant from the 
	 * parent.
	 */
	const GRANT_DENY = -1;
	const GRANT_INHERIT = 0;
	const GRANT_ALLOW = 1;
	
	/**
	 * 
	 * @param Schema $schema
	 * @return Schema
	 */
	public function definitions(Schema $schema) {
		$schema->resource = new Reference('resource');
		$schema->identity = new Reference('identity');
		$schema->grant    = new IntegerField();
		
		$schema->index($schema->resource, $schema->identity);
	}

}
