<?php 

namespace Ceb\Generators;

use Ceb\Generators\TestRoute;
use Illuminate\Support\Facades\File;
use Route;
use Stringy\StaticStringy as S;

 class TestsGenerators
 {
 	protected $routes;

 	function __construct()
 	{
 		$this->routes = Route::getRoutes();;
 	}

 	/**
 	 * Get all routes
 	 * @return  array
 	 */
 	public function routes()
 	{

 		$all = [];
 		foreach ($this->routes as  $route) {
 			
			$router				= 	new TestRoute;
			$path				= 	$route->getPath();
			$params				= 	$this->getParameters($path);
			
			$router->parameters	= 	$params;
			$router->path		= 	$path;
			$router->name		=	$route->getName();
			$router->action		=	$route->getAction();
			$router->method		=	$route->getMethods()[0];
			$router->testUrl 	= 	$router->getTestUrl();

			$methodName = str_replace('.', '_', $router->name);
			if (is_null($router->name)) {
				$methodName     = str_replace('/', '_',$router->path);
			}
			$methodName = preg_replace('/[^\da-z]/i', '', $methodName);
			$router->testMethodName = 'test'.ucfirst(S::camelize($methodName));

			$router->testDefinition = $this->getTestDefinition($router); 

			$fileName = explode('/',$router->path)[0];
			if (is_null($fileName) || empty($fileName)) {
				$fileName = 'Default';
			}
 			$all[$fileName][$methodName] = $router;
 		}

 		return $all;
 	}

 	/**
 	 * Get parameters based on the routes path
 	 * @param  string $route routepath
 	 * @return array of parameters
 	 */
 	public function getParameters($route)
 	{
 		    $params = [];
        	while (stringContains($route,'{')) {
        		$param = stringBetween($route, '{', '}');
        		$params[] = $param;
        		$route = str_replace('{'.$param.'}', '', $route);
        	}
        return $params;
 	}

 	/**
 	 * Generate testing function definition
 	 * @param  TestRoute $route 
 	 * @return string        
 	 */
 	public function getTestDefinition($route)
 	{

	$page = explode('{',$route->path)[0];

	$testingMethod  = '
	/**
	 * A '.$route->testMethodName.' functional.
	 *
	 * @return void
	 */
	public function '.$route->testMethodName.'()
	{
	    $this->'.strtolower($route->method).'(\''.$route->testUrl.'\')
	         ->seePageIs(\''.$page.'\');
	}';
	return $testingMethod;
 	}

 	public function writeTestClass()
 	{
 		
 		$files = $this->routes();

 		foreach ($files as $fileName => $routes) {

 		$path = base_path('tests/'.S::uppercamelize($fileName).'Test.php');

 		File::put($path,'<?php');
        File::append($path,"\r\n");
		File::append($path,'use Illuminate\Foundation\Testing\WithoutMiddleware;');
		File::append($path,"\r\n");
		File::append($path,'use Illuminate\Foundation\Testing\DatabaseMigrations;');
	    File::append($path,"\r\n");
		File::append($path,'use Illuminate\Foundation\Testing\DatabaseTransactions;');
        File::append($path,"\r\n");
	    File::append($path,'class '.$fileName.' extends TestCase');
		File::append($path,"{");

		foreach ($routes as $route) {

			File::append($path,"\r\n");
		  	File::append($path, $route->testDefinition);
		  }

		File::append($path,"\r\n");
		File::append($path,"}");
		File::append($path,"\r\n");
		File::append($path,"?>");

		}
 	}
 }