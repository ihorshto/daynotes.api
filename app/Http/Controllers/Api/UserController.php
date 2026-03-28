<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\Lang;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserLangRequest;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function updateLang(UpdateUserLangRequest $request): JsonResponse
    {
        $request->user()->update([
            'lang' => Lang::from($request->validated('lang')),
        ]);

        return response()->json(['lang' => $request->user()->fresh()->lang]);
    }
}
