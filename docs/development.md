

When you edit "*.data" files
=====================================================================

Files "*.data" have next features:
- does not support empty lines;
- does not support indent with tab characters;
- only Unix line endings (LF) are support;
- only UTF-8 text encoding are support.

Be sure that your editor/IDE has a right settings for editing.
File ".editorconfig" is describe right settings for any editor/IDE
but only professional editors/IDE can work with this feature.

The next editors/IDE need some improvements:
- BBEdit            : Adapt to ".editorconfig" if it's in the parent directory.
- TextWrangler      : Adapt to ".editorconfig".
- Atom              : Strips trailing whitespace and adds a trailing newline when it's saved.
                      Install package "EditorConfig" with this command: "apm install editorconfig".
                      Or go to "Settings → Packages → Core Packages" and disable the package "whitespace".
- PHPStorm          : Strips trailing whitespace when it's saved.
                      Install package "EditorConfig".
                      Or go to "Preferences → Editor → General" and disable "Strip trailing spaces on Save".
- Visual Studio Code: Tab Size will be automatically detected when a file is opened based on the file contents.
                      Go to "Preferences → Text Editor → Detect Indentation" and disable it.
- Espresso          : Set "Tab width|size" to "2" and enable "Soft tabs" (spaces instead tab).
- Textastic         : Set "Tab width|size" to "2" and enable "Soft tabs" (spaces instead tab).
- Coda2             : Install plugin "EditorConfig" or set "Tab width|size" to "2" and enable "Soft tabs" (spaces instead tab).
- Eclipse           : Install plugin "EditorConfig" or set "Tab width|size" to "2" and enable "Soft tabs" (spaces instead tab).
- Notepad++         : Install plugin "EditorConfig" or set "Tab width|size" to "2" and enable "Soft tabs" (spaces instead tab).
- Sublime           : Install plugin "EditorConfig" or set "Tab width|size" to "2" and enable "Soft tabs" (spaces instead tab) (p.s. "translate_tabs_to_spaces" : true).
- TextMate          : Install plugin "EditorConfig".


NoSQL Storage characteristics
=====================================================================

- tree structure and no restrictions - each item can have own unique structure;
- data parts definitions in format "property: value" on each own line (it's a convenient
  solution for preview differences in data in tools like "git" and "diff");
- each sub storage will initialize only if required.


How to activate Neor Profile SQL
=====================================================================

- In file "/dynamic/data/changes.php" change:
    data::$data['changes']['core']->insert['storages/storage/sql']->credentials->host = '[::1]';
    data::$data['changes']['core']->insert['storages/storage/sql']->credentials->port = '3306';
  to:
    data::$data['changes']['core']->insert['storages/storage/sql']->credentials->host = '127.0.0.1';
    data::$data['changes']['core']->insert['storages/storage/sql']->credentials->port = '4040';
- Run "/shell/cache_clear.sh".


How to set cross-domain cookie
=====================================================================

- For base domain and each subdomain install a new System instance with its own table prefix.
- For base domain and each subdomain in file "/dynamic/data/changes.php" insert:
    data::$data['changes']['core']->update['settings/core/cookie_domain'] = BASE_DOMAIN_NAME;
- For each subdomain in file "/dynamic/data/changes.php" update:
    data::$data['changes']['core']->update['settings/core/keys']['session'] = KEY_FROM_BASE_DOMAIN;
    data::$data['changes']['core']->update['settings/core/keys']['salt']    = KEY_FROM_BASE_DOMAIN;
