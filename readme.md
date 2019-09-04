

                       ○○○○   ○○○○
                     ○○○○○  ○○○○○○
                    ○○○    ○○○
        ○○○○○○○     ○○○○○○○○○○○○○○  ○○○○○○○      ○○○○○○○     ○○ ○○○   ○○○○○○○
      ○○○○   ○○○○   ○○○○○○○○○○○○  ○○○     ○○○  ○○○     ○○○   ○○○    ○○○     ○○○
     ○○○       ○○○  ○○○    ○○○   ○○           ○○         ○○  ○○    ○○         ○○
    ○○○○○○○○○○○○○○  ○○○    ○○○  ○○           ○○           ○○ ○○   ○○○○○○○○○○○○○○
     ○○○            ○○○    ○○○   ○○           ○○         ○○  ○○    ○○
      ○○○○   ○○○○   ○○○    ○○○    ○○○     ○○○  ○○○     ○○○   ○○     ○○○     ○○○
        ○○○○○○○     ○○○    ○○○      ○○○○○○○      ○○○○○○○     ○○       ○○○○○○○
                  ○○○○   ○○○○
                ○○○○   ○○○○                Copyright © 2017—2020 Maxim Rysevets.


About the project
=====================================================================


…


Security
---------------------------------------------------------------------


…


Core: NoSQL
---------------------------------------------------------------------

Any instance of the class and other NoSQL data can be described
in text format in a file of type * .data, like YAML, but has a more
stringent rules such as "each string can contain the only one
phrase in the form "key: value".

It's comfortable for controlling changes in the code - any change
of one key or value will be highlighted in "git diff" with just one line.
Also, this format significantly speeds up parsing the files.

Below is given an example of *.data file.

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

After parsing * .data files, the result is converted to PHP code (single
tree of objects - instances of class-patterns), after which it's
saved to files dynamic/cache/cache-*.php separately for each kind of
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
For example, the main menu is located in NoSQL storage and the user cannot
disrupt its work. The anonymous user menu is stored in SQL storage and
the user can edit this menu through the system interface.


Core: SQL
---------------------------------------------------------------------

MySQL and SQLite can be used as SQL storages.
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


…


Event model
---------------------------------------------------------------------

…


Web server
---------------------------------------------------------------------


…


Caching
---------------------------------------------------------------------


…


Licensing
---------------------------------------------------------------------

The system is open and free.
The system is not in the public domain.
Anyone can create a website, portal or service on the basis of it,
both personally and for any customer.
However, you cannot distribute system files in their original or
modified form or in conjunction with anything else.

