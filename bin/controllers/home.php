<?php


/**
 * Prebuilt test controller. Use this to test all the components built into
 * for right operation. This should be deleted whe using Spitfire.
 */

class HomeController extends Controller
{
	public function index() {
		
		if (!db()->table('resource')->get('parent', null)->where('key', '_resource')->first()) {
			$this->response->setBody('Redirect')->getHeaders()->redirect(url('setup'));
			return;
		}
		
		/*
		 * Currently there's nothing on the homepage that is worth mentioning.
		 * We're just redirecting to the appropriate resource
		 */
		$this->response->setBody('Redirecting...')->getHeaders()->redirect(url('resource'));
	}
}