## TechCamp Memphis CFP

Based on [OpenCFP](https://github.com/opencfp/opencfp)

### Usage

* Clone this repo
* Run `composer install`
* Run `./vendor/bin/homestead make`
* Ensure `Homestead.yaml` contains everything from `Homestead.default`
* Run ```vagrant up``` from the root of the project
* Open [http://cfp.dev](http://cfp.dev) in a browser
* Register a new account
* Run ```vagrant ssh``` from the root of the project
* Run ```cd cfp.dev```
* Run ```bin/opencfp admin:promote email@you-registered.com```
* You can now log in as an admin


### Deployments

You will need [Laravel/Envoy](http://laravel.com/docs/5.0/envoy) installed locally. Envoy itself requires PHP to be installed locally.

You will need to contact [@Svpernova09](https://github.com/svpernova09) to get your public SSH key added.

Once you have installed Envoy and your key has been added:

* Run ```envoy run deploy:prod```
