<?php
// Application middleware
$guard = new Slim\Csrf\Guard();
$guard->setFailureCallable(function ($request, $response, $next) {
    $request = $request->withAttribute("csrf_status", false);
    return $next($request, $response);
});
$app->add($guard);
