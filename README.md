# FNBr Webtool
Webtool is an annotation and database management application developed by [FrameNet Brasil Project](http://www.ufjf.br/framenetbr-eng/), which can be accessed using any web browser,
without the need to install any additional software. Webtool handles multilingual framenets and constructicons.

Webtool app is implemented using [Framework Maestro](https://github.com/frameworkmaestro/maestro3/), a PHP7 framework developed at Federal University of Juiz de Fora (Brazil).

This repository contains a customized Maestro copy. Webtool app is localized at apps/folder.

## Installation

To create a local installation for Webtool:

⋅⋅* Clone repository at an accesible web server folder (e.g. public_html/webtool)

```sh
$ git clone https://github.com/FrameNetBrasil/webtool.git
```

⋅⋅* Run composer for Maestro

```sh
$ cd webtool
$ composer install
```

⋅⋅* Run composer for Webtool

```sh
$ cd webtool/apps/webtool
$ composer install
```

⋅⋅* Create conf files from dist

```sh
$ cd webtool/core/conf
$ cp conf.dist.php conf.php

$ cd webtool/apps/webtool/conf
$ cp conf.dist.php conf.php
```

⋅⋅* Restore/create the MySQL database from the dump located at webtool/apps/webtool/doc/database/webtool_dump.zip

⋅⋅* Configure database access at webtool/apps/webtool/conf/conf.php

```sh
    'db' => [
        'webtool' => [
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'dbname' => 'webtool_db',
            'user' => 'webtool',
            'password' => '', // inform the password
            ...
        ]
    ],
```
 
Access the app (e.g. http://localhost/webtool/index.php/webtool/main with user = webtool password = test)


## Tutorials

See [this YouTube channel](https://www.youtube.com/playlist?list=PLbRWTx8_CBTniSlJdlhBqJNe7A-AjKizD) for tutorials on the main functions of the WebTool.


## License

GNU GPLv3 - See the [COPYING](COPYING) file for license rights and limitations.
