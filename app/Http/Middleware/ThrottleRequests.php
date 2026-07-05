<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThrottleRequests
{
    /**
     * The rate limiter instance.
     *
     * @var RateLimiter
     */
    protected $limiter;

    /**
     * @param RateLimiter $limiter
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * Usage: 'throttle:<maxAttempts>,<decayMinutes>,<bucket>'
     *
     * @param Request $request
     * @param Closure $next
     * @param int     $maxAttempts
     * @param int     $decayMinutes
     * @param string  $bucket
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $bucket = 'global')
    {
        $key = $this->resolveRequestSignature($request, $bucket);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return $this->buildTooManyAttemptsResponse($key, $maxAttempts);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        return $this->addHeaders(
            $next($request),
            $maxAttempts,
            $this->limiter->retriesLeft($key, $maxAttempts)
        );
    }

    /**
     * Build the rate-limit key: per authenticated user, otherwise per client IP.
     *
     * @param Request $request
     * @param string  $bucket
     *
     * @return string
     */
    protected function resolveRequestSignature(Request $request, string $bucket): string
    {
        $identifier = auth()->check()
            ? 'user|'.auth()->user()->getAuthIdentifier()
            : 'ip|'.$request->ip();

        return sha1($bucket.'|'.$identifier);
    }

    /**
     * Build the 429 response once the limit is exceeded.
     *
     * @param string $key
     * @param int    $maxAttempts
     *
     * @return JsonResponse
     */
    protected function buildTooManyAttemptsResponse(string $key, int $maxAttempts): JsonResponse
    {
        $retryAfter = $this->limiter->availableIn($key);

        $response = new JsonResponse(['error' => 'Too many requests.'], 429);

        return $this->addHeaders($response, $maxAttempts, 0, $retryAfter);
    }

    /**
     * Attach rate-limit headers to the response.
     *
     * @param Response $response
     * @param int      $maxAttempts
     * @param int      $remainingAttempts
     * @param int|null $retryAfter
     *
     * @return Response
     */
    protected function addHeaders(Response $response, int $maxAttempts, int $remainingAttempts, ?int $retryAfter = null): Response
    {
        $headers = [
            'X-RateLimit-Limit'     => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ];

        if (!is_null($retryAfter)) {
            $headers['Retry-After'] = $retryAfter;
        }

        $response->headers->add($headers);

        return $response;
    }
}
