<?php 

namespace Ceb\Generators;

/**
 * Route to test
 */
 class TestRoute
 {
 	/**
 	 * $name
 	 * @var string
 	 */
 	public $name;
 	/**
 	 * $path
 	 * @var string
 	 */
 	public $path;
 
	/**
	 * $public
	 * @var  string
	 */
 	public $action;

 	/**
 	 * $method
 	 * @var string
 	 */
 	public $method;

 	/**
 	 * $parameters
 	 * @var array
 	 */
 	public $parameters;
    
    /**
     * Test url with parameters
     * @var string
     */
    public $testUrl;

    /**
     * Name of the test Method
     * @var string
     */
    public $testMethodName;

    /**
     * definition of the testing function
     * 
     * @var [type]
     */
    public $testDefinition;

    
    public $fileName;
    /**
     * Get url to use for testing
     * @return string
     */
    public function getTestUrl()
    {
       // Build url with parameters
       $routeName = $this->path;

        foreach ($this->parameters as $key => $value) {
            $routeName = str_replace('{'.$value.'}', $key + 1, $routeName);
        }

        return $this->testUrl = $routeName;
    }
}