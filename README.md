Nette CRUD generator
==============
Nette CRUD generator allows you create Nette Framework 2.2.X based application for managing your databases or even more.

Data sources
--------------
- MySQL InnoDB tables (with foreign keys support)
- MySQL MyISAM tables (without foreign keys support)
- Doctrine2 entities (with automatic creating MySQL InnoDB tables)

Generated stuff:
--------------
- Presenters
- Models (Nette\Database or Doctrine2)
- Templates

Features:
--------------
- Two types of data sources (MySQL database & Doctrine2 entities)
- Building application for all tables or only some of them
- Two types of builded model ([Nette\Database\Table](https://github.com/nette/database) & [Doctrine2](https://github.com/doctrine/doctrine2))
- Building into module
- Highly customisable templates used for generating (presenters, models and templates)

Usage:
--------------
- Create new Nette Framework 2.2.X project using Composer: `composer create-project nette/sandbox my-project`
- Move to newly created project: `cd my-project`
- Add latest Nette CRUD generator using Composer: `composer require r-bruha/nette-generator @dev`
- Add latest [Kdyby\Replicator](https://github.com/Kdyby/Replicator) using Composer: `composer require kdyby/forms-replicator 1.2.*@dev`
- Start generator through CLI: `php.exe /path/to/project/vendor/r-bruha/nette-generator/index.php` and follow instruction