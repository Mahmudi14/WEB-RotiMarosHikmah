<?php

namespace App\Http\Middleware;

use App\Models\PosTerminal;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyTerminalBridgeToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Token terminal wajib dikirim.',
            ], 401);
        }

        $terminal = PosTerminal::query()
            ->where('bridge_token', $token)
            ->first();

        if (! $terminal) {
            return response()->json([
                'success' => false,
                'message' => 'Token terminal tidak valid.',
            ], 401);
        }

        if (isset($terminal->status) && $terminal->status !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Terminal tidak aktif.',
            ], 403);
        }

        $request->attributes->set('terminal', $terminal);

        return $next($request);
    }
}