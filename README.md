# SUPERDOCK
CLI PROJECT MANAGER
 
local environment for symfony, drupal and wordpress, synchronize directories and databases from/to and deploy code with version management

# Install

### Installation requirements:

  - [x] [Docker](https://docs.docker.com/install/) Local environement
  - [x] [Docker Machine](https://docs.docker.com/machine/install-machine/) CPU stability and better hibernation
  - [x] Docker Compose
  - [x] Virtualbox  
  - [x] [Mutagen](https://mutagen.io/documentation/introduction/installation) File sync to speed up docker page load <2s


## Install for users:
```sh
curl -LO https://github.com/YannickArmspach/superdock/raw/main/dist/superdock.phar && mv superdock.phar /usr/local/bin/superdock && chmod +x /usr/local/bin/superdock && superdock core install
```

## Install for contributors:
> in developement mode, scripts will be overwrite by files locate in ~/superdock folders
```sh
$ git clone git@github.com:YannickArmspach/superdock.git ~/superdock
```
> Use ```make install``` to compile and update superdock. 

> For live update add the following alias in your ~/.bash_profile or ~/.zshrc and run command with ***sdx*** instead of ***superdock***
```alias sdx="php ~/superdock/bin/superdock.php"```

# Command

## New
> Create new project
```
$ superdock new [id]
```

## Init
> Create new from existing project folder
```
$ superdock init
```

## Up
> Go in project folder and start your local environement. 
```
$ superdock up
```

## Down
> Stop your local project
```
$ superdock down
```

## Kill
> kill all process
```
$ superdock kill
```

## Deploy*
> Deploy your code on each environement
```sh
$ superdock deploy staging

$ superdock deploy preproduction

$ superdock deploy production
```

## Sync from*
> Sync database and media from (staging/preproduction/production) to local
```sh
$ superdock sync-from staging

$ superdock sync-from preproduction

$ superdock sync-from production
```

## Sync to*
> Sync database and media from local to (staging/preproduction/production)
```sh
$ superdock sync-to staging

$ superdock sync-to preproduction

$ superdock sync-to production
```

##### * require access by ssh key. Send your public key to server admin of the project.
