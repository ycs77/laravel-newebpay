<?php

namespace Ycs77\NewebPay\Contracts;

use Illuminate\Http\Request;
use Ycs77\LaravelRecoverSession\UserSource;

interface UserSourceable
{
    /**
     * Preserve the user information into session.
     */
    public function preserveUserSource(Request $request): void;

    /**
     * Set the user source instance.
     */
    public function setUserSource(UserSource $userSource);
}
