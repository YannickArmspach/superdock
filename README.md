# SUPERDOCK
CLI PROJECT MANAGER
 
local environment for symfony, drupal and wordpress, synchronize directories and databases from/to and deploy code with version management

# Install

## Requirements:

Docker Desktop ```v4.11.1```

Experimental Features
- [x] Use Docker Compose V2
- [x] Use the new Virtualization framework
- [x] Enable VirtioFS accelerated directory sharing

## Steps

Clone this project in ~/superdock

```sh
git clone git@github.com:YannickArmspach/superdock.git ~/superdock
```
Go to superdock directory

```sh
cd ~/superdock
```

Install dependencies

```sh
composer install
```

Add `sd` alias to your ~/.bash_profile or ~/.zshrc

```sh
echo 'alias sd="php ~/superdock/bin/superdock.php"' >> ~/.bash_profile

-or-

echo 'alias sd="php ~/superdock/bin/superdock.php"' >> ~/.zshrc
```

# Use

## Up
Go in project folder and start your local environement. 
```
$ sd up
```

## Down
Stop your local project
```
$ sd down
```

## Kill
kill all process
```
$ sd kill
```

## Deploy*
Deploy your code on each environement
```sh
$ sd deploy $env
```

## Sync from*
Sync database and media from (staging/preproduction/production) to local
```sh
$ sd sync-from $env
```

## Sync to*
Sync database and media from local to (staging/preproduction/production)
```sh
$ sd sync-to $env
```

## DB install**
Install local dump in local environement
```sh
$ sd db-install $env/$name.sql
```

## DB dump**
Dump database in superdock/databases/$env directory
```sh
$ sd db-dump $env
```

## New
Create new project
```
$ sd new [id]
```

## Init
Create new from existing project folder
```
$ sd init
```


##### * require access by ssh key. Send your public key to server admin of the project.
##### ** add your dump in superdock/databases directory


