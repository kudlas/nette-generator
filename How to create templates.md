﻿**1. Creating root template directory and template loader**

This will create three new templates named **Blog**, **Game** and **Personal** and make them ready for use in Nette generator.

```
Root                     ← Root directory for project
├── Blog                 ← Root directory for templace named Blog
├── Game                 ← Root directory for templace named Game
├── Personal             ← Root directory for templace named Personal
└── TemplateLoader.php   ← Template loader for Nette generator
```

```php
<?php // TemplateLoader.php
class BlogTemplate {}
class GameTemplate {}
class PersonalTemplate {}
```

**2. Creating simple template directories and files**

There are several simple rules which must be followed to create working templates.

**2. 1. Directories handling**

All template directories are created (without content) in application directory except several exceptions.

**2. 1. 1. Directories starting with Module handling**

**2. 1. 1. 1. User choosed Example module**

In this case string **Module** will be replaced with real module name before creating directory in application directory.

Template path | Application path |
--------------|------------------|
/app/**Module**/presenters | /app/**ExampleModule**/presenters
/app/**Module**/models/**Module**/repositories | /app/**ExampleModule**/models/**ExampleModule**/repositories

**2. 1. 1. 2. User choosed no module**

In this case string **Module** will be removed from path before creating directory in application directory.

Template path | Application path |
--------------|------------------|
/app/**Module**/presenters | /app/presenters
/app/**Module**/models/**Module**/repositories | /app/models/repositories

**2. 1. 2. Directories starting with NDBT handling**

