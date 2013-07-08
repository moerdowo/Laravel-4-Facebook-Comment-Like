<?php namespace Totox777\Fbplugins;

use Illuminate\Support\ServiceProvider;

class FbpluginsServiceProvider extends ServiceProvider {

	protected $defer = false;

	public function boot()
	{
		$this->package('totox777/fbplugins');
	}

	public function register()
	{
		$app = $this->app;

		$this->app['fbplugins'] = $this->app->share(function($app)
	    {
	        //new Fbplugins();
	        $facebook = Fbplugins::getInstance ( "http://www.facebook.com/yoqizainteractivebali" ) ;
            $facebook->setOpenGraph ( 'url' , $facebook->getUrl () ) ;
            $facebook->setType ( 'blog' ) ;

            return $facebook;
	    });
	}

	public function provides()
	{
		return array();
	}

}