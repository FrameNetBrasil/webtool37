# FNBr Webtool [Docker Container]
Webtool is an annotation and database management application developed by [FrameNet Brasil Project](http://www.ufjf.br/framenetbr-eng/), which can be accessed using any web browser,
without the need to install any additional software. Webtool handles multilingual framenets and constructicons.

Webtool app is implemented using [Framework Maestro](https://github.com/frameworkmaestro/maestro3/), a PHP7 framework developed at Federal University of Juiz de Fora (Brazil).

This repository contains a customized Maestro copy. Webtool app is localized at folder apps/webtool.

### Prerequisities


In order to run this container you'll need docker installed.

* [Windows](https://docs.docker.com/windows/started)
* [OS X](https://docs.docker.com/mac/started/)
* [Linux](https://docs.docker.com/linux/started/)

### Usage

Create a local installation for Webtool:

* Clone this repository at an accesible folder (e.g. home/webtool)

```sh
$ git clone https://github.com/FrameNetBrasil/webtool.git
```

* Create conf files from dist

```sh
$ cd /home/webtool/core/conf
$ cp conf.dist.php conf.php

$ cd /home/webtool/apps/webtool/conf
$ cp conf.dist.php conf.php
```

* Restore/create the MySQL database from the dump located at /home/webtool/apps/webtool/doc/database/webtool_github_bkp.zip

    - You must have a local database installation (MySQL or MariaDb)
    - Configure the database server to allow external connections
    - Create user "webtool" at database server (and choose a local password)
    - Restore/create database using webtool/<local-password> 

* Configure database access at /home/webtool/apps/webtool/conf/conf.php

```sh
    'db' => [
        'webtool' => [
            'driver' => 'pdo_mysql',
            'host' => 'host.docker.internal',
            'dbname' => 'webtool_db',
            'user' => 'webtool',
            'password' => '', // inform the password <local-password>
            ...
        ]
    ],
```

* Start the container

```sh
$ cd /home/webtool/devops/docker
$ docker-compose up
```
 
* Access the app at http://localhost:8001 (with user = webtool password = test)

#### Container Parameters

Parameters are available to the container (mainly the webserver port) at

```shell
/home/webtool/devops/docker/.env
```

## Built With

* PHP 7.4
* Framework Maestro 3.0
* Vue 2.7

## Tutorials

See [this YouTube channel](https://www.youtube.com/playlist?list=PLbRWTx8_CBTniSlJdlhBqJNe7A-AjKizD) for tutorials on the main functions of the WebTool.

## Find Us

* [GitHub](https://github.com/FrameNetBrasil)
* [FrameNet Brasil](http://www.ufjf.br/framenetbr-eng/)

## Contributing
* Create a new branch with a meaningful name `git checkout -b branch_name`.<br />
* Develop your feature on Xcode IDE  and run it .<br />
* Add the files you changed `git add file_name`.<br />
* Commit your changes `git commit -m "Message briefly explaining the feature"`.<br />
* Keep one commit per feature. If you forgot to add changes, you can edit the previous commit `git commit --amend`.<br />
* Push to your repo `git push --set-upstream origin branch-name`.<br />
* Go into [the Github repo](https://github.com/FrameNetBrasil/webtool.git) and create a pull request explaining your changes.<br />

## License

GNU GPLv3 - See the [COPYING](COPYING) file for license rights and limitations.
