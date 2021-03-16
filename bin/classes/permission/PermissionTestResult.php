<?php namespace permission;

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
	
	/**
	 *
	 * @var \ResourceModel
	 */
	private $path;
	
	public function __construct($result, \ResourceModel $resource = null) 
	{
		$this->result = $result;
		$this->path = $resource? $resource->path() : '';
	}
	
	public function getResult() 
	{
		return $this->result;
	}
	
	public function getPath() {
		return $this->path;
	}
	
	public function compare(PermissionTestResult $to) : PermissionTestResult
	{
		/**
		 * If the incoming path does not contain this one, we assume that it's not 
		 * overriding it.
		 */
		if (!\Strings::startsWith($to->getPath(), $this->getPath())) { return $this; }
		
		/**
		 * If the paths match, we return the longer one.
		 */
		return strlen($to->getPath()) > strlen($this->getPath())? $to : $this;
	}
	
}
