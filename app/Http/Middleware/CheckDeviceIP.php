<?php

namespace App\Http\Middleware;

use App\Models\WhiteListIps;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckDeviceIP
{
    protected $allowedIpBlocks = [];

    public function __construct()
    {
        // Fetch all allowed IPs and CIDR ranges from the database
        $this->allowedIpBlocks = WhiteListIps::pluck('ip')->toArray();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // $clientIP = $request->server('HTTP_CF_CONNECTING_IP') ?? $request->header('CF-Connecting-IP') ?? $request->header('X-Forwarded-For') ?? $request->ip();
        $clientIP = $request->ip();
        if ($this->isIpAllowed($clientIP)) {
            return $next($request);
        }
        Log::info('IP Blocked: ' . $clientIP);
        return response()->json([
            'message' => 'IP Blocked ! Please Contact Administrator'
        ], 403);
    }

     /**
     * Check if the IP is whitelisted (either as a single IP or inside a CIDR range).
     */
    private function isIpAllowed(string $ip): bool
    {
        foreach ($this->allowedIpBlocks as $entry) {
            if ($this->isValidIp($entry)) {
                if ($ip === $entry) {
                    return true;
                }
            } elseif ($this->isValidCidr($entry)) {
                if ($this->ipInRange($ip, $entry)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
    * Check if an entry is a valid single IP.
    */
    private function isValidIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
    * Check if an entry is a valid CIDR notation (x.x.x.x/x).
    */
    private function isValidCidr(string $cidr): bool
    {
        $parts = explode('/', $cidr);
        return count($parts) === 2 && filter_var($parts[0], FILTER_VALIDATE_IP) && is_numeric($parts[1]) && $parts[1] >= 0 && $parts[1] <= 32;
    }


    private function ipInRange(string $ip, string $cidr): bool
    {
        list($subnet, $mask) = explode('/', $cidr);
        $subnetLong = ip2long($subnet);
        $ipLong = ip2long($ip);
        $maskLong = -1 << (32 - $mask);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }
}
