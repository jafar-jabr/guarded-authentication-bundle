Getting started
===============

Prerequisites
-------------

This bundle requires Symfony 3.3+ (and the OpenSSL library).

preparation
-----

### for start using this package you need to :
1- to create your won user table and to make it implements AdvancedUserInterface

2- to implement UserLoaderInterface in the user repository for loadUserByUsername() method for example :
```php
     /**
     * @param string $username
     * @return AdvancedUserInterface|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        $user = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
        return $user;
    }
```
3- create loginform class
```php
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('_username')
            ->add('_password', Types\PasswordType::class);
    }
```


Installation
------------

Add [`jafar/guard-authentication-bundle`](https://packagist.org/packages/jafar/guard-authentication-bundle)
to your `composer.json` file:

    composer require jafar/guard-authentication-bundle "dev-master"

Register the bundle in `app/AppKernel.php`:

``` php
public function registerBundles()
{
    return array(
        // ...
        new Jafar\Bundle\GuardedAuthenticationBundle\JafarGuardedAuthenticationBundle(),
    );
}
```

Bundle configuration
---------------------
Necessary configuration in your `config.yml` :

``` yaml
jafar_guarded_authentication:
   #address to the login form class
    login_form: AuthBundle\Form\LoginForm
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
in your `app/config/security.yml`
```yaml

security:
    encoders:
        AuthBundle\Entity\Users:  #your own user table
            algorithm: bcrypt #or whatever
    providers:
      # ...
        user_provider:
          entity:
              class: AuthBundle:Users #your own user table
    
    firewalls:
      # ...
        api:
            pattern: ^/api/
            anonymous: ~
            stateless: ~
            guard:
               authenticators:
                    - app.security.jwt_token_authenticator
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
#use this pass_phrase also in setting in config.yml file
make sure that you have Openssl enabled on you computer.

Usage
-----

### with the above mentioned setting now your authentication system start to work
after you create your routes for form login and API authentication 
- login form controller can look like:
``` php
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AuthBundle\Form\LoginForm;

/**
 * Class LoginController
 * @package AuthBundle\Controller
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
        $form = $this->createForm(LoginForm::class, [
            '_username' => $lastUsername,
        ]);
        return $this->render('AuthBundle:Pages:login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }
}

```

- API login controller can look like:
``` php
use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiProblem;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AuthBundle\Entity\Users;

class ApiLoginController extends Controller
{
    private $responseFactory;

    public function __construct(ApiResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function indexAction(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');
        $user = $this->getDoctrine()
            ->getRepository(Users::class)->findOneBy(['email' => $username]);
        if (!$user) {
            $apiProblem = new ApiProblem(401);
            $apiProblem->set('detail', 'Invalid email');
            return $this->responseFactory->createResponse($apiProblem);
        }
        $passwordValid = $this->get('security.password_encoder')->isPasswordValid($user, $password);
        if (!$passwordValid) {
            $apiProblem = new ApiProblem(401);
            $apiProblem->set('detail', 'Incorrect Password');
            return $this->responseFactory->createResponse($apiProblem);
        }
        $token = $this->get('jafar_guarded_authentication.encoder')->encode(['username' => $username]);
        return new JsonResponse(['status' => 'Success', 'token' => 'Bearer ' . $token]);
    }
}
```

Now you can submit the login form for authentication or send (curl or postman) request

```bash
curl -X POST http://localhost:8000/api/your_api_login_url -d username=your_email -d password=your_password
``` 

### Use the token

from now and on you have to include the JWT on each request to the Api protected firewall as an authorization header
 `Authorization: Bearer token`

Notes
-----

### token life time "ttl"

Each request after token expiration will result in a 401 response.
go to Api login again to get a new token.