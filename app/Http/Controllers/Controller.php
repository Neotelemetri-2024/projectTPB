<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Whitelisted page size shared by all paginated table listings.
     */
    protected function perPage(?Request $request = null, int $default = 10): int
    {
        $value = (int) ($request ?? request())->query('per_page', $default);

        return in_array($value, [10, 25, 50, 100], true) ? $value : $default;
    }
}
