# laravel-bank
A CRUD bank library for laravel.
Uma biblioteca para CRUD de banco

## Prerequisites
- 1º: These middwares are needed:
- If your project does not have some of these middleware, it is necessary to add them.
```
auth.admin
```
- 2º: The following tables are required. The columns are the same as in the UBERSERVICOS project:
```
Bank
Country
Profile
Permission
PermissionProfile
```

## Getting Started
- In root of your Laravel app in the composer.json add this code to clone the project:

```

"repositories": [
		{
			"type":"package",
			"package": {
			  "name": "codificar/bank",
			  "version":"master",
			  "source": {
				  "url": "https://libs:ofImhksJ@git.codificar.com.br/laravel-libs/laravel-bank.git",
				  "type": "git",
				  "reference":"master"
				}
			}
		}
	],

// ...

"require": {
	// ADD this
	"codificar/bank": "dev-master",
},

```
- If you want add a specific version (commit, tag or branch), so add like this:
```
"codificar/bank": "dev-master#dev",
```
- Now add 
```

"autoload": {
		//...
		"psr-4": {
			// Add your Lib here
		   "Codificar\\Bank\\": "vendor/codificar/bank/src",
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
			Codificar\Bank\BankServiceProvider::class,
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
| `get` | View/html | /admin/banks | View to list all banks |
| `get` | View/html | /admin/banks/create | View to create a bank | 
| `get` | View/html | /admin/banks/update/{id} | View to update a bank |
| `get` | Api/json | /api/banks/filter | Endpoint to filter banks by id, name, code, agency_max_length,agency_digit_required, agency_digit_max_length, account_max_length, account_digit_required, account_digit_max_length, country_iso) |
| `resource` | Api/json | /api/banks | Api to get t|
| `get` | Api/json | /api/countries | Api to get the list of countries |

## Translation files route
| Type  | Return | Route  | Description |
| :------------ |:---------------: |:---------------:| :-----|
| `get` | Api/json | /libs/lang.trans/{file} | Api to get the translation files of laravel and use inside the vue.js |