- For base domain and each subdomain run "/shell/cache_clear.sh".
- For base domain and each subdomain clear cookie in the browser.


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

    | hex ip                           |                      to IPv6 on win-x86 |                      to IPv6 on osx-x64 |
    |----------------------------------|-----------------------------------------|-----------------------------------------|
    | 00000000000000000000000000000000 |                                      :: |                                      :: |
    | 0000000000000000000000000000000f |                                     ::f |                              ::0.0.0.15 |
    | 000000000000000000000000000000ff |                                    ::ff |                             ::0.0.0.255 |
    | 00000000000000000000000000000fff |                                   ::fff |                            ::0.0.15.255 |
    | 0000000000000000000000000000ffff |                                  ::ffff |                           ::0.0.255.255 |
    | 000000000000000000000000000fffff |                          ::0.15.255.255 |                          ::0.15.255.255 |
    | 00000000000000000000000000ffffff |                         ::0.255.255.255 |                         ::0.255.255.255 |
    | 0000000000000000000000000fffffff |                        ::15.255.255.255 |                        ::15.255.255.255 |
    | 000000000000000000000000ffffffff |                       ::255.255.255.255 |                       ::255.255.255.255 |
    | 00000000000000000000000fffffffff |                           ::f:ffff:ffff |                           ::f:ffff:ffff |
    | 0000000000000000000000ffffffffff |                          ::ff:ffff:ffff |                          ::ff:ffff:ffff |
    | 000000000000000000000fffffffffff |                         ::fff:ffff:ffff |                         ::fff:ffff:ffff |
    | 00000000000000000000ffffffffffff |                  ::ffff:255.255.255.255 |                  ::ffff:255.255.255.255 |
    | 0000000000000000000fffffffffffff |                      ::f:ffff:ffff:ffff |                      ::f:ffff:ffff:ffff |
    | 000000000000000000ffffffffffffff |                     ::ff:ffff:ffff:ffff |                     ::ff:ffff:ffff:ffff |
    | 00000000000000000fffffffffffffff |                    ::fff:ffff:ffff:ffff |                    ::fff:ffff:ffff:ffff |
    | 0000000000000000ffffffffffffffff |                   ::ffff:ffff:ffff:ffff |                   ::ffff:ffff:ffff:ffff |
    | 000000000000000fffffffffffffffff |                 ::f:ffff:ffff:ffff:ffff |                 ::f:ffff:ffff:ffff:ffff |
    | 00000000000000ffffffffffffffffff |                ::ff:ffff:ffff:ffff:ffff |                ::ff:ffff:ffff:ffff:ffff |
    | 0000000000000fffffffffffffffffff |               ::fff:ffff:ffff:ffff:ffff |               ::fff:ffff:ffff:ffff:ffff |
    | 000000000000ffffffffffffffffffff |              ::ffff:ffff:ffff:ffff:ffff |              ::ffff:ffff:ffff:ffff:ffff |
    | 00000000000fffffffffffffffffffff |            ::f:ffff:ffff:ffff:ffff:ffff |            ::f:ffff:ffff:ffff:ffff:ffff |
    | 0000000000ffffffffffffffffffffff |           ::ff:ffff:ffff:ffff:ffff:ffff |           ::ff:ffff:ffff:ffff:ffff:ffff |
    | 000000000fffffffffffffffffffffff |          ::fff:ffff:ffff:ffff:ffff:ffff |          ::fff:ffff:ffff:ffff:ffff:ffff |
    | 00000000ffffffffffffffffffffffff |         ::ffff:ffff:ffff:ffff:ffff:ffff |         ::ffff:ffff:ffff:ffff:ffff:ffff |
    | 0000000fffffffffffffffffffffffff |       0:f:ffff:ffff:ffff:ffff:ffff:ffff |       ::f:ffff:ffff:ffff:ffff:ffff:ffff |
    | 000000ffffffffffffffffffffffffff |      0:ff:ffff:ffff:ffff:ffff:ffff:ffff |      ::ff:ffff:ffff:ffff:ffff:ffff:ffff |
    | 00000fffffffffffffffffffffffffff |     0:fff:ffff:ffff:ffff:ffff:ffff:ffff |     ::fff:ffff:ffff:ffff:ffff:ffff:ffff |
    | 0000ffffffffffffffffffffffffffff |    0:ffff:ffff:ffff:ffff:ffff:ffff:ffff |    ::ffff:ffff:ffff:ffff:ffff:ffff:ffff |
    | 000fffffffffffffffffffffffffffff |    f:ffff:ffff:ffff:ffff:ffff:ffff:ffff |    f:ffff:ffff:ffff:ffff:ffff:ffff:ffff |
    | 00ffffffffffffffffffffffffffffff |   ff:ffff:ffff:ffff:ffff:ffff:ffff:ffff |   ff:ffff:ffff:ffff:ffff:ffff:ffff:ffff |
    | 0fffffffffffffffffffffffffffffff |  fff:ffff:ffff:ffff:ffff:ffff:ffff:ffff |  fff:ffff:ffff:ffff:ffff:ffff:ffff:ffff |
    | ffffffffffffffffffffffffffffffff | ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff | ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff |


