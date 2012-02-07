<?php
namespace org\opencomb\advertisement\aspect;

use org\jecat\framework\bean\BeanFactory;
use org\jecat\framework\lang\aop\jointpoint\JointPointMethodDefine;

class MainMenuAspect
{
	/**
	 * @pointcut
	 */
	public function pointcutCreateBeanConfig()
	{
		return array(
			new JointPointMethodDefine('org\\opencomb\\coresystem\\mvc\\controller\\ControlPanelFrame','createBeanConfig') ,
		) ;
	}
	
	/**
	 * @advice around
	 * @for pointcutCreateBeanConfig
	 */
	private function createBeanConfig()
	{
		// 调用原始原始函数
		$arrConfig = aop_call_origin() ;
		/*
		// 合并配置数组，增加菜单
		BeanFactory::mergeConfig(
				$arrConfig['frameview:frameView']['widget:mainMenu']['items']['system']['menu']['items']['platform-manage']
				['menu']['items'],$arrMenus
		) ;
		*/
		$arrConfig['frameview:frameView']['widget:mainMenu']['items']['CMS']['menu']['items'][] =array(
			'title'=>'广告设置',
			'link'=>'?c=org.opencomb.advertisement.AdvertisementSetting',
			'query'=>'c=org.opencomb.advertisement.AdvertisementSetting'
		);
		return $arrConfig ;
	}
}
?>