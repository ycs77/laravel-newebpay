<?php

namespace Ycs77\NewebPay\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;
use Ycs77\NewebPay\Support\Base64Url;

class RestoreSessionId
{
    /**
     * The session store instance.
     */
    protected Session $session;

    /**
     * Create a new middleware.
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $key = 'sid'): Response
    {
        $sessionId = $this->decryptSessionId($request, $key);

        if ($sessionId) {
            $this->restoreSessionId(
                $request, $this->session, $sessionId
            );
        }

        return $next($request);
    }

    /**
     * Decrypt the session id from callback url query.
     */
    protected function decryptSessionId(Request $request, string $key): string
    {
        try {
            return Base64Url::decode(Crypt::decryptString($request->query($key)));
        } catch (DecryptException $e) {
            $this->undecrypted($e);
        }
    }

    /**
     * Handle on undecrypted.
     */
    protected function undecrypted(DecryptException $e): void
    {
        //
    }

    /**
     * Restore the session id for current request.
     */
    protected function restoreSessionId(Request $request, Session $session, string $sessionId): void
    {
        $session->invalidate();

        $session->setId($sessionId);

        $request->setLaravelSession(
            $this->startSession($request, $session)
        );
    }

    /**
     * Start the session for the given request.
     */
    protected function startSession(Request $request, Session $session): Session
    {
        return tap($session, function ($session) use ($request) {
            $session->setRequestOnHandler($request);

            $session->start();
        });
    }
}
