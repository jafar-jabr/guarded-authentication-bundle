Getting started
===============

Prerequisites
-------------

This bundle requires php 7+, Symfony 3.4+ and OpenSSL library.

Installation
------------

Add [`jafar/guard-authentication-bundle`](https://packagist.org/packages/jafar/guard-authentication-bundle)
to your `composer.json` file:

    composer require jafar/guarded-authentication-bundle

The bundle suppose to be automatically registered in `config/bundles.php` :

``` php
return [
     /.....
     Jafar\Bundle\GuardedAuthenticationBundle\JafarGuardedAuthenticationBundle::class => ['all' => true],
 ];

```

Bundle configuration
---------------------
Necessary configuration in your `config/packages` :

Create `jafar_guarded_authentication.yaml`

``` yaml
jafar_guarded_authentication:
   #the route name of login page
    login_route: ''
   #route name of home page 
    home_page_route: ''
   #route name for Api login url
    api_login_route: ''
   # ssh key pass phrase
    pass_phrase:         '' # passphrase which you choose when you generate keys in command line
    # token ttl
    token_ttl:           3600 #time to live in second
   # refresh token ttl
    refresh_ttl:      604800 #one week
```

Security configuration
-----------------------
in your `config/packages/security.yml`

```yaml

security:
    encoders:
        App\Entity\User:  #your own user Entity
            algorithm: auto #or whatever
    providers:
      # ...
        user_provider:
          entity:
              class: App\Entity\User #your own user Entity
    
    firewalls:
      # ...
        api:
            pattern: ^/api/
            anonymous: ~
            stateless: ~
            guard:
               authenticators:
                    - guarded.authentication.jws_token_authenticator
        main:
            pattern: ^/
            anonymous: ~
            guard:
                authenticators:
                     - guarded.authentication.login_form_authenticator
                entry_point: guarded.authentication.login_form_authenticator
            logout:
                path: /logout
            remember_me:
                secret:   '%kernel.secret%'
                lifetime: 604800 # 1 week in seconds
                path:     /
    # ...
    
    access_control:
            # ...
            - { path: ^/api/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/api/token/refresh, roles: IS_AUTHENTICATED_ANONYMOUSLY }
            # ...            
```

Generate the SSH keys :
from your project directory run in command line "as administrator"
``` php
php bin/console jafar:generate-keys      
```
you will be asked for choosing passPhrase (you will need it also in configuration). 

## use this pass_phrase also in setting in `jafar_guarded_authentication.yaml` file
make sure that you have Openssl enabled on you computer.

Usage
-----
1- to create your won user entity and to make it implements
``` php
Jafar\Bundle\GuardedAuthenticationBundle\User\GuardedUserInterface
     
```

2- the user repository has to implement :
``` php
Jafar\Bundle\GuardedAuthenticationBundle\User\GuardedUserRepositoryInterface;
```
and the method `loadUserByUsername` to load the user by whatever you like
for example it can looks like:

```php
     /**
         *{@inheritdoc}
         */
        public function loadUserByUsername($parameter)
        {
            return $this->createQueryBuilder('u')
                ->where('u.email = :username')
                ->orWhere('u.userName = :username')
                ->setParameter('username', $parameter)
                ->getQuery()
                ->getOneOrNullResult();
        }
```

3- for login method in the controller you can use:
```php
   Jafar\Bundle\GuardedAuthenticationBundle\Form\GuardedLoginForm
```
and it can looks like:

``` php
use Jafar\Bundle\GuardedAuthenticationBundle\Form\GuardedLoginForm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class LoginController
 */
class LoginController extends AuthController
{

    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    public function __construct(AuthenticationUtils $authenticationUtils)
    {
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * @return Response
     */
    public function login()
    {
        $authUtils = $this->authenticationUtils; //$this->get('security.authentication_utils');
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();
        $form = $this->createForm(GuardedLoginForm::class, [
            '_username' => $lastUsername,
        ]);
        return $this->render('auth/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }
}


```

- API login controller can look like:
``` php
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponse\ApiProblem;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponse\ApiResponseFactory;
use Jafar\Bundle\GuardedAuthenticationBundle\Exception\ApiException;

class ApiLoginController extends AuthController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * ApiLoginController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
        {
            $username = $request->get('username');
            $password = $request->get('password');
            $user = $this->userRepository->loadUserByUsername($username);
            if (!$user) {
                $apiProblem = new ApiProblem(401);
                $apiProblem->set('detail', 'Invalid email');
    
                return (new ApiResponseFactory())->createResponse($apiProblem);
            }
            $passwordValid = $this->get('security.password_encoder')->isPasswordValid($user, $password);
            if (!$passwordValid) {
                $apiProblem = new ApiProblem(401);
                $apiProblem->set('detail', 'Incorrect Password');
    
                return (new ApiResponseFactory())->createResponse($apiProblem);
            }
            try {
                $token        = $this->get('jafar_guarded_authentication.encoder')->encode(['username' => $username]);
                $refreshToken = $this->get('jafar_guarded_authentication.encoder')->encode(
                    ['username' => $username],
                    'Refresh'
                );
            } catch (ApiException $e) {
                $apiProblem = new ApiProblem(401);
                $apiProblem->set('detail', $e->getReason());
                return (new ApiResponseFactory())->createResponse($apiProblem);
            }
            return new JsonResponse(['status' => 'Success', 'token' => 'Bearer '.$token, 'refresh-token' => $refreshToken]);
        }
}
```
### with the above mentioned setting now your authentication system start to work
after you create your routes for form login and API authentication and update `jafar_guarded_authentication.yaml`
with real data
Now you can submit the login form for authentication or send (curl or postman) request

```bash
curl -X POST http://localhost:8000/api/your_api_login_url -d username=your_email -d password=your_password
``` 
### Use the token

from now and on you have to include the JWT on each request to the Api protected firewall as an authorization header
 `Authorization: Bearer token`

Notes
-----
### if you have problem with Api Authentication (authorization header not sent)
you need to add
```bash
<IfModule mod_rewrite.c>
#.....
    RewriteEngine On
    RewriteCond %{HTTP:Authorization} ^(.*)
    RewriteRule .* - [e=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
</IfModule>
``` 
in your `public/.htaccess` file

### token life time "ttl"

Each request after token expiration will result in a 401 response.
```bash
{
    "detail": "Expired JWT Token",
    "status": 401,
    "type": "about:blank",
    "title": "Unauthorized"
}
``` 
### Refresh Token 
starting from v2.06 you will have refresh-token service
```php
jafar_guarded_authentication.token_refresher
``` 

and you can generate refresh token,
```php
$refreshToken = $this->get('jafar_guarded_authentication.encoder')->encode(['username' => $username], 'Refresh');
``` 
an example of how to refresh the token
``` php
use App\Repository\UserRepository;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiProblem;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiLoginController extends AuthController
{
         # ...
         # ...
    
         /**
         * @param Request $request
         *
         * @return JsonResponse
         * @throws ApiException
         */
        public function refresh(Request $request)
        {
            try {
                $data         = $this->get('jafar_guarded_authentication.token_refresher')->decode($request);
                $username     = isset($data['username']) ? $data['username'] : 'not set';
                $user         = $this->userRepository->loadUserByUsername($username);
                if (!$user) {
                    $apiProblem = new ApiProblem(401);
                    $apiProblem->set('detail', 'Invalid refresh token');
    
                    return (new ApiResponseFactory())->createResponse($apiProblem);
                }
            } catch (ApiException $e) {
                $apiProblem = new ApiProblem(401);
                $apiProblem->set('detail', 'Invalid refresh token');
    
                return (new ApiResponseFactory())->createResponse($apiProblem);
            }
            $token            = $this->get('jafar_guarded_authentication.encoder')->encode(['username' => $username]);
            $refreshToken = $this->get('jafar_guarded_authentication.encoder')->encode(
                ['username' => $username],
                'Refresh'
            );
            return new JsonResponse(['status' => 'Success', 'token' => 'Bearer '.$token, 'refresh-token' => $refreshToken]);
        }
}
```




