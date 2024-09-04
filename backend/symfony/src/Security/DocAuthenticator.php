<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\HttpFoundation\Response;

class DocAuthenticator extends AbstractAuthenticator
{
    /**
     * @var string $docPassword
     */
    private string $docPassword;

    /**
     * @param string $docPassword
     */
    public function __construct(string $docPassword)
    {
        $this->docPassword = $docPassword;
    }

    /**
     * @param Request $request
     * @return bool|null
     */
    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() === '/doc' || $request->getPathInfo() === '/doc.json';
    }

    /**
     * @param Request $request
     * @return SelfValidatingPassport
     */
    public function authenticate(Request $request): SelfValidatingPassport
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Basic ')) {
            throw new CustomUserMessageAuthenticationException('No password provided');
        }

        // Get the password from the Authorization header
        $encodedCredentials = str_replace('Basic ', '', $authHeader);
        $decodedCredentials = base64_decode($encodedCredentials);

        // Split the credentials into username and password and ignore the username
        [, $password] = explode(':', $decodedCredentials, 2);

        if ($password !== $this->docPassword) {
            throw new CustomUserMessageAuthenticationException('Invalid password');
        }

        return new SelfValidatingPassport(new UserBadge('api_doc_user'));
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => $exception->getMessageKey()], Response::HTTP_UNAUTHORIZED, ['WWW-Authenticate' => 'Basic realm="API Documentation"']);
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $firewallName
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // Pustíme požadavek dále, pokud autentizace proběhne úspěšně
    }
}
