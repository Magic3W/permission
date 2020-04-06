<?php

use auth\SSO;
use auth\Token;
use spitfire\cache\MemcachedAdapter;
use spitfire\core\Environment;
use spitfire\io\session\Session;

class BaseController extends Controller
{
	
	/**
	 *
	 * @var Session
	 */
	protected $session;
	
	/**
	 *
	 * @var SSO
	 */
	protected $sso;
	
	/**
	 *
	 * @var Token
	 */
	protected $token;
	protected $user;
	protected $authapp;
	
	public function _onload() {
		$s = $this->session = Session::getInstance();
		$t = $s->getUser();
		
		#Create a cache to reduce the load on PHPAuth
		$c = new MemcachedAdapter();
		$c->setTimeout(120);
		
		$this->sso   = new \auth\SSOCache(Environment::get('SSO'));
		$this->token = $t?                          $t                       : null;
		
		#Fetch the user.
		$this->user  = $t && $t instanceof Token? $c->get('token_' . $this->token->getId(), function () use ($t) { 
			return $t->isAuthenticated()? $t->getTokenInfo()->user : null; 
		}) : null;
		
		if (isset($_GET['signature'])) {
			$_auth = $this->sso->authApp($_GET['signature']);
			$this->authapp = $_auth->getRemote()->getId();
		}
		
		
		$this->view->set('authUser', $this->user);
		$this->view->set('authSSO', $this->sso);
		$this->view->set('sso', $this->sso);
		$this->view->set('authToken', $this->token);
		
	}
	
}
