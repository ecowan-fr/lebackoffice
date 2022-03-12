<?php

namespace App\RequestMatcher;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

class ApiRequestMatcher implements RequestMatcherInterface {

    public function matches(Request $request): bool {
        return str_starts_with($request->getPathInfo(), '/api') && $request->getPathInfo() !== '/api/login' && (in_array('application/json', $request->getAcceptableContentTypes()) || in_array('application/ld+json', $request->getAcceptableContentTypes()));
    }
}
