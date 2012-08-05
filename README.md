Introduction
============

Note: this branch is compatible with releases of Symfony2 in the 2.0.x branch.

This Bundle enables integration of the Google PHP and JS SDK's. Furthermore it
also provides a Symfony2 authentication provider so that users can login to a
Symfony2 application via Google. Furthermore via custom user provider support
the google login can also be integrated with other data sources like the
database based solution provided by FOSUserBundle.

Note that logging in a user requires multiple steps:

  1. the user must be logged into Google
  2. the user must connect his Google account to your site

Please also refer to the official documentation of the SecurityBundle, especially
for details on the configuration:
http://symfony.com/doc/2.0/book/security/authentication.html

Installation
============

  1. Add this bundle and the Google PHP SDK to your ``vendor/`` dir:
      * Using the vendors script.

        Add the following lines in your ``deps`` file::

            {
            "require": {
                "bitgandtter/google-bundle": "dev-master"
            }
            }

  2. Run the composer to download the bundle
            
            $ php composer.phar update
          
  
  3. Add this bundle to your application's kernel:

          // app/ApplicationKernel.php
          public function registerBundles()
          {
              return array(
                  // ...
                  new FOS\GoogleBundle\FOSGoogleBundle(),
                  // ...
              );
          }
          
  4. Add the following routes to your application and point them at actual controller actions
          
          #application/config/routing.yml
          _security_check:
              pattern:  /login_check
          _security_logout:
              pattern:  /logout

          #application/config/routing.xml
          <route id="_security_check" pattern="/login_check" />
          <route id="_security_logout" pattern="/logout" />     

  5. Configure the `google` service in your config:

          # application/config/config.yml
          fos_google:
      	    app_name: appName
      	    client_id: 123456789
      	    client_secret: s3cr3t
      	    state: auth
      	    access_type: online
      	    scopes: [userinfo.email, userinfo.profile]
      	    approval_prompt: auto
      	    redirect_uri: http://yoursite.com/login_check

  6. Add this configuration if you want to use the `security component`:

          # application/config/config.yml
          security:
              factories:
                  - "%kernel.root_dir%/../vendor/bundles/FOS/GoogleBundle/Resources/config/security_factories.xml"

              firewalls:
                  public:
                      # since anonymous is allowed users will not be forced to login
                      pattern:   ^/.*
		      fos_google:
			        provider: google

              access_control:
                  - { path: ^/secured/.*, role: [IS_AUTHENTICATED_FULLY] } # This is the route secured with fos_google
                  - { path: ^/.*, role: [IS_AUTHENTICATED_ANONYMOUSLY] }

     You have to add `/secured/` in your routing for this to work. An example would be...
     
              _google_secured:
                  pattern: /secured/
                  defaults: { _controller: AcmeDemoBundle:Welcome:index }

  7. Optionally define a custom user provider class and use it as the provider or define path for login

          # application/config/config.yml
          security:
              factories:
                    - "%kernel.root_dir%/../vendor/bundles/FOS/GoogleBundle/Resources/config/security_factories.xml"

              providers:
                  # choose the provider name freely
		              google:
		                id: google.user # see "Example Customer User Provider using the FOS\UserBundle" chapter further down

              firewalls:
                  public:
                      pattern:   ^/.*
                      fos_google:
			            provider: google
                      anonymous: true
                      logout: true 

  8. Optionally use access control to secure specific URLs


          # application/config/config.yml
          security:
              # ...
              
              access_control:
                  - { path: ^/google/,           role: [ROLE_GOOGLE] }
                  - { path: ^/.*,                  role: [IS_AUTHENTICATED_ANONYMOUSLY] }

Include the login button in your templates
------------------------------------------

Just add the following code in one of your templates:

    {{ google_login_button() }}

This link its only a shotcut to generate a url to the real handler controller inside
the GoogleBundle. You need to configure a route to this controller, the bundle
propose one taht you can use:

#GoogleBundle/Resources/routing.xml

<?xml version="1.0" encoding="UTF-8" ?>

<routes xmlns="http://symfony.com/schema/routing"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/routing http://symfony.com/schema/routing/routing-1.0.xsd">

    <route id="_googleLogin" pattern="/google/login">
        <default key="_controller">FOSGoogleBundle:Security:login</default>
    </route>

</routes>

This controller handle the all process

Example Customer User Provider using the FOS\UserBundle
-------------------------------------------------------

