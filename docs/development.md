

When you edit `*.data` files
---------------------------------------------------------------------

Files `*.data` have next features:
- does not support indent with tab characters;
- only UTF-8 text encoding are support;
- only Unix line endings (LF) are recommended;
- empty lines does not recommended.

Be sure that your editor/IDE has a right settings for editing.
File `.editorconfig` is describe right settings for any editor/IDE
but only professional editors/IDE can work with this feature.

The next editors/IDE need some improvements:
- **Atom**:
  Removes trailing spaces and adds a trailing newline when saving files.
  Install the "EditorConfig" package with this command: `apm install editorconfig`.
  Or go to _Settings → Packages → Core Packages_ and disable the "whitespace" package.
- **BBEdit**:
  Adapts to `.editorconfig` if it is in the parent directory.
- **Coda2**:
  Install plugin "EditorConfig" or set "Tab width|size" to "4" and
  enable "Soft tabs" (spaces instead Tab).
- **Eclipse**:
  Install plugin "EditorConfig" or set "Tab width|size" to "4" and
  enable "Soft tabs" (spaces instead Tab).
- **Espresso**:
  Set "Tab width|size" to "4" and enable "Soft tabs" (spaces instead Tab).
- **Notepad++**:
  Install plugin "EditorConfig" or set "Tab width|size" to "4" and
  enable "Soft tabs" (spaces instead Tab).
- **Nova**:
  Adapts to `.editorconfig`.
- **PHPStorm**:
  Removes trailing spaces when saving the file.
  Install the "EditorConfig" package.
  Or go to _Preferences → Editor → General_ and disable "Strip trailing spaces on Save".
- **Textastic**:
  Set "Tab width|size" to "4" and enable "Soft tabs" (spaces instead Tab).
- **TextWrangler**:
  Adapts to `.editorconfig`.
- **TextMate**:
  Install plugin "EditorConfig".
- **Sublime**:
  Install plugin "EditorConfig" or set "Tab width|size" to "4" and
  enable "Soft tabs" (spaces instead Tab) (p.s. `translate_tabs_to_spaces` : true).
- **Visual Studio Code**:
  The size of the Tab will be automatically determined based on the content
  of the file. Go to _Preferences → Text Editor → Detect Indentation_ and disable it.


How to activate Neor Profile SQL
---------------------------------------------------------------------

- In file `/dynamic/data/changes.php` change:

      data::$data['changes']['core']->insert['storages/storage/sql']->credentials->port = '3306';

  to:

      data::$data['changes']['core']->insert['storages/storage/sql']->credentials->port = '4040';

- Run `/shell/cache_clear.sh`.


About forms
---------------------------------------------------------------------

1. It is not recommended to use DISABLED|READONLY text fields with shared
   NAME (`name="shared_name[]"`) because user can delete DISABLED|READONLY
   state from field and change the field VALUE and submit the form — after
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

   example (result of form state after validate):

       - input[type=text,name=shared_name[],value=1,disabled|readonly]
       - input[type=text,name=shared_name[],value=fake_value]
       - input[type=text,name=shared_name[],value=2]

2. If you used greater than 1 element with attribute MULTIPLE and shared
   NAME (`name="shared_name[]"`), after submit you will get merged
   arrays of values.

   example (result of form state before validate):

       - select[name=shared_name[],multiple]
         - option[value=1,selected]
         - option[value=2]
         - option[value=3]
       - select[name=shared_name[],multiple]
         - option[value=1]
         - option[value=2,selected]
         - option[value=3]

   example (result of form state after validate):

       - select[name=shared_name[],multiple]
         - option[value=1,selected]
         - option[value=2,selected]
         - option[value=3]
       - select[name=shared_name[],multiple]
         - option[value=1,selected]
         - option[value=2,selected]
         - option[value=3]

