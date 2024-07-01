<?php
class Plugins_Layout extends Zend_Controller_Plugin_Abstract 
{

	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
			$layoutPath = APPLICATION_PATH . '/modules/' . $request->getModuleName() . '/layouts/scripts/';
			Zend_Layout::getMvcInstance()->setLayoutPath($layoutPath);
	}
} 
?>