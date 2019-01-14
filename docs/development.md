

Storage layer characteristics
=====================================================================

- supported MySQL and SQLite databases;
- supported prepared queries anywhere (no chance for SQL-injections);
- supported only UTF-8 anywhere;
- supported transactions: begin, roll_back, commit;
- supported collations: nocase, binary;
- supported constraints: primary, unique, foreign;
- supported indexes: unique index, index;
- supported table prefixes (global);
- supported connections to remote storages via manual initialization process;
- distributed queries to remote storages not supported.

Supported field types:
- autoincrement;
- integer|real;
- varchar;
- time|date|datetime (always UTC);
- boolean (0|1);
- blob;
Other types allowed but not tested.
We recommend to use only tested types for cross-platform compatibility reasons.
List of the tested types is sufficient for most tasks.


How to activate Neor Profile SQL
=====================================================================

In "/dynamic/data/changes.php" change:
    data::$data['changes']['core']->insert['storages/storage/sql']->credentials->host = '[::1]';
    data::$data['changes']['core']->insert['storages/storage/sql']->credentials->port = '3306';
to:
    data::$data['changes']['core']->insert['storages/storage/sql']->credentials->host = '127.0.0.1';
    data::$data['changes']['core']->insert['storages/storage/sql']->credentials->port = '4040';
run "/shell/cache_clear.sh"


About forms
=====================================================================

1. Not recommend to use DISABLED|READONLY text fields with shared
   NAME (name="shared_name[]") because user can delete DISABLED|READONLY
   state from field and change the field VALUE and submit the form - after
   this action the new VALUE will be set to the next field with
   shared NAME.
   example (default form state):
   - input[type=text,name=shared_name[],value=1,disabled|readonly]
   - input[type=text,name=shared_name[],value=2]
   - input[type=text,name=shared_name[],value=3]
   example (user made a fake changes):
   - input[type=text,name=shared_name[],value=fake_value]
   - input[type=text,name=shared_name[],value=2]
   - input[type=text,name=shared_name[],value=3]
   example (result form state after validate):
   - input[type=text,name=shared_name[],value=1,disabled|readonly]
   - input[type=text,name=shared_name[],value=fake_value]
   - input[type=text,name=shared_name[],value=2]
2. If you used more than 1 element with attribute MULTIPLE and shared
   NAME (name="shared_name[]"), after submit you will get merged
   arrays of values.
   example (result form state before validate):
   - select[name=shared_name[],multiple]
     - option[value=1,selected]
     - option[value=2]
     - option[value=3]
   - select[name=shared_name[],multiple]
     - option[value=1]
     - option[value=2,selected]
     - option[value=3]
   example (result form state after validate):
   - select[name=shared_name[],multiple]
     - option[value=1,selected]
     - option[value=2,selected]
     - option[value=3]
   - select[name=shared_name[],multiple]
     - option[value=1,selected]
     - option[value=2,selected]
     - option[value=3]


Platforms differences in IP conversion
=====================================================================

