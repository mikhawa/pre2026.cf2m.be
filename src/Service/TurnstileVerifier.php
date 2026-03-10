<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Vérifie les tokens Cloudflare Turnstile via l'API siteverify.
 */
class TurnstileVerifier
{
    private const string VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire(env: 'TURNSTILE_SECRET_KEY')]
        private readonly string $secretKey,
    ) {}

    /**
     * Retourne true si le token Turnstile est valide.
     */
    public function verify(string $token, ?string $ip = null): bool
    {
        if ($token === '') {
            return false;
        }

        $body = [
            'secret'   => $this->secretKey,
            'response' => $token,
        ];

        if ($ip !== null) {
            $body['remoteip'] = $ip;
        }

        try {
            $response = $this->httpClient->request('POST', self::VERIFY_URL, [
                'body' => $body,
            ]);

            return (bool) ($response->toArray()['success'] ?? false);
        } catch (\Throwable) {
            // En cas d'erreur réseau, on laisse passer pour ne pas bloquer les utilisateurs légitimes
            return true;
        }
    }
}
