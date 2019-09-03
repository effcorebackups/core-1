

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


…


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

Distributed queries to remote storages not supported.


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

