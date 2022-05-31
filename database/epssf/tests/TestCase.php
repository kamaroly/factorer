<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    public $user;
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Login auser
     * @param  string $email 
     * @return 
     */
    public function login($email = 'admin@admin.com')
    {
      $this->sentryUserBe($email);
      return $this;
    }

    /**
     * Login to sentry for Testing purpose
     * @param  $email
     * @return void
     */
    public function sentryUserBe($email='admin@admin.com')
    {
        $this->user = \Sentry::findUserByLogin($email);
        \Sentry::login($this->user);
        \Event::fire('sentinel.user.login', ['user' => $this->user]);

        return $this;
    }

   /**
     * Login to sentry for Testing purpose
     * @param  $email
     * @return void
     */
    public function user($email='admin@admin.com')
    {
    	return $this->sentryUserBe($email);
    }
}
