# DBAdventure [![Build Status](https://travis-ci.org/DBAdventure/api.svg?branch=master)](https://travis-ci.org/DBAdventure/api)

DBAdventure, the backend api!


## Requirements

  * PHP 7.0 or higher;
  * PDO-PgSQL PHP extension enabled;
  * and the [usual Symfony application requirements][1].


## Configuration

Alternatively, you can [configure a fully-featured web server][2] like Nginx
or Apache to run the application.


## Installation

You must have created a [pgsql database][3].

```
$ ./scripts/setup.sh
$ psql -U DB_USER -W DB_NAME < var/sql/schema.sql
$ psql -U DB_USER -W DB_NAME < var/sql/map.sql
$ psql -U DB_USER -W DB_NAME < var/sql/data.sql
$ psql -U DB_USER -W DB_NAME < var/sql/todo.sql # Currently spells 
```


## Tests

Execute this command to run tests:

```bash
$ ./scripts/test.sh
```

To run static tests (PHPMD, PHPCS)
```bash
$ ./scripts/test.sh static
```

To run unit tests
```bash
$ ./scripts/test.sh unit
```


[1]: https://symfony.com/doc/current/reference/requirements.html
[2]: https://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html
[3]: https://help.ubuntu.com/community/PostgreSQL
