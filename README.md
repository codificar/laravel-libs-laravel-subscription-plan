# laravel-subscription-plan
A CRUD bank library for laravel.
Uma biblioteca para CRUD de banco

## Prerequisites
- 1º: These middwares are needed:
- If your project does not have some of these middleware, it is necessary to add them.
```
auth.admin
```
- 2º: The following tables are required. The columns are the same as in the UBERSERVICOS project:
``
```

## Getting Started
- In root of your Laravel app in the composer.json add this code to clone the project:

```

"repositories": [
		{
			"type":"package",
			"package": {
				"name": "codificar/laravel-subscription-plan",
				"version":"master",
				"source": {
					"url": "https://libs:ofImhksJ@git.codificar.com.br/laravel-libs/laravel-subscription-plan.git",
					"type": "git",
					"reference":"master"
				}
			}
		},
	],

// ...

"require": {
	// ADD this
	"codificar/laravel-subscription-plan": "dev-master#62ab0ce9"
},

```
- If you want add a specific version (commit, tag or branch), so add like this:
```
"codificar/laravel-subscription-plan": "dev-master#dev",
```
- Now add 
```

"autoload": {
		//...
		"psr-4": {
			// Add your Lib here
		   	"Codificar\\LaravelSubscriptionPlan\\": "vendor/codificar/laravel-subscription-plan/src",
			//...
		}
	},
	//...
```
- Dump the composer autoloader

```
composer dump-autoload -o
```

Check if has the laravel publishes in composer.json with public_vuejs_libs tag:
```
	"scripts": {
		//...
		"post-autoload-dump": [
			"@php artisan vendor:publish --tag=public_vuejs_libs --force"
		]
	},
```

- Next, we need to add our new Service Provider in our `config/app.php` inside the `providers` array:

```
'providers' => [
		 ...,
			// The new package class
			Codificar\LaravelSubscriptionPlan\SubscriptionPlanServiceProvider::class,
		],
```
- Migrate the database tables

```
php artisan migrate
```

And finally, start the application by running:

```
php artisan serve
```

## Admin (web)
| Type  | Return | Route  | Description |
| :------------ |:---------------: |:---------------:| :-----|
| `get` | Api/json | api/admin/plan | Panel route to get plans
| `post` | Api/json | api/admin/storePlan | Plan route to save plan
| `get` | View/html | api/admin/plan/add | Panel page to add plan
| `get` | Api/json | api/admin/plan/filter | Route to filter plans
| `post` | Api/json | api/admin/plan/delete | Route to delete plans
| `post` | Api/json | api/admin/plan/update | Route to update plans
| `get` | View/html | api/admin/plan/signatures | Return list signatures pages

| `get` | View/html | admin/plan/add | Admin page to add plan
| `post` | View/html | admin/plan/store | Admin route to add plan
| `get` | View/html | admin/plan | Return the list page to list plans
| `get` | View/html | admin/plan/list | Return the list page to list plans
| `get` | View/html | admin/plan/filter | Route to filter plans
| `post` | View/html | admin/plan/delete/{id} | Route to delete plans
| `get` | View/html | admin/plan/{id} | Return the plan by id
| `get` | View/html | admin/signature | Return the page to list active signatures
| `post` | View/html | admin/signature/suspend/{id} | Route to suspend a signature

| `get` | View/html |  libs/provider/plans | Panel route to list plans availables to provider
| `get` | Api/json |  libs/provider/credit_card | List the provider credit cards
| `post` | Api/json |  libs/provider/plan/updatePlan | Route to update provider plan
| `post` | Api/json |  libs/provider/cancel_subscription | Route to cancel subscription
| `get` | View/html |  libs/provider/plan/{id} | List the plans details to provider

| `post` | Api/json | api/libs/provider/update_plan | Route to update provider plan
| `get` | Api/json | api/libs/provider/subscription_details | List the details of the active plan
| `get` | Api/json | api/libs/provider/plans/required_plans | Return the required plan to provider stay online
| `post` | Api/json | api/libs/provider/cancel_subscription | Route to provider cancel subscription
| `get` | Api/json | api/libs/provider/plans | List availables plans to provider

| `post` | Api/json | /api/libs/postback | Route to postback billet payment status

| `post` | Api/json | api/addCard | Route to add card
| `post` | Api/json | api/defaultCard | Route to get the default card
| `post` | Api/json | api/removeCard | route to remove card
