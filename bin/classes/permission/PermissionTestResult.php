<?php namespace permission;

use IdentityModel;
use ResourceModel;

/* 
 * The MIT License
 *
 * Copyright 2021 César de la Cal Bretschneider <cesar@magic3w.com>.
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

class PermissionTestResult
{
	
	private $result;
	
	private $identity;
	
	/**
	 * @var string
	 */
	private $specificity;
	
	/**
	 *
	 * @var string
	 */
	private $path;
	
	public function __construct($identity, string $resource, $result, ResourceModel $specificity = null) 
	{
		$this->result = $result;
		$this->path = $resource;
		$this->identity = $identity;
		$this->specificity = $specificity? $specificity->path() : '';
	}
	
	public function getIdentity() 
	{
		return $this->identity;
	}
	
	public function getResult() 
	{
		return $this->result;
	}
	
	public function getPath() {
		return $this->path;
	}
	
	public function getSpecificity() 
	{
		return count(array_filter(explode('.', $this->specificity)));
	}
	
}
