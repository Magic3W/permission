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

class MnemonicModel extends Model
{
	/*
	 * Descriptions are just weakly attached to either resources or identities,
	 * and allow the system to display helpful information to the user if this
	 * is wanted.
	 * 
	 * This for example allows to replace the '@1' in an identity with 'Csharp',
	 * or ':1' with administrators, or the resource 'app1234' with 'Ping',  
	 * which makes it way more accessible to humans.
	 */
	public function definitions(Schema $schema) {
		
		$schema->type = new EnumField('resource', 'identity');
		$schema->id   = new IntegerField(true);
		$schema->caption = new StringField(50);
		$schema->description = new TextField();
		
		$schema->index($schema->type, $schema->id);
	}

}
