

About the project
=====================================================================

Type            : content management system
Author          : Maxim Rysevets
Developer       : Maxim Rysevets
Initial release : 2020-01-01
Written in      : PHP
Operating system: macOS, UNIX, Linux, Microsoft Windows
License         : proprietary software
Website         : http://effcore.com

effcore is a next-generation content management system (CMS)
developed by between 2017 and 2020.
It incorporates all the new Web 2.0/3.0 technologies including
HTML5, CSS3, JS ES6, IP v6, SVG, SMIL, UTF8, UTC.

Can be used as a tool for building sites by simple users, and also
as a tool for the development of sites, portals and services by
professional web developers.

The name of the system is an acronym and comes from the phrase "effective core".
The system was made from scratch. Its main principle is the complete absence of
third-party code (to exclude legal claims and also any other restrictions
which related with borrowing), and third-party ideas (to search for new
innovative solutions).

The impetus for the creation of the system was a massive degradation in the
development of Open Source projects. If you consider this situation,
you can see that the developers of such projects are people with different
skill levels and located in different parts of the planet.
Incoordination in their actions and different views on solutions
in the project is only part of the problem.
Second significant flaw - this is an extensive way to develop code in such
projects as opposed to intensive, i.e. instead of creating own code,
they usually take another library which it's written not clear by whom
and it's not clear by where and try to combine it with a group of the
same libraries, not coordinated among themselves and having, usually,
redundant and not fully tested functionality.
As a result, we get a set of obscure and inconsistent libraries with poorly
tested redundant functionality which is constantly growing in volume.
Unfortunately, many developers are bogged down in such code and
are trying to deny the obvious.

Content management in the system is focused on the inline insertion
of any available block (menu, text, forms, breadcrumbs and others)
directly to the layout of the edited page. When creating a new page,
the user is given a choice of layout and each new page can have its
own unique design and layout of blocks.

The main emphasis in the system is on to get maximum performance.
As evaluation criteria, a simple and understandable condition was applied:
a system which installed on a hosting with the cheapest tariff plan,
but compliant with the minimum installation requirements (from ~3$ per month),
should generate a homepage in 0.002 seconds (when OPcache is enabled),
or 0.02 seconds (when OPcache is disabled), which in the first case
allows serving up to ~500 clients per second, and in the second
to ~50 (excluding parallel loading of connected files).


Architecture
---------------------------------------------------------------------

The architecture is made according to the classical MVC scheme.
It's a hybrid system of NoSQL and SQL storages and a set of
classes/class-patterns.

The system code is adapted for reuse.
The system consists of many small classes/class-patterns,
containing on average from 3 to 15 methods,
consisting on average of 3-7 lines of code.

Thanks to the "matrix" style of code layout, its perception is
greatly facilitated (reminds Python syntax in some places), and proper
location of files in the system allows you to determine their purpose
without resorting to any documentation (in each module everything
necessary for frontend development is stored in the "module_*/frontend"
directory, and for backend development - in the "module_*/backend" directory).
Also, everything that seems complicated was rejected or remade.
Each function iteratively improved from 3 to 10 times.
Functional testing was performed on the whole set of
combinatorial permutations.

Has a built-in parser and class loader PSR-0, thanks to which, to add
a new library (a set of classes), it's enough to place the files
containing them on a web server and reset the cache, after which they
become available from anywhere in the system.
The system includes a page with a UML diagram of all classes and a link
to download a JSON file with a description of the classes in StarUML
program format.


Security
---------------------------------------------------------------------

Also an important factor in the system is security.
As solutions to increase the level of security were used:
- the ability to work without JS;
- key-signed user sessions;
- key-signed form validation identifiers;
- the use of prepared SQL queries;
- filtering of user input in form fields;
- filtering of URL argument;
- single entry point of any HTTP request,
  as a result - no negative effects when the web server
  is configured incorrectly (".htaccess", "web.config");
- the ability to create a new file type with full access control;
- the ability to get a page assembly hash in the system console;
- CAPTCHA base module.

Determinism in the system work - another important factor.
With the same input parameters, the same result should be reproduced
regardless of platform and as result - complete rejection of
functions which work depends on the environment (for example "setlocale"
and others).


Core: NoSQL
---------------------------------------------------------------------

All data is stored as PHP code.
Perhaps the fastest storage after "storage in RAM".
After organizing the disk in RAM, you can increase performance by 3-5 times.
Each storage subdirectory will be initialized only if required.

Any instance of the class and other NoSQL data can be described
in text format in a file of type "*.data", like YAML, but has a more
stringent rules such as "each string can contain the only one
phrase in the form "key: value".

It's comfortable for controlling changes in the code - any change
of one key or value will be highlighted in "git diff" with just one line.
Also, this format significantly speeds up parsing the files.

Below is given an example of "*.data" file.

    demo
      object_1|class_name
        property_1: value 1
        property_2: value 2 …
        property_N: value N
      array_1
      - item_1: value 1
      - item_2: value 2 …
      - item_N: value N

At the same time, both objects (instances of class-patterns) and arrays
can have any nesting levels and contain inside any other objects or
arrays.

