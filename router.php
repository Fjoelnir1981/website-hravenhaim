<?php
// Safe router for the documented local PHP development server.
$path=rawurldecode(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH) ?: '/');
if(preg_match('~(?:^|/)(?:\.[^/]*|data|includes|lib)(?:/|$)|(?:\.sqlite(?:-\w+)?$|\.md$)|^/(?:bootstrap|router)\.php$~i',$path) || str_contains($path,'..')) {
    http_response_code(404);echo 'Nicht gefunden';return true;
}
return false;