Incompatibility with third-party applications
=====================================================================

- Kaspersky Internet Security browser plugin can insert to page
  some CSS with strings like "appearance: checkbox" and etc.
  This string can break your own styles of form elements like
  checkboxes, radios and other.
  We can not do anything against violating web standards from third side.
  Just ignore Kaspersky Internet Security.


Core scheme
=====================================================================


    ┌────────────────── classes ──────────────────┐             ┌────────────── noSQL data ──────────────┐
    │                                             │             │                                        │
    │  ╔═══════════════════════════════════════╗  │             │   ╔════════════════════════════════╗   │
    │  ║ /module_N/backend/pattern-class_1.php ║  │             │   ║ /module_N/data/instance_1.data ║   │
    │  ╠═══════════════════════════════════════╣  │             │   ╠════════════════════════════════╣   │
    │  ║ /module_N/backend/pattern-class_2.php ║  │             │   ║ /module_N/data/instance_2.data ║   │
    │  ╚═══════════════════════════════════════╝  │             │   ╚════════════════════════════════╝   │
    │                     ...                     │             │                  ...                   │
    │  ╔═══════════════════════════════════════╗  │    ┌───┐    │   ╔════════════════════════════════╗   │
    │  ║ /module_N/backend/pattern-class_N.php ║──────▶│ + │◀───────║ /module_N/data/instance_N.data ║   │
    │  ╚═══════════════════════════════════════╝  │    └───┘    │   ╚════════════════════════════════╝   │
    │                                             │      │      │                                        │
    └─────────────────────────────────────────────┘      │      └────────────────────────────────────────┘
                                                         │
                                                         │
    ╔═════════════ big tree (memory) ═════════════╗      │
    ║                                             ║      │
    ║   data[class_instance_1] = new instance {   ║      │
    ║     property_1: value_1                     ║      │
    ║     property_2: value_2                     ║      │
    ║     property_N: value_N }                   ║      │
    ║                                             ║      │
    ║   data[class_instance_2] = new instance {   ║      │
    ║     property_1: value_1                     ║      │
    ║     property_2: value_2                     ║◀─────┘
    ║     property_N: value_N }                   ║
    ║   ...                                       ║
    ║   data[class_instance_N] = new instance {   ║
    ║     property_1: value_1                     ║
    ║     property_2: value_2                     ║
    ║     property_N: value_N }                   ║
    ║                                             ║
    ╚═════════════════════════════════════════════╝
                           │
                           ▼
    ╔════════════ /dynamic/cache/*.php ═══════════╗
    ║                                             ║
    ║   cache[instance_1] = new instance          ║
    ║   cache[instance_1]->property_1 = value_1   ║
    ║   cache[instance_1]->property_2 = value_2   ║
    ║   cache[instance_1]->property_N = value_N   ║
    ║                                             ║
    ║   cache[instance_2] = new instance          ║
    ║   cache[instance_2]->property_1 = value_1   ║
    ║   cache[instance_2]->property_2 = value_2   ║
    ║   cache[instance_2]->property_N = value_N   ║
    ║   ...                                       ║
    ║   cache[instance_N] = new instance          ║
    ║   cache[instance_N]->property_1 = value_1   ║
    ║   cache[instance_N]->property_2 = value_2   ║
    ║   cache[instance_N]->property_N = value_N   ║
    ║                                             ║
    ╚═════════════════════════════════════════════╝
    
    
    ─────────────────────────────────────────────────────────────────