**2. 1. 2. 1. User choosed [Nette\Database](https://github.com/nette/database) models**

In this case string **NDBT** will be removed from path before creating directory in application directory.

Template path | Application path |
--------------|------------------|
/app/models/**NDBT**/repositories | /app/models/repositories
/app/**NDBTmodels**/repositories | /app/models/repositories

**2. 1. 2. 2. User choosed [Doctrine2](https://github.com/doctrine/doctrine2) models**

In this case directory and all it's subdirectories will be skipped.

Template path | Application path |
--------------|------------------|
/app/models/**NDBT**/repositories | Skipped
/app/**NDBTmodels**/repositories | Skipped

**2. 1. 3. Directories starting with D2 handling**

**2. 1. 3. 1. User choosed [Doctrine2](https://github.com/doctrine/doctrine2) models**

In this case string **D2** will be removed from path before creating directory in application directory.

Template path | Application path |
--------------|------------------|
/app/models/**D2**/repositories | /app/models/repositories
/app/**D2models**/repositories | /app/models/repositories

**2. 1. 3. 2. User choosed [Nette\Database](https://github.com/nette/database) models**

In this case directory and all it's subdirectories will be skipped.

Template path | Application path |
--------------|------------------|
/app/models/**D2**/repositories | Skipped
/app/**D2models**/repositories | Skipped

**2. 1. 4. Directories starting with Table handling**

In this case string **Table** will be replaced with real table name for each database table before creating directory in application directory.

Template path | Application path |
--------------|------------------|
/app/templates/**Table** | /app/templates/**ExampleOne**
 | /app/templates/**ExampleTwo**
 | /app/templates/**ExampleThree**

**2. 2. Files handling**

**2. 2. 1. Files with at least two extensions ending on .latte**

In this case file will be processed by [Latte](https://github.com/nette/latte) engine and extension **.latte** will be removed from file name before creating in application directory.

Even Latte files can be processed by [Latte](https://github.com/nette/latte) engine. In this case just use double syntax and **:n macros** for generator proccessing and signle syntax and **l: macros** for application processing (**:l macros** will be replaced with **:n macros** after generator proccessing).

Template path | Application path |
--------------|------------------|
/app/presenters/**BasePresenter.php.latte** | /app/presenters/**BasePresenter.php**
/app/models/**BaseRepository.php.latte** | /app/models/**BaseRepository.php**
/app/templates/**@layout.latte.latte** | /app/templates/**@layout.latte**
/app/config/**config.neon.latte** | /app/config/**config.neon**

**2. 2. 2. Files without any other extension ending on .latte**

In this case file will be only processed by [Latte](https://github.com/nette/latte) and created nowhere in application directory.

*Personally, I'm using this for generating Doctrine2 entities from MySQL database because Latte allows you execute any PHP code, so you can generate another files from Latte file but I really don't want this Doctrine2 entity generator in application itself.*

Template path | Application path |
--------------|------------------|
/app/models/**D2EntityBuilder.latte** | Skipped

**2. 2. 3. Files starting with Module handling**

**2. 2. 3. 1. User choosed Example module**

In this case string **Module** will be replaced with real module name before creating file in application directory.

Template path | Application path |
--------------|------------------|
/app/presenters/**ModuleBasePresenter.php.latte** | /app/presenters/**ExampleModuleBasePresenter.php**

**2. 2. 3. 2. User choosed no module**

In this case string **Module** will be removed from path before creating file in application directory.

Template path | Application path |
--------------|------------------|
/app/presenters/**ModuleBasePresenter.php.latte** | /app/presenters/**BasePresenter.php**

**2. 2. 4. Files starting with NDBT handling**

**2. 2. 4. 1. User choosed [Nette\Database](https://github.com/nette/database) models**

In this case string **NDBT** will be removed from path before creating file in application directory.

Template path | Application path |
--------------|------------------|
/app/models/**NDBTBaseRepository.php.latte** | /app/models/**BaseRepository.php**

**2. 2. 4. 2. User choosed [Doctrine2](https://github.com/doctrine/doctrine2) models**

In this case file will be skipped.

Template path | Application path |
--------------|------------------|
/app/models/**NDBTBaseRepository.php.latte** | Skipped


**2. 2. 5. Files starting with D2 handling**

**2. 2. 5. 1. User choosed [Doctrine2](https://github.com/doctrine/doctrine2) models**

In this case string **D2** will be removed from path before creating file in application directory.

Template path | Application path |
--------------|------------------|
/app/models/**D2BaseRepository.php.latte** | /app/models/**BaseRepository.php**

**2. 2. 5. 2. User choosed [Nette\Database](https://github.com/nette/database) models**

In this case file will be skipped.

Template path | Application path |
--------------|------------------|
/app/models/**D2BaseRepository.php.latte** | Skipped

**2. 2. 6. Files starting with Table handling**

In this case string **Table** will be replaced with real table name for each database table before creating file in application directory.

Template path | Application path |
--------------|------------------|
/app/presenters/**TablePresenter.php.latte** | /app/presenters/**ExampleOnePresenter.php**
 | /app/presenters/**ExampleTwoPresenter.php**
 | /app/presenters/**ExampleThreePresenter.php**
 
**3. Creating advanced template directories and files**

All previous directory and file rules can be combined, but they must be in folowed order **Module** => **NDBT/D2** => **Table** to create working templates.

**3. 1. Example: User choosed no module and NDBT models**

Template path | Application path |
--------------|------------------|
/app/**Module**/presenters/**BasePresenter.php.latte** | /app/presenters/**BasePresenter.php**
/app/**Module**/presenters/**TablePresenter.php.latte** | /app/presenters/**ExampleOnePresenter.php**
 | /app/presenters/**ExampleTwoPresenter.php**
 | /app/presenters/**ExampleThreePresenter.php**
/app/**Module**/templates/**@layout.lattel.latte** | /app/templates/**@layout.latte**
/app/**Module**/templates/**Table**/**view.latte.latte** | /app/templates/**ExampleOne**/**view.latte**
 | /app/templates/**ExampleTwo**/**view.latte**
 | /app/templates/**ExampleThree**/**view.latte**
/app/**Module**/models/**NDBTBaseRepository.php.latte** | /app/models/**BaseRepository.php**
/app/**Module**/models/**NDBTTableRepository.php.latte** | /app/models/**ExampleOneRepository.php**
 | /app/models/**ExampleTwoRepository.php**
 | /app/models/**ExampleThreeRepository.php**
/app/**Module**/models/**D2BaseRepository.php.latte** | Skipped
/app/**Module**/models/**D2TableRepository.php.latte** | Skipped

**3. 2. Example: Default generator templates**
```
root                                           ← Root directory for your template named Blog
├── app                                         ← Will be placed in root directory
│   ├── config                                  ← Will be placed in root/app directory 
│   │   ├── config.local.neon.latte             ← Will be processed by Latte and placed in root/app/config.neon 
│   │   └── config.neon.latte                   ← Will be processed by Latte and placed in root/app/config.neon  
│   ├── lang                                    ← Will be placed in root/app directory
│   │   └── generator.en_US.neon.latte          ← Will be processed by Latte and placed in root/lang/generator.en_US.neon
│   ├── Module                                  ← Will be replaced with module name and placed in root/app directory
│   │   │                                              or removed from path when no module was choosen
│   │   ├── models                              ← Will be placed in root/app/<module> directory
│   │   │   ├── Entities                        ← Will be placed in root/app/<module>/models directory
│   │   │   │   └── D2EntitesBuilder.latte      ← Will be processed by Latte and placed nowhere because there is only one
│   │   │   │                                          extension in file name
│   │   │   ├── D2BaseRepository.php.latte      ← Will be processed by Latte only if Doctrine2 source was choosen
│   │   │   │                                          because of D2 prefix and placed in
│   │   │   │                                          root/app/<module>/models/BaseRepository.php
│   │   │   ├── D2TableRepository.php.latte     ← Will be processed by Latte foreach table because of Table prefix and
│   │   │   │                                          only if Doctrine2 source was choosen because of D2 prefix
│   │   │   │                                          and placed in root/app/<module>/models/<Table>Repository.php
│   │   │   ├── NDBTBaseRepository.php.latte    ← Will be processed by Latte only if Nette\Database source was choosen
│   │   │   │                                          because of NDBT prefix and placed in
│   │   │   │                                          root/app/<module>/models/BaseRepository.php
│   │   │   ├── NDBTTableRepository.php.latte   ← Will be processed by Latte foreach table because of Table prefix and
│   │   │   │                                          only if Nette\Database source was choosen because of NDBT prefix
│   │   │   │                                          and placed in root/app/<module>/models/<Table>Repository.php
│   │   │   └── Permissions.php.latte           ← Will be processed by Latte and placed in
│   │   │                                              root/app/<Module>/models/Permissions.php
│   │   │── presenters                          ← Will be placed in root/app/<module> directory
│   │   │   ├── BasePresenter.php.latte         ← Will be processed by Latte and placed in
│   │   │   │                                          root/app/<module>/presenters/BasePresenter.php
│   │   │   └── TablePresenter.php.latte        ← Will be processed by Latte foreach table because of Table prefix
│   │   │                                              and placed in root/app/<module>/presenter/<Table>Presenter.php 
│   │   ├── templates                           ← Will be placed in root/app/<module> directory
│   │   │   ├── Table                           ← Will be processed by Latte foreach table because of Table prefix, replaced
│   │   │   │   │                                      by real database table name and placed in
│   │   │   │   │                                      root/app/<module>/template directory
│   │   │   │   ├── change.latte.latte          ← Will be processed by Latte foreach table because of previous directory Table
│   │   │   │   │                                      prefix and placed in root/app/<module>/template/<Table>/change.latte
│   │   │   │   └── list.latte.latte            ← Will be processed by Latte foreach table because of previous directory Table
│   │   │   │                                          prefix and placed in root/app/<module>/template/<Table>/list.latte
│   │   │   └── @layout.latte.latte             ← Will be processed by Latte and placed in
│   │   │                                              root/app/<module>/templates/@layout.latte
│   ├── router                                  ← Will be placed in root/app directory 
│   │   └── RouterFactory.php.latte             ← Will be processed by Latte and placed in root/app/router/RouterFactory.php
└── www                                         ← Will be placed in root directory
    ├── css                                     ← Will be placed in root/www directory  
    │   └── main.css                            ← Will be placed in root/www/css/main.css
    ├── images                                  ← Will be placed in root/www directory 
    │   └── icon.icon                           ← Will be placed in root/www/images/icon.ico
    └── js                                      ← Will be placed in root/www directory 
        └── main.js                             ← Will be placed in root/www/js/main.js
```

**4. Creating content of templates**

If you want to know what exatly you will get into your [Latte](https://github.com/nette/latte) templates go see these files:

- **src\Builder\Builder.php** and dump **$parameters** property
- **src\Examiner\MysqlExaminer** and watch code how I am handling MySQL database
- all files in **src\Utils\Object** which are used by MysqlExaminer above

**There will be some digest once... (when I finish it) :)**

You will get these variables in every [Latte](https://github.com/nette/latte) template.

**4. 1. string netteRoot**

Absolute path to Nette root directory.

**4. 2. array netteConfig**

Content of Nette main configuration file **config.neon** as array decoded by [Nette\Neon](https://github.com/nette/neon) decoder.

**4. 3. stdClass netteDatabase**

Database connection from [Nette\DI](https://github.com/nette/di).

```php
netteDatabase => stdClass
   hostname => ""
   username => ""
   password => ""
   database => "" 
```

**4. 4. integer source**

Data source for generator.

```php
const SOURCE_MYSQL_DISCOVERED = 1,
      SOURCE_MYSQL_CONVENTIONAL = 2,
      SOURCE_DOCTRINE2 = 3;
```

**4. 5. array tables**

You will get array of **\Bruha\Generator\Utils\Object\Table** objects, which represents database table.

```php
class Table {
	public $name;
	public $sanitizedName;
	public $comment;
	public $columns;
	public $state;

	public function __construct($name = NULL, $comment = NULL, array $colums = [], $state = NULL) {
		$this->name = $name;
		$this->sanitizedName = implode('', array_map(function($value) { return ucfirst($value); }, explode('_', $name)));
		$this->comment = $comment;
		$this->columns = $colums;
		$this->state = $state;
	}
}
```

Each tables have columns, so you will get them as array of **\Bruha\Generator\Utils\Object\Column** objects, which represents table column.

```php
class Column {
	public $name;
	public $sanitizedName;
	public $type;
	public $nullable;
	public $keys;
	public $default;
	public $extra;
	public $comment;

	public function __construct($name = NULL, \Bruha\Generator\Utils\Object\Type $type = NULL, $nullable = NULL, array $keys = [], $default = NULL, $extra = NULL, $comment = NULL) {
		$this->name = $name;
		$this->sanitizedName = implode('', array_map(function($value) { return ucfirst($value); }, explode('_', $name)));
		$this->type = $type;
		$this->nullable = $nullable;
		$this->keys = $keys;
		$this->default = $default;
		$this->extra = $extra;
		$this->comment = $comment;
	}
}
```

Each column must have type, so you will get it as **\Bruha\Generator\Utils\Object\Type** type, which represents column type.

```php
class Type {
	public $name;
	public $length;
	public $extra;

	function __construct($name = NULL, $length = NULL, $extra = NULL) {
		$this->name = $name;
		$this->length = $length;
		$this->extra = $extra;
	}
}
```

Database keys are also very important so you will get them as array of **\Bruha\Generator\Utils\Object\Key** objects.

```php
class PrimaryKey {
	
}

class IndexKey {
	
}

class UniqueKey {
	
}

class ForeignKey {
	public $table;
	public $key;
	public $value;

	function __construct(\Bruha\Generator\Utils\Object\Table $table = NULL, $key = NULL, $value = NULL) {
		$this->table = $table;
		$this->key = $key;
		$this->value = $value;
	}
}
```

Each **\Bruha\Generator\Utils\Object\Key\ForeignKey** object stores reference to **\Bruha\Generator\Utils\Object\Table** for easy access to everything what you could need from refferenced table. Also there is $key property which is name of referenced column in referenced table and $value property which is name of column in referenced table which should be the best for showing instead of $key column.

**4. 6. \Bruha\Generator\Utils\Object\Table table**

You will get **\Bruha\Generator\Utils\Object\Table** object, which represents current table in templates which are processed for each table or FALSE for rest templates. (see **2. 2. 6** chapter).

**4. 7. integer target**

Type of generated models.

```php
const TARGET_NETTE_DATABASE = 1,
      TARGET_DOCTRINE2 = 2;
```

**4. 8. integer foreignKeys**

How to handle foreign keys.

```php
const FOREIGN_KEYS_TABLE = 1,
      FOREIGN_KEYS_SELECT = 2
```

**4. 9. string module**

Stores module name or empty string if no module was choosed.

**4. 10. string template**

Absolute path to choosed template directory.

**4. 11. Kdyby\Doctrine\EntityManager entityManager**

Kdyby\Doctrine\EntityManager object only if user choosed generating from Doctrine2 entities.