This information is actual for function core::hex_to_ip().

    ┌──────────────────────────────────┬─────────────────────────────────────────┬─────────────────────────────────────────┐
    │ hex ip                           │                      to IPv6 on win-x86 │                      to IPv6 on osx-x64 │
    ├──────────────────────────────────┼─────────────────────────────────────────┼─────────────────────────────────────────┤
    │ 00000000000000000000000000000000 │                                      :: │                                      :: │
    │ 0000000000000000000000000000000f │                                     ::f │                              ::0.0.0.15 │
    │ 000000000000000000000000000000ff │                                    ::ff │                             ::0.0.0.255 │
    │ 00000000000000000000000000000fff │                                   ::fff │                            ::0.0.15.255 │
    │ 0000000000000000000000000000ffff │                                  ::ffff │                           ::0.0.255.255 │
    │ 000000000000000000000000000fffff │                          ::0.15.255.255 │                          ::0.15.255.255 │
    │ 00000000000000000000000000ffffff │                         ::0.255.255.255 │                         ::0.255.255.255 │
    │ 0000000000000000000000000fffffff │                        ::15.255.255.255 │                        ::15.255.255.255 │
    │ 000000000000000000000000ffffffff │                       ::255.255.255.255 │                       ::255.255.255.255 │
    │ 00000000000000000000000fffffffff │                           ::f:ffff:ffff │                           ::f:ffff:ffff │
    │ 0000000000000000000000ffffffffff │                          ::ff:ffff:ffff │                          ::ff:ffff:ffff │
    │ 000000000000000000000fffffffffff │                         ::fff:ffff:ffff │                         ::fff:ffff:ffff │
    │ 00000000000000000000ffffffffffff │                  ::ffff:255.255.255.255 │                  ::ffff:255.255.255.255 │
    │ 0000000000000000000fffffffffffff │                      ::f:ffff:ffff:ffff │                      ::f:ffff:ffff:ffff │
    │ 000000000000000000ffffffffffffff │                     ::ff:ffff:ffff:ffff │                     ::ff:ffff:ffff:ffff │
    │ 00000000000000000fffffffffffffff │                    ::fff:ffff:ffff:ffff │                    ::fff:ffff:ffff:ffff │
    │ 0000000000000000ffffffffffffffff │                   ::ffff:ffff:ffff:ffff │                   ::ffff:ffff:ffff:ffff │
    │ 000000000000000fffffffffffffffff │                 ::f:ffff:ffff:ffff:ffff │                 ::f:ffff:ffff:ffff:ffff │
    │ 00000000000000ffffffffffffffffff │                ::ff:ffff:ffff:ffff:ffff │                ::ff:ffff:ffff:ffff:ffff │
    │ 0000000000000fffffffffffffffffff │               ::fff:ffff:ffff:ffff:ffff │               ::fff:ffff:ffff:ffff:ffff │
    │ 000000000000ffffffffffffffffffff │              ::ffff:ffff:ffff:ffff:ffff │              ::ffff:ffff:ffff:ffff:ffff │
    │ 00000000000fffffffffffffffffffff │            ::f:ffff:ffff:ffff:ffff:ffff │            ::f:ffff:ffff:ffff:ffff:ffff │
    │ 0000000000ffffffffffffffffffffff │           ::ff:ffff:ffff:ffff:ffff:ffff │           ::ff:ffff:ffff:ffff:ffff:ffff │
    │ 000000000fffffffffffffffffffffff │          ::fff:ffff:ffff:ffff:ffff:ffff │          ::fff:ffff:ffff:ffff:ffff:ffff │
    │ 00000000ffffffffffffffffffffffff │         ::ffff:ffff:ffff:ffff:ffff:ffff │         ::ffff:ffff:ffff:ffff:ffff:ffff │
    │ 0000000fffffffffffffffffffffffff |       0:f:ffff:ffff:ffff:ffff:ffff:ffff │       ::f:ffff:ffff:ffff:ffff:ffff:ffff │
    │ 000000ffffffffffffffffffffffffff |      0:ff:ffff:ffff:ffff:ffff:ffff:ffff │      ::ff:ffff:ffff:ffff:ffff:ffff:ffff │
    │ 00000fffffffffffffffffffffffffff |     0:fff:ffff:ffff:ffff:ffff:ffff:ffff │     ::fff:ffff:ffff:ffff:ffff:ffff:ffff │
    │ 0000ffffffffffffffffffffffffffff |    0:ffff:ffff:ffff:ffff:ffff:ffff:ffff │    ::ffff:ffff:ffff:ffff:ffff:ffff:ffff │
    │ 000fffffffffffffffffffffffffffff |    f:ffff:ffff:ffff:ffff:ffff:ffff:ffff │    f:ffff:ffff:ffff:ffff:ffff:ffff:ffff │
    │ 00ffffffffffffffffffffffffffffff |   ff:ffff:ffff:ffff:ffff:ffff:ffff:ffff │   ff:ffff:ffff:ffff:ffff:ffff:ffff:ffff │
    │ 0fffffffffffffffffffffffffffffff |  fff:ffff:ffff:ffff:ffff:ffff:ffff:ffff │  fff:ffff:ffff:ffff:ffff:ffff:ffff:ffff │
    │ ffffffffffffffffffffffffffffffff | ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff │ ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff │
    └──────────────────────────────────┴─────────────────────────────────────────┴─────────────────────────────────────────┘

