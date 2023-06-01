

General information
---------------------------------------------------------------------

- Type: content management system/framework
- Author: Maxim Rysevets
- Developer: Maxim Rysevets
- Start of development: end of 2016
- Initial release: 2022-01-01
- Written in: PHP
- Supported OS: UNIX, Linux, Microsoft Windows
- Supported web servers: NGINX, Apache, IIS
- Supported databases: SQLite, MySQL
- Is open source project: yes
- License: proprietary software
- Website: [http://effcore.com](http://effcore.com)
- Main code repository: [https://bitbucket.org/effcore](https://bitbucket.org/effcore)
- Mirror of main code repository: [https://github.com/effcore-foundation](https://github.com/effcore-foundation)

**EFFCORE** — the next-generation mini-CMS (Content Management System)
and mini-CMF (Content Management Framework).
It was developed between 2016 and 2022.
Uses technologies such as: HTML5, CSS3, IP v6, SVG, SMIL,
UTF8, UTC, WAI-ARIA, NoSQL, Markdown, UML, Composer…

Can be used as a tool to create simple websites without the need for programming.
It can be used as a tool for creating web portals and web services with the need
to program additional functionality or search for ready-made solutions.

The name of the System is an acronym and comes from the phrase `effective core`.
The System was designed from scratch. Its main principle is the absence
of third-party code (for the exclusion of legal claims), as well as the lack
of third-party ideas (for the development of innovative solutions).

The incentive for the creation of the System was the massive
degradation in the development of Open Source projects.
The first reason is the distributed development team.
At its core, it is a group of people with different skill levels,
different views and a weak level of coordination in such projects.
The second reason is the approach to development itself.
Instead of an intensive path of development, they chose an extensive one.
An intensive development path involves the development of your own code and
its close integration with other components of the System.
The extensive way is to use ready-made libraries and assemble these
libraries into something unified. At the same time, only a part
of the functionality is used from each such library.
Each such library was created for abstract purposes.
As a result, the amount of code grows, the load on the equipment grows,
the number of errors grows, the reliability and security decreases,
and the functionality increases only slightly.

The main emphasis in the System is on obtaining maximum performance.
The evaluation criterion is a simple and understandable condition: the System
installed on the hosting with the cheapest tariff plan should generate
the main page in 0.01 second. This will allow up to ~100 requests per
second to be served simultaneously. The plan must meet the minimum
installation requirements and include basic features such as OPCache + JIT
and a Solid State Drive. The approximate cost of the tariff plan should
vary within $3-5 per month.


Content management
---------------------------------------------------------------------

Any page in the System (except for the administrative interface) can have
its own layout with a different number of regions. Each region can have
any number of different blocks. Each block is any element of the System
that has a block representation (menu, form, text, survey, and others).


Localization
---------------------------------------------------------------------

The System interface can be translated into any language. The System
already comes with some translations. You can change the interface language at
any time using the _Management → Locale_ section.

In turn, any page (except for the administrative interface) can
override the global settings and select a different language to match the language
of the content on this page. Each page is configured in the section
_Management → Data → Content → Pages_.

The System implements a more advanced "Plural" system.
This system allows you to insert any content from your function,
depending on the value of the variable in the phrase.
Example: `%%_number second%%_plural(number|s)` at `number = 1`
will return `1 second`, and at `number = 10`
will return `10 seconds`.

**There are two ways to set up a multilingual website/web portal**:

1. organize as many copies of pages and blocks within one domain as many
   languages need to be supported (for example, `http://example.com/EN/about`);
2. organize a separate copy of the System for each language
   domain (for example, `http://EN.example.com/about`).

**Note**: Both the first and second versions require duplicate pages and blocks in
different languages. This approach is justified because often different language
versions of content have differences not only in content, but also in structure.
For example, the main menu in one language version may have some menu items,
and in another — completely different — the names of these points and their
addresses, and their number will also differ.

In the module "Profile "Classic" you can see an example of the implementation
of multilingualism according to method #1.


Decoration
---------------------------------------------------------------------

There is no such thing as a theme in the System.
Instead of themes, a special kind of module called "Profile" is used.
Any profile can contain the following types of elements:
- pages;
- page layouts;
- blocks (menu, text, audio, video, galleries, selections, polls, etc.);
- colors and color profiles;
- templates in `*.tpl`/`*.data` files;
- user styles in `*.css`/`*.cssd` files;
- user scripts in `*.js`/`*.jsd` files;
- files `robots.txt`, `sitemap.xml`;
- any files that are copied to the System when deploying a profile;
- and other elements that a typical module can generate.

The profile is installed as a normal module.
If you specify `enabled: yes` in the `module.data` profile file, then it
will appear in the list of profiles on the System installation page.
When a profile is enabled, it can either override any decoration settings
or not make any changes, but only add additional features that the user
can activate on their own.

You should place profile files like any third-party modules in
the `modules` directory. Otherwise, all changes may be lost during
the update — when the System is brought to the reference copy
from the repository.

To work with the decoration in the administrative interface of the System,
there is a section _Management → View_, which includes the following
subsections:

- _Colors → Current_: change the color of a specific element;
- _Colors → Presets_: change the color of many elements at the same time;
- _Layouts_: view available page layouts;
- _Global CSS_: adding users CSS directives;
- _Settings_: change the minimum and maximum width of all pages (this
  parameter can be overridden in the settings of each page);

All layouts available in the System (as well as Selections with
the design type "Table (adaptive)") are already able to adapt to the
mobile version.


File organization
---------------------------------------------------------------------

The intuitive location of files in the System allows you to determine
their purpose without resorting to documentation:

- directories like `module_*/frontend` contain everything
  you need for frontend development;
- directories like `module_*/backend` contain everything
  you need for backend development;
- directories like `module_*/data` contain NoSQL data.

The System has a built-in parser and class loader.
The operation of files does not depend on their location and,
if necessary, all files will be found and processed if they are located
in the `modules` and `system` directories.

The placement of the main System modules, third-party modules and library
packages in the file system can be represented by the following scheme:

    ├─ modules
    │  ├─ module_custom_0
    │  ├─ module_custom_1 …
    │  ├─ module_custom_N
    │  │  ├─ backend
    │  │  ├─ data
    │  │  └─ frontend
    │  └─ vendors
    │     └─ packages
    │        ├─ package_0
    │        ├─ package_1 …
    │        └─ package_N
    └─ system
       ├─ module_0
       ├─ module_1 …
       └─ module_N
          ├─ backend
          ├─ data
          └─ frontend

You cannot make changes to the `system` directory, otherwise, when the System
is updated, its contents will be restored to the reference copy and all changes
in it will be lost.

Third party modules should be placed in the `modules` directory.
Library packages should be placed in the `modules\vendors\packages` directory.

Information about modules and packages of libraries is stored in the cache,
so when the state of their files changes, the cache should be reset.
To reset the cache, go to the administrative interface in the section
_Management → Modules → Install_ and press the `↺` button.


Architecture
---------------------------------------------------------------------

The architecture is made according to the classic MVC scheme.
Data is stored in SQL and NoSQL storages.
Structured data uses SQL storage. For unstructured data (for example,
forms, color presets, tests, module settings), NoSQL storage is used.
SQL and NoSQL are described in their respective sections.

The System operates with two types of classes:
1. static factory classes;
2. pattern classes.
In terms of quantitative composition, there are much more
pattern classes than static factories.

The description of instances of pattern classes (future PHP objects)
is stored in NoSQL. At the moment of sampling this description,
an instance (object) is created.

The code of the System follows the DRY (Don't Repeat Yourself) and KISS (Keep It
Short and Simple) principles. Each class consists of a small number of methods,
consisting of a small number of lines. The code is adapted for reuse, and this
approach greatly facilitates the perception of the code.

The development process follows the following rules:
- everything that seems complicated needs to be redone (each method can be
  iteratively rewritten 3 to 10 times);
- for functional testing it is necessary to iterate over all possible
  combinations of arguments/parameters (complete set of combinatorial
  permutations), for check both the correct operation of the System and
  its components, and operation with obviously false values (for look for
  simple bugs and deliberate hacking attempts).

In the _Develop → Structures → Diagram_ section, a diagram of classes
registered in the System is generated. There is also a link to download this
diagram as a JSON file for the StarUML program. This section will become
available after enabling the "Develop" module.

In the _Develop → Console_ section, you can enable the display
of the console for developers. The console shows the order of actions for
generating the page (calling classes, including files, triggering events,
queries to the database and others). This section will become available
after enabling the "Develop" module.

There are basic tests in the _Develop → Tests_ section.
The "Security of Server Settings" test allows you to check server software
for security. The "Security of Roles" test allows you to check the security
of the project's role system. This section will become available after
enabling the "Test" module.


`*.data` format. NoSQL
---------------------------------------------------------------------

One of the strengths of the project is the `*.data` format.
It is similar to the YAML format, but has the following advantages:
- more simple description;
- more strict and unambiguous syntax;
- faster parser;
- allows you to describe future objects of any pattern classes;
- each format line describes only one property and its value,
  for example, in this way: `key: value`;
- changing one line in the `*.data` file changes
  one line in `git diff`.

Example from `demo--data.data` file:

    demo_data
      demo_string: text
      demo_string_empty: 
      demo_integer: 123
      demo_float: 0.000001
      demo_boolean: true
      demo_null: null
      demo_array
      - key_1: value 1
      - key_2: value 2
      - key_3: value 3
      demo_array_empty|_empty_array
      demo_object
        property_name_1: value 1
        property_name_2: value 2
        property_name_3: value 3
      demo_object_empty
      demo_object_text|text
        text: some translated text

Such a file will be converted to a PHP file `/dynamic/cache/data--demo.php`
with the following content:

    namespace effcore {
        cache::$data['demo_data'] = new \stdClass;
        cache::$data['demo_data']->demo_string = 'text';
        cache::$data['demo_data']->demo_string_empty = '';
        cache::$data['demo_data']->demo_integer = 123;
        cache::$data['demo_data']->demo_float = 0.000001;
        cache::$data['demo_data']->demo_boolean = true;
        cache::$data['demo_data']->demo_null = null;
        cache::$data['demo_data']->demo_array['key_1'] = 'value 1';
        cache::$data['demo_data']->demo_array['key_2'] = 'value 2';
        cache::$data['demo_data']->demo_array['key_3'] = 'value 3';
        cache::$data['demo_data']->demo_array_empty = [];
        cache::$data['demo_data']->demo_object = new \stdClass;
        cache::$data['demo_data']->demo_object->property_name_1 = 'value 1';
        cache::$data['demo_data']->demo_object->property_name_2 = 'value 2';
        cache::$data['demo_data']->demo_object->property_name_3 = 'value 3';
        cache::$data['demo_data']->demo_object_empty = new \stdClass;
        cache::$data['demo_data']->demo_object_text = new \effcore\text;
        cache::$data['demo_data']->demo_object_text->text = 'some translated text';
    }

When `cache::select('demo_data')` is called, this file will be loaded into memory
and the data will become available without delay.

**If PHP OPCache is used** then all data will be compiled into op-code
and stored in shared memory, which will not take time to load a PHP file
and parse it on every request.
If **PHP JIT** is used, then parts of the code are already
converted to bytecode and executed even faster.

Using this format, a developer can describe **any structure with any level
of nesting** — he is limited only by his imagination.
For each line in this format, there are only the following
notation variations:

- `entity_name`
- `entity_name|class_name`
- `object_property_name: value`
- `- array_key_name: value`

Each module can have any number of `*.data` files and place them anywhere,
but traditionally all such files will be stored in the `data` directory
of each module.

**A set of such `*.data` files, as well as the mechanism for parsing,
storing and retrieving them, is essentially a NoSQL storage.**
In fact, this is a hybrid of a document-oriented, object-oriented
and hierarchical database model.

All `*.data` files are parsed once, when the cache is cleared. Clearing
the cache is a very rare procedure that is required only after updating
the modules in the System. After parsing, all content is placed in the
`dynamic/cache/data_original.php` file. Also, a separate file is created
for each type of entity. Here is an example of such file organization:

- `dynamic/cache/data--forms.php`
- `dynamic/cache/data--pages.php`
- `dynamic/cache/data--menus.php`

When a change needs to be made to the NoSQL tree, the "Changes" mechanism
is used. It describes what changes should be made to this tree. The process
of making changes can be described by the following procedure: a full load
of the NoSQL tree from the `data_original.php` file is performed, further
changes are made to this tree, then, each entity of the NoSQL tree is saved
file-by-file (example above). If there were no changes for any entity,
the file is not overwritten. The "Changes" mechanism is also rarely
used — mainly when saving System settings through
the administrative interface.

**Note**: An example of making changes is shown in the
`demo--data--changes.data` file of the "Demo" module.

The cache refresh rate does not exceed one second on average.
The update rate via "Changes" is performed in tenths of a second.
Thus, NoSQL works mostly in read mode and gives the best possible
performance when using OPCache + JIT.


SQL
---------------------------------------------------------------------

MySQL or SQLite can be used as SQL storage.
Required versions can be found in the `readme/software.md` file.
The main emphasis in the System is on ANSI SQL and cross-platform.

The following features are supported:

- checks `check` (SQLite, MySQL v.8+);
- transactions `transaction` (`begin`, `rollback`, `commit`);
- collations `collate` (`nocase`, `binary`);
- constraints `constraint` (`primary`, `unique`, `foreign` with cascading action);
- simple and unique indexes (`index`, `unique index`);
- connection to additional storages through the process of manual initialization;
- table prefixes.

Foreign key cascading actions are supported:

- for update: `cascade` (`restrict` and `no action` have not been tested);
- for remove: `cascade` (`restrict` and `no action` have not been tested).

Cross-platform field types are supported:

- `autoincrement`
- `varchar`  (MySQL: `varchar`  | SQLite: `text`)
- `integer`  (MySQL: `int`      | SQLite: `integer`)
- `real`     (MySQL: `double`   | SQLite: `real`)
- `time`     (MySQL: `time`     | SQLite: `text`)
- `date`     (MySQL: `date`     | SQLite: `text`)
- `datetime` (MySQL: `datetime` | SQLite: `text`)
- `boolean`  (MySQL: `tinyint`  | SQLite: `integer`)
- `blob`     (MySQL: `blob`     | SQLite: `blob`)

The set of specified types is sufficient for most tasks. Other types
are allowed but have not been tested. It is recommended to use only these
types to ensure cross-platform.

Storing dates in **any RDBMS** has its own peculiarities. For example,
the `timestamp` type stores the date as a number, so it has a small range
of values — from `1970-01-01 00:00:01` UTC to `2038-01-19 03:14:07` UTC.
Also an additional problem is its conversion to a timezone. Therefore,
this type is not used in the System, but instead it is **recommended to use
the `integer`** type. However, there is an ideal solution for full-fledged
dates — this is **using the types `time`, `date` and `datetime`**.
For example, the `date` and `datetime` types have wide ranges from
`0001-01-01` to `9999-12-31` and are not tied to a time zone.
When adding values to fields of these types, they should be
converted to the UTC±0:00 time zone.

**Note**: SQLite only supports 4 data types: `integer`, `real`, `text` and `blob`.
If another type is used, then its value is cast to these base types.
So `datetime` is cast to `text`, `boolean` to `integer`, and so on.
Such an implementation does not control the integrity of the data domain,
however, this System performs such control on the side of the form fields
and it will be impossible to enter invalid values.

**Note**: MySQL prior to version 8 did not support `check` checks,
however this System implements such control on the side of form fields
and it will be impossible to enter invalid values.

**Note**: During the development process, PostgreSQL was excluded from support
as a RDBMS that is the least compliant with ANSI standards and has features
in working with `autoincrement` counters.


Dynamic files
---------------------------------------------------------------------

There are special types of files in the System — these are **`*.cssd`**,
**`*.jsd`**, **`*.svgd`**, **`*.htmld`**, **`*.txtd`**, **`*.xmld`** and possibly
others if they were described as `kind: dynamic` in `file_types.data`.

These files are **not cached** and are dynamically processed — for each
request of this type of file, it is processed on the PHP side (if a handler
function of this type or a handler function of all similar types was
registered in the System).

Dynamic processing makes it possible to organize the output of System variables
in such files or perform any other operation with their content.

An example of variables that can be used in dynamic files:
- `%%_color__main`
- `%%_color__main(10|10|10|.5)`
- `%%_return_if_token_color_is_dark(color__text|#000|#fff)`
- `%%_return_if_token(color__text|#fff|1|0)`
- `%%_avatar_path`
- `%%_page_width_min_context`
- `%%_page_width_max_context`
- `%%_request_scheme`
- `%%_request_host`
- `%%_return_translation(simple string)`

In fact:
- *`*.cssd`* is a classic CSS file that contains cascading style sheets,
  but which may contain dynamic content;
- *`*.jsd`* is a classic JS file containing JavaScript code,
  but which may contain dynamic content;
- *`*.svgd`* is a classic SVG containing vector graphics in XML format,
  but which may contain dynamic content.

**Note**: See `/system/module_develop/frontend/develop.cssd`
for more examples.


Event model
---------------------------------------------------------------------

The System has a transparent and predictable event model.
It is enough to find or create the `events.data` file in the **custom** module,
specify the event handler function and its weight in it, and then reset
the cache and the event will be processed.

In the administrative interface of the System in the section
_Develop → NoSQL data → Events_ you can view all registered events.
This section will become available after enabling
the "Develop" module.


Caching
---------------------------------------------------------------------

The System architecture is designed in such a way as to work as quickly
as possible and do not require page caching. On projects of any level,
the System will work as fast as other systems with caching enabled.
For heavily loaded projects, you can organize **page caching** using
the NGINX, Apache or IIS web server, and no additional actions from
the System are required.

**Note**: Dynamic files are not cached (`*.cssd`, `*.jsd`, `*.svgd`
and others). When organizing their caching, the developer takes full
responsibility for updating such a cache.


Performance improvement
---------------------------------------------------------------------

To improve performance, you should:

- in PHP v.7+ enable OPCache;
- in PHP v.8+ enable OPCache + JIT;
- switch to Solid-State Drive (SSD);
- transfer directories `dynamic/cache` and `dynamic/tmp` to RAM, at the same time,
  to increase the level of reliability of the web server, such RAM must support
  the error-correcting codes (ECC), and the server itself use an
  Uninterruptible Power Supply (UPS).

The **best** way to improve performance is to cascade styles intelligently.
This approach allows you to do without SAS and LESS preprocessors, the main task
of which is to copy existing styles to many new elements, as a result of which
the volume of each CSS file begins to multiply, and the very idea of cascading
is leveled.

A **good** way to improve performance is to minify JS files by reducing code
by third party programs or services. It is also a good idea to move away from
"heavy" libraries like jQuery and switch to CSS3 animation, SMIL animation,
modern JavaScript and HTML5 features.

The **controversial** way to increase performance is to enable GZIP streaming
compression technology. This can be done using the NGINX, Apache, IIS web server.
However, it should be remembered that compression and decompression of GZIP
traffic leads to an increase in processor load, creates a delay when downloading
and decompressing compressed traffic, and also reduces the battery life
of a mobile device.


Updates
---------------------------------------------------------------------

System or module update provides:

- new functionality;
- bug fixes;
- performance improvement;
- security improvement.

**To update files of the System manually**, you need to go to its official
website [effcore.com](effcore.com) and download the current version of the
distribution as an archive. Next, unpack the downloaded archive locally.
**Important: The unpacked files include the `modules` and `dynamic` directories.
In these directories you need to transfer files from an existing project.**
Next, copy the resulting files to a web server. Third-party modules
are updated separately.

**To update files of the System or Module via Git repository**, go to the
_Management → Modules → Update → Files from repository_ section in the
administrative interface of the System and click the "update" button.
If the "update" button is not available, but the "restore repository" button
is available, then you must first perform the procedure for restoring
the repository. If the "update" and "restore repository" buttons are
not available, then the System or Module does not have its own
repository and it is impossible to update it via Git.

**Updating files of the System via Git** can be performed **from the command line**
by accessing the web server via an SSH connection, going to the `shell` directory
and executing the `./update.sh` command. This update is only possible if there
is a `.git` directory in the web root. Third-party modules are updated separately.

**Updating files of the System and Modules via Composer** can be performed from
the command line by logging into web server via SSH connection, going to web root,
and executing the `composer update` command. This update is only
possible if Composer is installed on the web server. All modules that
were not added via `composer require` or were not registered in `composer.json`
or `composer.lock` **will not be updated**.

**After updating files of the System or Module, the data must be updated.**
Such an update is performed in the section _Management → Modules → Update → Data_.


Licensing
---------------------------------------------------------------------

The System is **open and free**.

The System is not in the public domain.

Any person or organization has the right to take the System,
make changes to it or leave it unchanged, then, on the basis of such a System,
create a website, a web portal, a web service and place it on a server (deployment),
where the System will perform work and program files of the System
should not become publicly available.

An person or an organization has the right to distribute the System
as part of other products only unchanged.

Thus, the license agreement imposes a ban on the distribution of the System
in a modified form. This is done to prevent malicious persons from impersonating
the authors of this work. At the same time, conscientious users are essentially
not limited in their rights.

