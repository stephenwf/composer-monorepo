# Composer monorepo

Usage:
```
$ composer install
```

from the root. Post install script will build a dependency tree of your `./packages` folder and run install in them in order. Any packages that are local will be symlinked
