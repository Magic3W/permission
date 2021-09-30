

# Permission
The permission server provides role based access management to a microservice network, allowing you
to control what parts of the application network can be used or accessed by which users or applications.

Roles may be described as:

 * User identifiers like @{userid}
 * Application identifiers
 * Roles
 * Relationships (are you the owner or creator of a resource)

Resources are inherentily global, this means that permission will create a scope for every application
on the network in which it can perform operations but the other applications cannot.

## Installing / Getting started

You can start Permission with `docker-compose` like

```shell
docker build .
docker-compose up -d
```

The `d` stands for daemon and keeps docker running in the background, so Permission will continue
running even if you close the terminal. It's optional.

If you want the project to run without `docker` I recommend you still check into the `Dockerfile`
since that contains the detailed instructions on how we exactly set up our environment. But you'll
need [composer](https://getcomposer.com) to pull the dependencies for the project.

```shell
apt install php-mysql php-memcached memcached php-intl
composer install
nano bin/settings/environments.php # To edit the database credentials
```

### Initial Configuration

After installation, you can head to the URL of the application (in docker it will be `localhost:8086` by default)
and you should be presented with the set up page. The set up will guide you through the basic 
configuration to create a basic configuration.

## Developing

To help the development of permission, please start of by reaching out to us. We do
only mirror our code to Github, but internally use Phabricator for code reviews and
similar. This means that accepting Pull Requests is cumbersome for us and will
remove credit from you. Just shoot us an email at cesar@magic3w.com and I'll help
you get started with Phabricator.

Once you have an account on our Phabricator and have `Arcanist` set up, you can just 
pull the repository like normal:

```shell
git clone ssh://git@phabricator.magic3w.com/source/permission.git
cd permission/
composer install
```

To start working on your own changes I recommend you check out a new branch on your
local repository.

```shell
git checkout -b dev/my-new-feature
```

You can now make the changes you need. Test the code locally, once you're satisfied,
you can send us a `diff` (Phabricator's version of a Pull Request) by following these
steps.

```shell
composer test # This will tell you if your code matches our guidelines.
git add .
git commit -m "Your commit message"
arc diff
```

You will be prompted to explain the changes you made, how to test them and who should
review your change. You can leave the reviewer empty if you're unsure.

### Building

Once you've made your changes and wish to test the application, just run:

```shell
docker build .
```

You should get a built container out of this that you can now publish to your
server or push to a docker registry from which it can be downloaded. If you're
running on a Kubernetes cluster, you can also push it to the cluster directly.

## Features

* Create and manage resources
* Create and manage mnemonics (names for resources and identities)
* Grant or deny identities access to resources

This server is intended to compile an access file that includes the inherited
permissions to a certain resource, allowing us to cache the access control for 
a certain resource.

## Wishlist

This server currently doesn't support, but it'd be nice if it did:

 * Webhooks


## Contributing

Thank you so much for contributing to making the project better. We appreciate
all contributions, big and small.

Code submissions are always welcome. As stated above, I would recommend you reach
out and get onboarded to our Phabricator, but if you just want to make a small
submission you can always send a pull request on Github.

## Links

- Project homepage: https://phabricator.magic3w.com/source/permission/
- Repository: https://phabricator.magic3w.com/source/permission/
- Issue tracker: https://phabricator.magic3w.com/maniphest/task/edit/form/default/
  - You will need an account to access the Phabricator
  - In case of sensitive bugs like security vulnerabilities, please contact
    cesar@magic3w.com directly instead of using issue tracker. We value your effort
    to improve the security and privacy of this project!
- Related projects:
  - Authentication server: https://phabricator.magic3w.com/source/phpas/
  - Profile server: https://phabricator.magic3w.com/source/Switches/


## Licensing

The code in this project is licensed under GPL license.
