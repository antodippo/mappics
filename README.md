# Mappics

[![Build Status](https://travis-ci.org/antodippo/mappics.svg?branch=master)](https://travis-ci.org/antodippo/mappics)
[![codecov](https://codecov.io/gh/antodippo/mappics/branch/master/graph/badge.svg)](https://codecov.io/gh/antodippo/mappics)

Mappics is a **map based** travel photos gallery, with automatic **place** and **weather description** of the very moment the photos are taken. 

It will process all the images placed in a specific directory and perform these operations:

- it fetches **Exif data**, (https://en.wikipedia.org/wiki/Exif), which usually contain camera model and settings, GPS information, and date and time the photo was taken
- it fetches **place description**. A short one (like `Goðafoss Waterfall` and a long one (like `Goðafoss Waterfall, Goðafossvegur, Öxará, Þingeyjarsveit, Norðurland eystra, Ísland`). This is based on the Exif GPS information stored in the photo and obtained from [Nominatim and OpenStreetMap APIs](https://nominatim.openstreetmap.org/)
- it fetches **weather forecast** in the place and moment the photo was taken. This is based on the Exif GPS information and the creation date of the photo, and obtained from the [DarkSky APIs](https://darksky.net/dev)
- it creates a **resized version** of the image and a **thumbnail**, and store them in a different folder from the original one

All these information are shown in galleries and pop up with photos and details, using [Leaflet](https://leafletjs.com/) javascript library and [Mapbox](https://www.mapbox.com/) maps. Frontend is based on [Bootstrap](https://getbootstrap.com) framework and backend is written in PHP on top of some [Symfony](https://symfony.com/) components.

You can see a working version here: http://pics.antodippo.com.

## Usage

Mappics provides a simple console command to process images. 
To configure and run the command you will have to:

1. obtain API keys from external services:
    - Dark Sky API: https://darksky.net/dev/register
    - Mapbox: https://account.mapbox.com/auth/signin/?route-to=%22/access-tokens/%22
2. deploy the application (see dedicated paragraph for details)
3. edit your `/.env` file adding the keys from step 1
4. place your images in the `<root>/var/galleries` directory (Mappics supports `jpg` and `png` images), organized in folders (they will become galleries)
5. run the console command `bin/console mappics:process-galleries` from the `<root>` directory
6. go to the home page and enjoy your photos!

If you have a lot of images this command can take a long time, so you may want to run it in background:

`bin/console mappics:process-galleries &`

or, if you want to set-it-and-forget-it and just upload photo every now and then, you could run it as a cron job (it has a lock which prevents multiple execution):

`0 * * * * /var/www/mappics/current/bin/console mappics:process-galleries` 

## Deploy

One option is to make Mappics run on Apache with PHP module. You will have to install and enable the following PHP extensions:

```
php-curl
php-mbstring
php-zip
php-gd
php-xml
php-exif
```

You can find a sample virtual host file in `/docker/vhost.conf` and a `Dockerfile` to have a better understanting of the system stack needed.

To deploy and update Mappics you can also find a simple configuration for [https://deployer.org](https://deployer.org), in `/deploy.sample.php`, so you will have to:

1. [install Deployer](https://deployer.org/docs/getting-started.html) 
2. edit the sample file adding you host information (see comments in file and [documentation](https://deployer.org/docs/hosts.html))
3. rename the file in `deploy.php`
4. run `dep deploy` (to install the latest version)

If you want to deploy a specific version you can also use the tag option: `dep deploy --tag="1.0.0"`

## Run on your local machine

The suggested way to run and work on Mappics in a local enviroment is [Docker](https://www.docker.com/):

1. [install Docker](https://www.docker.com/get-started) on your machine
2. clone the repository: `git clone git@github.com:antodippo/mappics.git`
3. build Mappics application: `make build`
4. run tests: `make test`
6. run application: `make start`
7. browse Mappics on http://localhost:8080
8. stop application: `make stop`

You should get an empty Mappics home page. To fill it, see the "Usage" paragraph.