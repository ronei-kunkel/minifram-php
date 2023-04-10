# minifram

## Requirements

composer
docker
docker-compose

## How to set up

### To install dependeces

run `composer update`

### To create .env file

run `composer env` and modify configurations

## How to run tests

run `composer test`

## Folder explain

## How to develop

## Considerations

In current moment, the path of routes are able to only receive 3 levels. Well... What i mean? If you make, in example, a route in web system that delete user you probably make this route to action: <http://localhost/{folder-name}/user/1/delete>, right?! But, if, in other example, you want make a page that list the mutual followers of user with you, probabbly the route it will be like: <http://localhost/{folder-name}/user/1/followers/mutual>.

You see? The first example have 3 levels in path 1: "user", 2: "1" and 3: "delete". This path works fine.

But the second example have 4 levels in path 1: "user", 2: "1", 3: "followers", 4: "mutual". This path probablly not work (has not been actively tested).

This behavior will be fixed in nexts updates to improve the development experience.