After parsing "*.data" files, the result is converted to PHP code (single
tree of objects - instances of class-patterns), after which it's
saved to files "dynamic/cache/cache-*.php" separately for each kind of
entity, as shown in the example below:
- dynamic/cache/data--blocks.php
- dynamic/cache/data--breadcrumbs.php
- dynamic/cache/data--file_types.php
and so on.

The example described above will be converted to a PHP
file of the following form:

    namespace effcore {
      cache::$data['demo'] = new \stdClass;
      cache::$data['demo']->object_1 = new class_name;
      cache::$data['demo']->object_1->property_1 = 'value 1';
      cache::$data['demo']->object_1->property_2 = 'value 2';
      cache::$data['demo']->object_1->property_N = 'value N';
      cache::$data['demo']->array_1['item_1'] = 'value 1';
      cache::$data['demo']->array_1['item_2'] = 'value 2';
      cache::$data['demo']->array_1['item_N'] = 'value N';
    }

This architecture allows you to access NoSQL data as quickly as possible.
When using the PHP module OPcache, the access speed can increase
from 2 to 3 times. In fact, to access NoSQL data, it's enough to
load a PHP file of a certain entity and data will be available
immediately after loading.

Thus, the core of the system is the aforementioned set of class-patterns
and NoSQL storage, which cache is represented as PHP code, containing
instances of these classes in tree form with any level of nesting
and unlimited by structure.

Changing the structure of NoSQL data is possible only from the side of PHP code.
For example, the main menu is located in NoSQL storage and nobody cannot
disrupt its work. The anonymous user menu is stored in SQL storage and
the administrator can edit this menu through the system interface.

Field types are supported:
- integer;
- float;
- boolean;
- string;
- array;
- object|class_name;
- null.


Core: SQL
---------------------------------------------------------------------

MySQL and SQLite can be used as SQL storages.
The required versions can be found in the "readme/software.mark" file.
Storage connection and data retrieval will initialize only if required.
Denying access to SQL storage will not raise an error, but will only
make inaccessible part of the possibilities (for example, sessions
and login will be disconnected, and on the pages with election
"0 results" will be displayed).

The following are supported:
- checks;
- prepared queries (no chance for SQL-injections);
- transactions (begin, roll_back, commit);
- collations (nocase, binary);
- constraints (primary, unique, foreign with cascade action);
- index and unique index;
- connections to remote storages via manual initialization process;
- table prefixes.

Support for cascading foreign key actions:
- on update: "cascade" (not tested feature: "restrict", "no action");
- on delete: "cascade" (not tested feature: "restrict", "no action").

Cross-platform field types are supported:
- autoincrement;
- varchar;
- integer;
- real;
- time;
- date;
- datetime;
- boolean (as integer: 0|1);
- blob.

Other types allowed but not tested.
List of the tested types is sufficient for most tasks.
Only tested types are recommended for cross-platform compatibility reasons.
Distributed queries to remote storages not supported.

The main focus is on ANSI SQL.
PostgreSQL was excluded during development as Web RDBMS with
least compatible of ANSI standards.

It was decided not to use field of type timestamp.
Instead, it's recommended to use field of type datetime.
This field has a wide range of acceptable values (from "0000-01-01"
to "9999-12-31"), and also does not depend on the time zone.
When adding data to the server, dates should be converted to time zone UTC±0:00.
Instead of the original timestamp type, it's recommended
to use the integer type.


CSS, JS, SASS, LESS
---------------------------------------------------------------------

A single entry point allows the system to control the process of issuing
the contents of any file, making it possible to use external variables
in CSS and JS files. Such variables begin with "%%_", and the files
containing them have extensions "cssd" and "jsd".

The project does not implement and considers the CSS and JS streaming
compression technology obsolete due to the following reasons:
- 1 to 10 KiB text files are compressed, that with modern data
  transfer rate is not significant, at the same time, compression
  takes web server resources, and for unpacking - the client (which
  ultimately affects the battery charge of mobile clients).
- for CSS, a file size above 10 KiB is considered a consequence
  of improper cascading of styles, together with the use of disastrous
  technology such as SASS or LESS, which condoning "badcoding".
- JS files can be initially compressed and obfuscated by
  reducing-reorganization of JS code by third-party
  programs or services.
However, no one bothers to use the streaming compression capabilities
of the web server directly.


Event model
---------------------------------------------------------------------

The event model is built fairly transparent and predictable.
It's enough to register a new event in the "events.data" of your module,
specify its weight and handler in PHP code, flush the cache and
the event will begin to be processed. On the "NoSQL Data → Events" page,
you can view all events registered in the system.


Web server
---------------------------------------------------------------------

As a web server, Apache, NGINX, IIS are supported.
The required versions can be found in the "readme/software.mark" file.


Caching
---------------------------------------------------------------------

Due to its architecture, mid-level projects do not require caching.
For large projects, caching is usually done by third-party web server tools,
what is originally supposed by the author.


Licensing
---------------------------------------------------------------------

The system is open and free.
The system is not in the public domain.
Anyone can create a website, portal or service on the basis of it,
both personally and for any customer.
However, you cannot distribute system files in their original or
modified form or in conjunction with anything else.
This restriction does not apply to third-party modules
whose authors themselves determine the licensing policy.