This requires adding a service for the custom user provider which is then set
to the provider id in the "provider" section in the config.yml:

    services:
        google.user:
	  class: class: Acme\MyBundle\Security\User\Provider\googleProvider
	  arguments:
	      google: @fos_google.api
	      userManager: @fos_user.user_manager
	      validator: @validator
	      em: @doctrine.orm.entity_manager

    <?php
	Acme\MyBundle\Security\User\Provider;
	use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
	use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
	use Symfony\Component\Security\Core\User\UserProviderInterface;
	use Symfony\Component\Security\Core\User\UserInterface;

	class GoogleProvider implements UserProviderInterface
	{
	  /**
	   * @var \GoogleApi
	   */
	  protected $googleApi;
	  protected $userManager;
	  protected $validator;
	  protected $em;

	  public function __construct( $googleApi, $userManager, $validator, $em )
	  {
	    $this->googleApi = $googleApi;
	    $this->userManager = $userManager;
	    $this->validator = $validator;
	    $this->em = $em;
	  }

	  public function supportsClass( $class )
	  {
	    return $this->userManager->supportsClass( $class );
	  }

	  public function findUserByGIdOrEmail( $gId, $email = null )
	  {
	    $user = $this->userManager->findUserByUsernameOrEmail( $email );
	    if ( !$user )
	      $user = $this->userManager->findUserBy( array( 'googleID' => $gId ) );
	    return $user;
	  }

	  public function loadUserByUsername( $username )
	  {
	    try
	    {
	      $gData = $this->googleApi->getOAuth( )->userinfo->get( );
	    }
	    catch ( \Exception $e )
	    {
	      $gData = null;
	    }

	    $user = $this->findUserByGIdOrEmail( $username, isset( $gData['email'] ) ? $gData['email'] : null );

	    if ( !empty( $gData ) )
	    {
	      if ( empty( $user ) )
	      {
      		$user = $this->userManager->createUser( );
      		$user->setEnabled( true );
      		$user->setPassword( '' );
      		$user->setSalt( '' );
	      }

	      if ( isset( $gData['id'] ) )
	      {
		      $user->setGoogleID( $gData['id'] );
	      }
	      if ( isset( $gData['name'] ) )
	      {
      		$nameAndLastNames = explode( " ", $gData['name'] );
      		if ( count( $nameAndLastNames ) > 1 )
      		{
      		  $user->setFirstname( $nameAndLastNames[0] );
      		  $user->setLastname( $nameAndLastNames[1] );
      		  $user->setLastname2( ( count( $nameAndLastNames ) > 2 ) ? $nameAndLastNames[2] : "" );
      		}
      		else
      		{
      		  $user->setFirstname( $nameAndLastNames[0] );
      		  $user->setLastname( "" );
      		  $user->setLastname2( "" );
      		}
	      }
	      if ( isset( $gData['email'] ) )
	      {
      		$user->setEmail( $gData['email'] );
      		$user->setUsername( $gData['email'] );
	      }
	      else
	      {
      		$user->setEmail( $gData['id'] . "@google.com" );
      		$user->setUsername( $gData['id'] . "@google.com" );
	      }

	      if ( count( $this->validator->validate( $user, 'Google' ) ) )
	      {
		// TODO: the user was found obviously, but doesnt match our expectations, do something smart
		throw new UsernameNotFoundException( 'The google user could not be stored');
	      }
	      $this->userManager->updateUser( $user );
	    }

	    if ( empty( $user ) )
	    {
	      throw new UsernameNotFoundException( 'The user is not authenticated on google');
	    }

	    return $user;
	  }

	  public function refreshUser( UserInterface $user )
	  {
	    if ( !$this->supportsClass( get_class( $user ) ) || !$user->getGoogleId( ) )
	    {
	      throw new UnsupportedUserException( sprintf( 'Instances of "%s" are not supported.', get_class( $user ) ));
	    }

	    return $this->loadUserByUsername( $user->getGoogleId( ) );
	  }
	}


Finally one also needs to add a getGoogleId() and setFBData() method to the User model.
The following example also adds "firstname" and "lastname" properties:

    <?php

	Acme\MyBundle\Entity;
	use Doctrine\Common\Collections\ArrayCollection;
	use FOS\UserBundle\Entity\User as BaseUser;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity
	 * @ORM\Table(name="system_user")
	 */
	class User extends BaseUser
	{
	  /**
	   * @ORM\Id
	   * @ORM\Column(type="integer")
	   * @ORM\generatedValue(strategy="AUTO")
	   */
	  protected $id;

	  /**
	   * @ORM\Column(type="string", length=40, nullable=true)
	   */
	  protected $googleID;

	  /**
	   * @ORM\Column(type="string", length=100, nullable=true)
	   */
	  protected $firstname;

	  /**
	   * @ORM\Column(type="string", length=100, nullable=true)
	   */
	  protected $lastname;

	  /**
	   * @ORM\Column(type="string", length=100, nullable=true)
	   */
	  protected $lastname2;

	  public function __construct( )
	  {
	    parent::__construct( );
	  }

	  public function getId( )
	  {
	    return $this->id;
	  }

	  public function getFirstName( )
	  {
	    return $this->firstname;
	  }

	  public function setFirstName( $firstname )
	  {
	    $this->firstname = $firstname;
	  }

	  public function getLastName( )
	  {
	    return $this->lastname;
	  }

	  public function setLastName( $lastname )
	  {
	    $this->lastname = $lastname;
	  }

	  public function getLastName2( )
	  {
	    return $this->lastname2;
	  }

	  public function setLastName2( $lastname2 )
	  {
	    $this->lastname2 = $lastname2;
	  }

	  public function getFullName( )
	  {
	    $fullName = ( $this->getFirstName( ) ) ? $this->getFirstName( ) . ' ' : '';
	    $fullName .= ( $this->getLastName( ) ) ? $this->getLastName( ) . ' ' : '';
	    $fullName .= ( $this->getLastName2( ) ) ? $this->getLastName2( ) . ' ' : '';
	    return $fullName;
	  }

	  public function setGoogleID( $googleID )
	  {
	    $this->googleID = $googleID;
	  }

	  public function getGoogleID( )
	  {
	    return $this->googleID;
	  }


	  public function setSalt( $salt )
	  {
	    $this->salt = $salt;
	  }
	}

