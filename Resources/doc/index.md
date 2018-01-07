Getting started
===============

Prerequisites
-------------

This bundle requires php 7+, Symfony 3.4+ and OpenSSL library.

Installation
------------

Add [`jafar/guard-authentication-bundle`](https://packagist.org/packages/jafar/guard-authentication-bundle)
to your `composer.json` file:

    composer require jafar/guarded-authentication-bundle "version"

Check https://packagist.org/packages/jafar/guarded-authentication-bundle for last version

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
   #route name for Api home page
    api_home_page_route: ''
    # ssh key pass phrase
    pass_phrase:         '' #you just used to generate keys in command line
    # token ttl
    token_ttl:           3600 #time to live in second
```

Security configuration
-----------------------
in your `config/packages/security.yml`

```yaml

security:
    encoders:
        App\Entity\User:  #your own user Entity
            algorithm: bcrypt #or whatever
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
                    - app.security.jws_token_authenticator
        main:
            pattern: ^/
            anonymous: ~
            guard:
                authenticators:
                     - app.security.login_form_authenticator
                entry_point: app.security.login_form_authenticator
            logout:
                path: /logout
            remember_me:
                secret:   '%secret%'
                lifetime: 604800 # 1 week in seconds
                path:     /
```

Generate the SSH keys :
from your project directory run in command line "as administrator"
``` php
php bin/console jafar:generate-keys  your_pass_phrase     
```
#use this pass_phrase also in setting in `jafar_guarded_authentication.yaml` file
make sure that you have Openssl enabled on you computer.

Usage
-----
1- to create your won user entity and to make it implements
``` php
Jafar\Bundle\GuardedAuthenticationBundle\User\GuardedUserInterface
     
```

2- the user repository has to extends :
```php
    Jafar\Bundle\GuardedAuthenticationBundle\User\GuardedUserRepository
```
instead of `ServiceEntityRepository`

3- for login method in the controller you can use:
```php
   Jafar\Bundle\GuardedAuthenticationBundle\Form\GuardedLoginForm
```
and it can looks like:

``` php
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Jafar\Bundle\GuardedAuthenticationBundle\Form\GuardedLoginForm;

/**
 * Class LoginController
 * @package App\Controller
 */
class LoginController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $authUtils = $this->get('security.authentication_utils');
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();
        $form = $this->createForm(GuardedLoginForm::class, [
            '_username' => $lastUsername,
        ]);
        return $this->render('Pages:login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }
}

```

- API login controller can look like:
``` php
use App\Repository\UserRepository;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiProblem;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
        $user = $this->userRepository->findOneBy(['email' => $username]);
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
        $token = $this->get('jafar_guarded_authentication.encoder')->encode(['username' => $username]);
        return new JsonResponse(['status' => 'Success', 'token' => 'Bearer ' . $token]);
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
### if you have problem with Api Authentication (autherization header not sent)
you need to add
```bash
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTP:Authorization} ^(.*)
    RewriteRule .* - [e=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
</IfModule>
``` 
in your `public/.htaccess` file

### token life time "ttl"

Each request after token expiration will result in a 401 response.
go to Api login again to get a new token.
