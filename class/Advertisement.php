<?php 
namespace org\opencomb\advertisement ;

use org\jecat\framework\bean\BeanFactory;
use org\opencomb\platform\mvc\view\widget\Menu;
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
		// 注册 widget bean
		BeanFactory::singleton()->registerBeanClass("org\\opencomb\\advertisement\\widget\\Advertisment",'advertisment') ;
		
		// 注册菜单build事件的处理函数
		Menu::registerBuildHandle(
			'org\\opencomb\\coresystem\\mvc\\controller\\ControlPanelFrame'
			, 'frameView'
			, 'mainMenu'
			, array(__CLASS__,'buildControlPanelMenu')
		) ;
	}

	/**
	 * @advice around
	 * @for pointcutCreateBeanConfig
	 */
	private function buildControlPanelMenu()
	{
		$arrConfig['items']['system']['menu']['items']['platform-manage']['menu']['items']['oauth-menu'] = array (
				'title'=>'OAuth' ,
				'link' => '?c=org.opencomb.oauth.controlPanel.OAuthSetting' ,
				'query' => 'c=org.opencomb.oauth.controlPanel.OAuthSetting' ,
		);
	}
}