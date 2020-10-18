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

* Clone this repository at an accesible folder

```sh
$ git clone https://github.com/FrameNetBrasil/webtool.git
$ cd webtool
```
* If necessary, modify the docker-compose configuration file (.env)

* Start the container

```sh
$ docker-compose up
```

* Application configuration files will be created from dist

  * webtool/core/conf/conf.php
  * webtool/apps/webtool/conf/conf.php
  
* Access the app at http://localhost:8001 (with user = webtool password = test)

## Built With

* PHP 7.4
* MariaDb 10.4
* PhpMyAdmin 5.0.1
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
