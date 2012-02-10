<?php 
namespace org\opencomb\advertisement ;
use org\jecat\framework\bean\BeanFactory;

use org\opencomb\platform\ext\Extension ;
use org\jecat\framework\lang\aop\AOP;
use org\opencomb\advertisement\aspect\AdapterManager;
use org\opencomb\platform\system\PlatformSerializer;
use org\jecat\framework\ui\xhtml\weave\Patch;
use org\jecat\framework\ui\xhtml\weave\WeaveManager;

class Advertisement extends Extension 
{
	/**
	 * 载入扩展
	 */
	public function load()
	{
		BeanFactory::singleton()->registerBeanClass("org\\opencomb\\advertisement\\widget\\Advertisment",'advertisment') ;
		//AOP::singleton()->register('org\\opencomb\\advertisement\\MainMenuAspect') ;
		// todo ...
		
		//PlatformSerializer::singleton()->addSystemObject(AdapterManager::singleton()) ;
		AOP::singleton()->register('org\\opencomb\\advertisement\\aspect\\MainMenuAspect') ;
	}
}