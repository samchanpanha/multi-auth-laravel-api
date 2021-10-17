<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[CMS Max](https://www.cmsmax.com/)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

How to setup Multi-Auth for Laravel APIs
## STEP 1
Add passport to your Laravel 8+ project
## composer require laravel/passport 
Lower versions can install with the following if you have composer dependency issues while trying to install
## composer require laravel/passport "~9.0"
## STEP 2
Go to “app/Providers/AuthServiceProvider” add the passport routes function and then define your roles and descriptions for each role and then specify the default role that would be attached if a role is not explicitly requested for.
<?php	
	public function boot()
	{
	$this->registerPolicies();
	
	Passport::routes();
	Passport::tokensCan([
	'staff' => 'Access Admin Backend',
	'customer' => 'Access Customer App',
	'role' => 'Description for role',
	]);
	
	Passport::setDefaultScope([
	'customer',
	]);
	
	
	}
?>
## STEP 3
## Go to “config/auth.php”
In the “defaults” section. Set the guard to default scope name you passed earlier
<?php 
'defaults' => [
    'guard' => 'customer',
    'passwords' => 'users',
],
?>
## STEP 4
In the “guards” section. You would see web and API, you should add the other roles and for the “driver” you set it to passport and then the provider should be the name of the provider which would be configured in the next step. It makes sense to set the provider name to be the same name as the role as in the example below

<?php	
	'guards' => [
	'web' => [
	'driver' => 'session',
	'provider' => 'users',
	],
	
	'api' => [
	'driver' => 'passport',
	'provider' => 'users',
	'hash' => false,
	],
	
	'staff' => [
	'driver' => 'passport',
	'provider' => 'staff',
	],
	
	'customer' => [
	'driver' => 'passport',
	'provider' => 'customer',
	],
	
	],
?>
## STEP 5
In the providers' section, add a provider for each role as well. This the driver should be eloquent and the model should be the model of the tables you want each role to authenticate from.

<?php	
	'providers' => [
	'customer' => [
	'driver' => 'eloquent',
	'model' => App\customer::class,
	],
	
	'staff' => [
	'driver' => 'eloquent',
	'model' => App\staff::class,
	],
	],
?>
For each of the models to be used, extend “Authenticatable” and then use the traits “HasApiTokens” and “Notifiable”.

<?php	
	
	namespace App;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Foundation\Auth\User as Authenticatable;
	use Illuminate\Notifications\Notifiable;
	use Illuminate\Support\Carbon;
	use Illuminate\Support\Facades\Storage;
	use Laravel\Passport\HasApiTokens;
	
	class customer extends Authenticatable
	{
	use HasApiTokens, Notifiable;
	
	
	}
?>
## STEP 6
Create a middleware

php artisan make:middleware checkForAllScopes

## STEP 7
Add the code below. It checks that the authenticated user is allowed to make the request else it fails

<?php	
	
	namespace App\Http\Middleware;
	
	use Closure;
	use Illuminate\Auth\AuthenticationException;
	
	
	class CheckForAllScopes
	{
	/**
	* Handle the incoming request.
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  \Closure  $next
	* @param  mixed  ...$scopes
	* @return \Illuminate\Http\Response
	*
	* @throws \Illuminate\Auth\AuthenticationException|\Laravel\Passport\Exceptions\MissingScopeException
	*/
	public function handle($request, $next, ...$scopes)
	{
	    if(! $request->user() || ! $request->user()->token()) {
	throw new AuthenticationException;
	}
	
	foreach ($scopes as $scope) {
	if ($request->user()->tokenCan($scope)) {
	return $next($request);
	}
	}
	
	return response( array( "message" => "Not Authorized." ), 403 );
	
	}
	}
?>
## STEP 8
Go to “app/Http/Kernel.php” add the new scope to the routed middleware section

<?php	
	protected $routeMiddleware = [
	'auth' => \App\Http\Middleware\Authenticate::class,
	...
	'scopes' => CheckForAllScopes::class,
	];
?>
## STEP 9
Go to “routes/api.php” then put the auth middleware with the right guard for the role

<?php	
	Route::group(['prefix' => 'v1'],function(){
	
	//general unauthenticated routes here
	
	Route::group(['prefix' => 'customer'],function(){
	
	Route::post('sign-up','CustomerController@signUp');
	//unauthenticated routes for customers here
	
	Route::group( ['middleware' => ['auth:customer','scope:customer'] ],function(){
	// authenticated customer routes here
	Route::post('dashboard','CustomerController@dashboard');
	});
	});
	
	Route::group(['prefix' => 'staff'],function(){
	
	Route::post('sign-up','StaffController@signUp');
	//unauthenticated routes for customers here
	
	Route::group( ['middleware' => ['auth:staff','scope:staff'] ],function(){
	// authenticated staff routes here
	Route::post('dashboard','StaffController@dashboard');
	});
	});
	
	});
?>
## STEP 10
In your controller, you can retrieve a reference to the object by calling the request’s user. It would return an instance of the table that you used in authenticating.

<?php	
	public function dashboard(Request $request) {
	$customer = $request->user();
	// the full object of the customer as containted in the able would
	// be available now
	
	}
?>
## STEP 11
When generating tokens pass the role as the scope to passport’s “CreateToken” method as in the example below
<?php	
	public function signIn(Request $request)
	{
	$email = $request->input('email');
	$password = $request->input('password');
	
	$rules = [
	'email' => 'required|email:rfc,dns|max:255',
	'password' => ['required'],
	];
	
	$validator = Validator::make($request->all(), $rules,$this->validationMessages());
	
	if ($validator->fails()) {return  response()->json(["message" => $validator->errors()->first()],400);}
	
	if(customer::where('email',$email)->count() <= 0 ) return response( array( "message" => "Email number does not exist"  ), 400 );
	
	$customer = customer::where('email',$email)->first();
	
	if(password_verify($password,$customer->password)){
	$customer->last_login = Carbon::now();
	$customer->save();
	return response( array( "message" => "Sign In Successful", "data" => [
	"customer" => $customer,
	
	// Below the customer key passed as the second parameter sets the role
	// anyone with the auth token would have only customer access rights
	"token" => $customer->createToken('Personal Access Token',['customer'])->accessToken
	]  ), 200 );
	} else {
	return response( array( "message" => "Wrong Credentials." ), 400 );
	}
?>
All done!
Now you can authenticate with various tables for different roles.


