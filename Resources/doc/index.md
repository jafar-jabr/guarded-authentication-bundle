Getting started
===============

Prerequisites
-------------

This bundle requires php 7+, Symfony 3.4+ and OpenSSL library,
but starting from v4.0.0 it requires php 8+, Symfony 6+ and OpenSSL library.

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
   # ssh key pass phrase
    pass_phrase:         '' # passphrase which you will choose when you will generate keys in command line
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
    enable_authenticator_manager: true
    password_hashers:
          Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'
    providers:
      # ...
        user_provider:
            entity:
               class: App\Entity\User #your own user Entity
               property: 'email' #or any other property
    firewalls:
      # ...
        api:
           pattern: ^/api/ #your pattern
           lazy: true
           stateless: ~
           custom_authenticators:
                 authenticators:
                      guarded.authentication.jws_token_authenticator
        main:
            pattern: ^/
            lazy: true
            form_login:
                failure_path: login #route name
                login_path: /user_login #route
            custom_authenticators:
                  authenticators:
                        guarded.authentication.login_form_authenticator
                  entry_point: guarded.authentication.login_form_authenticator
            logout:
               path: logout
               target: homepage
            remember_me:
                 secret:   '%kernel.secret%'
                 lifetime: 604800 # 1 week in seconds
                 path:     /
    # ...
    
    access_control:
            # ...
            - { path: ^/api/login, roles: PUBLIC_ACCESS }
            - { path: ^/api/token/refresh, roles: PUBLIC_ACCESS }
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
Symfony\Component\Security\Core\User\UserInterface;
Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
     
```

2- for login method in the controller
can look like:

``` php
/**
     * @param AuthenticationUtils $authenticationUtils
     * @return RedirectResponse|Response
     */
    public function loginPage(AuthenticationUtils $authenticationUtils): RedirectResponse|Response
    {
      $error = $authenticationUtils->getLastAuthenticationError();
      $lastUsername = $authenticationUtils->getLastUsername();
      return $this->render('auth/login_page.html.twig', ["error" => $error, "lastUsername" => $lastUsername]);
    }

```

3- login form html can be something like:

```html
{% if error %}
<div class="yo_search_for">{{ error.messageKey }}</div>
{% endif %}
 <form class="login-form" action="{{ path('login') }}" method="post" tabindex="500">
     <h3>{{ _('login_page.login') }}</h3>
     <div class="mail">
         <input type="text" name="_username" value="{{ lastUsername }}"/>
         <label>{{ _('login_page.phone') }}</label>
     </div>
     <div class="passwd">
         <input type="password" name="_password">
         <label>{{ _('login_page.password') }}</label>
     </div>
     <div class="passwd">
     <label>
         <input type="checkbox" name="_remember_me" checked/>
         Keep me logged in
     </label>
     </div>
     <input type="hidden" name="_target_path" value="homepage"/> {#route name to which to redirect after login#}
     <div class="submit">
         <input type="submit" href="#" value="{{ _('login_page.login') }}" class="dark button-login-style loginmodal-submit" />
     </div>
     <div class="forgot-remember">
         <div class="forgot">
             <a href="#" class="forgot-password-button">{{ _('login_page.forgot_password') }}?</a>
         </div>
     </div>
 </form>
```

- API login method in the controller you need to import:
```php
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSEncoder\JWSEncoderInterface;
use Jafar\Bundle\GuardedAuthenticationBundle\Exception\ApiException;
```
and it can look like:
``` php
 public function index(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordEncoder, JWSEncoderInterface $jwsTokenEncoder): JsonResponse
    {
        $username = $request->get('username');
        $password = $request->get('password');
        $user = $userRepository->loadUserByUsername($username);
        if (!$user) {
            return new JsonResponse('Invalid email', 401);
        }
        $passwordValid = $passwordEncoder->isPasswordValid($user, $password);
        if (!$passwordValid) {
            return new JsonResponse('Incorrect Password', 401);
        }
        try {
            $jwsData = ['username' => $username, ....];
            $token = $jwsTokenEncoder->encode($jwsData, 'Main');
            $refreshToken = $jwsTokenEncoder->encode($jwsData, 'Refresh');
        } catch (ApiException $e) {
            return new JsonResponse($e->getMessage(), 401);
        }
        return new JsonResponse([
            'status' => 'Success',
            'token' => 'Bearer '.$token,
            'refresh-token' => $refreshToken
        ]);
}
```
### with the above-mentioned setting now your authentication system start to work
after you create your routes for form login and update `jafar_guarded_authentication.yaml`
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
Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSRefresher\JWSRefresherInterface
``` 
you can generate refresh token,
```php
$refreshToken = Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSEncoder\JWSEncoderInterface->encode(['username' => $username], 'Refresh');
``` 
and to verify it after
```php
$data         = $refresher->decode($request);
$username     = $data['username'] ?? 'invalid';
``` 
