# Address autocomplete

[![Latest Version on Packagist](https://img.shields.io/packagist/v/enflow/laravel-address.svg?style=flat-square)](https://packagist.org/packages/enflow/laravel-address)
![GitHub Workflow Status](https://github.com/enflow-nl/laravel-address/workflows/run-tests/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/enflow/laravel-address.svg?style=flat-square)](https://packagist.org/packages/enflow/laravel-address)

The `enflow/laravel-address` package provides a opinionated way on how to implement address fields with autocomplete. See the []

## Usage

#### Step 1 - Install the package
You can install the package via composer:

``` bash
composer require enflow/laravel-address
```

#### Step 2 - Config

You must publish the config to set the driver you which to use.

Pushing the config file:
``` bash
php artisan vendor:publish --provider="Enflow\Address\AddressServiceProvider" --tag="config"
```

You must specify your driver choice. We provide a driver for HERE out of the box. See the 'Driver' section how to obtain a API key.

#### Step 3 - Migration

Pushing the migration file:
``` bash
php artisan vendor:publish --provider="Enflow\Address\AddressServiceProvider" --tag="migrations"
```

#### Step 4 - Prepare your model
The `Address` model is provided free to use, and you are able to configure it just like a normal Eloquent relationship:

##### Migration
```php
Schema::table('companies', function (Blueprint $table) {
    // ...
    $table->foreignId('address_id')->constrained();
});
```

##### Model
```php
<?php

use Enflow\Address\Models\Address;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
```

#### Step 5 - Frontend
We **highly recommend** the use of our `@enflow/laravel-address-ui` frontend package. This package adds the ability to add a autocomplete option to your address input for the end-user to easily select their address.

For installation instructions, see https://github.com/enflow-nl/laravel-address-ui

## Drivers

We support the following drivers out of the box. This may be extended in the future, and we welcome PRs for new drivers.

#### HERE
[HERE Geocoding and Search](https://developer.here.com/products/geocoding-and-search) provides a fast and easy to use autosuggestion option. We recomend this option due it's relative low cost and ease of use, without additional legal terms.

Product page: https://developer.here.com/products/geocoding-and-search   
Pricing: https://developer.here.com/pricing

You may obtain a API token by [registrating for an account](https://developer.here.com/sign-up?create=Freemium-Basic&keepState=true&step=account).

#### Google Places
Google Places isn't support out of the box and is something we want to support in the future. PRs are welcome!

#### Mapbox
Mapbox isn't support out of the box and is something we want to support in the future. PRs are welcome!

### 

## Testing
``` bash
$ composer test
```

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security
If you discover any security related issues, please email michel@enflow.nl instead of using the issue tracker.

## Credits
- [Michel Bardelmeijer](https://github.com/mbardelmeijer)
- [All Contributors](../../contributors)

## About Enflow
Enflow is a digital creative agency based in Alphen aan den Rijn, Netherlands. We specialize in developing web applications, mobile applications and websites. You can find more info [on our website](https://enflow.nl/en).

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
