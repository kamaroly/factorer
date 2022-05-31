<?php

namespace Ceb\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
          // If model is not found 
          if ($e instanceof ModelNotFoundException ) {
                flash()->error(trans('general.we_could_not_find_what_you_are_looking_for'));
                return redirect()->back();
          }

          if (get_class($e) == 'Illuminate\Session\TokenMismatchException') {
                    /**
                     * Generate a new token for more security
                     */
                    Session::regenerateToken();

                    Session::flash('error', trans('token_tricking_is_very_bad'));

                    /**
                     * Redirect to the last step
                     * Refill any old inputs except _token (it would override our new token)
                     * Set the error message
                     */
            return redirect()->back()->withInput(Input::except('_token'));
          }


        return parent::render($request, $e);
    }
}
