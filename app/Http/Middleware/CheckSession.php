<?php

namespace App\Http\Middleware;

use Closure;

class CheckSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $uriSegments = request()->segments();
        $systemRolesArray = $request->session()->get('system_roles');
        // dd( $systemRolesArray);
        $message = 'You do not have permission to access this page. Kindly contact administrator.';
        if ($request->session()->get('id') == "") {
            return redirect('/admin');
        }
        if (isset($systemRolesArray[$uriSegments[1]])) {
            $permission = $systemRolesArray[$uriSegments[1]];
            if ($permission == 5) {
                return $next($request);
            } elseif ($permission == 4) {
                if (isset($uriSegments[2])) {
                    if ($uriSegments[2] == 'list' || $uriSegments[2] == 'add' || $uriSegments[2] == 'edit' || $uriSegments[1] == 'dashboard') {
                        return $next($request);
                    } else {
                        return redirect()->back()->withErrors([$message]);
                    }
                } else {
                    return $next($request);
                }
            } elseif ($permission == 3) {
                if (isset($uriSegments[2])) {
                    if ($uriSegments[2] == 'list' || $uriSegments[2] == 'add' || $uriSegments[1] == 'dashboard') {
                        return $next($request);
                    } else {
                        return redirect()->back()->withErrors([$message]);
                    }
                } else {
                    return $next($request);
                }
            } elseif ($permission == 2) {
                if (isset($uriSegments[2])) {
                    if ($uriSegments[2] == 'list' || $uriSegments[2] == 'edit' || $uriSegments[1] == 'dashboard') {
                        return $next($request);
                    } else {
                        return redirect()->back()->withErrors([$message]);
                    }
                } else {
                    return $next($request);
                }
            } elseif ($permission == 1) {
                if (isset($uriSegments[2])) {
                    if ($uriSegments[2] == 'list' || $uriSegments[1] == 'dashboard') {
                        return $next($request);
                    } else {
                        return redirect()->back()->withErrors([$message]);
                    }
                } else {
                    return $next($request);
                }
            } else {
                if (isset($uriSegments[1])) {
                    if ($uriSegments[1] == 'dashboard') {
                        if (count($systemRolesArray) > 0) {
                            foreach ($systemRolesArray as $k => $l) {
                                if ($l > 0) {
                                    return redirect('/admin' . '/' . $k);
                                }
                            }
                        } else
                            return $next($request);
                    } else {
                        return redirect()->back()->withErrors([$message]);
                    }
                } else {
                    if (count($systemRolesArray) > 0) {
                        foreach ($systemRolesArray as $k => $l) {
                            if ($l > 0) {
                                return redirect('/admin' . '/' . $k);
                            }
                        }
                    } else
                        return $next($request);
                }
            }
        } else {
            if ($uriSegments[1] == 'dashboard') {
                return $next($request);
            } else {
                // Auth::logout();
                // Session::flush();
                return redirect()->back()->withErrors([$message]);
            }
        }
        // return $next($request);
    }
}
