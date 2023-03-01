

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
- Website: http://effcore.com
- Repository: http://bitbucket.org/effcore/core

EFFCORE — the next-generation mini-CMS (Content Management System)
and mini-CMF (Content Management Framework) developed by between 2016 and 2022.
Uses technologies such as: HTML5, CSS3, IP v6, SVG, SMIL,
UTF8, UTC, WAI-ARIA, NoSQL, Markdown, UML, PSR-0.

Can be used as a tool for building sites by simple users, and also
as a tool for the development of sites, portals and services by
professional web developers.

The name of the system is an acronym and comes from the phrase "effective core".
The system was made from a scratch. Its main principle — complete absence of
third-party code (for excluding legal claims and any other restrictions associated
with borrowing), and complete absence of third-party ideas (for search innovative
solutions).

The impetus for the creation of the system was a massive degradation in the
development of Open Source projects. If we consider this situation,
then we can see that the developers of such projects are people with the
different skill levels and located in different parts of the planet.
Inconsistency in their actions and also different views on the solutions
in the project are only part of the problem. The second significant
drawback — this is an extensive way of developing the code of such
projects as opposed to the intensive, i.e. instead of creating their
own code, they usually take an another library which was written by
someone unknown and it is not clear by where and it is not clear for
what and they try to combine this library with a group of the same
libraries which are not consistent with each other and have redundant
and not fully tested functionality. As the result we get a set of
incomprehensible and inconsistent libraries with redundant and
poorly tested functionality, which is constantly growing in volume.
Unfortunately, many developers are bogged down in such code and
are trying to deny the obvious.

The main focus of the system is on getting maximum performance.
The evaluation criterion is a simple and understandable condition:
the system installed on a hosting with the cheapest tariff plan
that meets the minimum installation requirements (from ~$3-5 per month)
should generate the main page in 0.01 second (when using OPCache + JIT
and Solid-State Drive), which makes it possible to simultaneously
serve up to ~100 requests per second.


Content management
---------------------------------------------------------------------

A set of layouts is available to the user in the system. Each layout has
a certain number of regions. Blocks with text, menus, forms (only in the
"content" region) and others can be placed in each region. Each page can
have an individual layout. Thus, the markup of any page can be unique.


Localization
---------------------------------------------------------------------

The system already has translations of its interface into Belarusian, Russian, Ukrainian.
In the administrative interface of the system in the section "Management → Locale"
you can set the main language of the system interface and in the section
"Management → Data → Content → Pages" for each page, you can set its own
language.

There are two ways to organize a multilingual website/web portal:

1) within one domain, organize as many copies of pages, menus, text blocks and etc.
   as many languages are required to support;
2) organize its own language copy of the system for each language domain.

Both the first and second versions assume the presence of duplicate pages, menus,
text blocks and other content in different languages. This approach is justified
because almost always, different language versions differ not only in content,
but also in structure. For example, the main menu in one language version may have
some menu items, and in another — completely different, while the names of the
items themselves and their addresses and the number of these items will differ.

In the module called "Profile "Classic" you can see an example of multilingualism
implementation according to method #1.

The system uses the more advanced "Plural" system. With the help of regular
expressions, you can describe almost any dependence of a part of a word on the
numeric and non-numeric arguments present in the phrase.


Appearance
---------------------------------------------------------------------

In the administrative interface of the system, there is a section "Management → View"
which is responsible for the design of the pages.

In the subsection "Colors → Presets" you can select and apply color sets for page elements
and in the subsection "Colors → Current" change the color of a specific element.
The number of colors is limited by the built-in palette (additional colors can be
obtained by installing or creating your own profile).

In the "Layouts" subsection, you can view the page layouts available in the system (additional
layouts can be obtained by installing or creating your own profile).
All layouts available in the system, as well as the decorator with the "view_type = table-adaptive"
parameter, can adapt to changing the screen resolution and thus adapt to the display of data
on the screens of mobile devices.

In the subsection "Global CSS" you can describe your own CSS directives and thus make
changes to the design of pages.

In the "Settings" subsection, you can set the minimum and maximum page width, while
in the settings of the pages themselves, these parameters can be overridden.


Profiles
---------------------------------------------------------------------

The system does not have the usual design themes. To create your own unique look,
there is such a module type as "Profile" ("module_as_profile").
The profile can describe: pages, menus, any kind of blocks (for example, containing text,
audio, video, galleries, selections, polls), colors and their sets, styles as
files "*.css"/"*.cssd", scripts as files "*.js"/"*.jsd", page layouts, element templates,
any kind of files that need to be copied to the system when deploying a profile (images,
audio, video, "robots.txt", "sitemap.xml" and others) and everything that any typical
module can implement.

The directory "profiles/examples" contains examples of profiles that you can copy to the
directory "modules" and perform any actions with them without fear of data loss (it is
recommended to rename all names inside the profile to your own).

Having created a profile, you can deploy the system with your own settings very
easily — it will be enough to enable this profile in the installed system (like any other
module), or, on the system installation page (if the installation is made from scratch),
simply select your profile from the list of available ones.


Modules/Profiles/Libraries
---------------------------------------------------------------------

New modules/profiles/libraries must be placed in the "modules" directory,
otherwise they will be lost during the update — the Git system will clear all directories
to the state of the master copy. For the same reason, you cannot make changes to
modules/profiles that are located in the "system" directory.

When copying modules in the administrative interface of the system in the section
"Management → Modules → Install", you should reset the cache (button "↺") in order for new
modules to appear here — in the list of available modules.

To embed any third-party library based on PHP or JS, you need to place its files in the
wrapper of an empty module and enable this module, after which all library files will
become available.


Updates
---------------------------------------------------------------------

Timely system update provides:

- new functionality;
- bug fixes;
- performance improvement;
- security improvement.

To update the system in manual mode, you need to go to its official website
effcore.com and download the current version of the distribution kit in the
form of an archive.
Next, you should unpack the downloaded archive locally.
Important: among the unpacked files you will find half-empty directories "modules"
and "dynamic" which must be removed before copying to the web server!
They need to be deleted because in some operating systems, when copying directories,
the old directories are completely replaced with new ones, and not merged,
which will lead to the loss of data on the web server.
Next, you should copy the remaining files to the web server so that the new
files from the distributions replace the old files on the web server.
After that, in the administrative interface of the system, visit the section
"Management → Modules → Update → Data" and perform an update for each module
if required.

To update the system through the Git repository, go to the section
"Management → Modules → Update → Files from repository" in the administrative interface
of the system and perform the update in one click with the "update" button.
If the "update" button is not available, but the "restore repository" button is available,
then you must first perform the procedure for restoring the repository.
If the "update" and "restore repository" buttons are unavailable, then the specified
module does not have its own repository and its update via Git is not possible.

The update process via the Git repository can also be performed from the
terminal/console/shell by logging into the web server via SSH connection and
navigating to the "shell" directory and then running the "./update.sh" script.
This update is only possible if there is a ".git" directory in the web root.


Performance improvement
---------------------------------------------------------------------

To improve performance, you should:

- in PHP v.7+ enable OPCache;
- in PHP v.8+ enable OPCache + JIT;
- switch to Solid-State Drive (SSD);
- transfer directories "dynamic/cache" and "dynamic/tmp" to RAM, at the same time,
  to increase the level of reliability of the web server, such RAM must support
  error-correcting code (ECC), and the server itself use an
  Uninterruptible Power Supply (UPS).

The best way to increase performance is to cascade styles wisely.
This approach allows you to do without the SAS and LESS preprocessors,
whose main task is to copy existing styles to many new elements,
as a result of which the size of each CSS file begins to exceed 10-20KiB.

A good way to increase performance is to minify JS files by reducing code
refactoring with third-party programs or services.
Also, a good decision is to abandon "heavy" libraries like jQuery
and switch to CSS3 animation, SMIL animation, modern JavaScript
and HTML5 capabilities.

An immaterial way to increase performance is to enable GZIP streaming
compression technology. This can be done using the web server NGINX, Apache, IIS.
However, it should be remembered that compressing and decompressing GZIP traffic
increases the load on the processor, and as a result, creates a slight delay
in downloading and decompressing compressed traffic, and also reduces the battery
life of the mobile device. Information transfer rates in modern networks make
such optimizations unimportant, and 10-20KiB files are transferred almost
instantly, which allows you to get 100PSI in Google Page Speed ​​rating without
such optimizations.


Caching
---------------------------------------------------------------------

The architecture of the core of the system is designed in such a way that the system
itself works as quickly as possible and does not require caching. Mid-range websites
based on this system will run as fast as other cache-enabled systems.
For heavily loaded web portals, caching may be required and can be enabled using the
web server NGINX, Apache, IIS (no additional actions from the system are required).


Licensing
---------------------------------------------------------------------

The system is open and free.

The system is not in the public domain.

Any person or organization has the right to take the system,
make changes to it or leave it unchanged, then, on the basis of such a system,
create a website, a web portal, a web service and place it on a server (deployment),
where the system will perform work, while program files of the system
should not become publicly available.

An person or an organization has the right to distribute the system
as part of other products only unchanged.

Thus, the licensing agreement prohibits distribution of the system in a modified form,
which makes it illegal for any attempt to pass the system off as a work of its own.


Architecture
---------------------------------------------------------------------

The architecture is made according to the classic MVC scheme.
It is a hybrid system based on NoSQL and SQL storages.
NoSQL storage has a unique implementation and is a hybrid of document-oriented,
object-oriented and hierarchical models.
The core of the system is a set of pattern classes and a NoSQL tree in the form
of PHP code, which containing instances of these classes (entities) in a tree-like form
with any nesting level and unlimited in its structure.

The system code is adapted for reuse.
The system consists of many small classes/class-patterns, which contain on average
from 3 to 15 methods which, in turn, consist of 3-15 lines of code. The perception
of the code is greatly facilitated by the "matrix" layout style (in some places resembles
the syntax of Python). Anything that seemed difficult was rejected or redone. Each
function was iteratively improved from 3 to 10 times. Functional testing was performed
on the entire set of combinatorial permutations.

The system includes a page with a UML diagram of all classes
and a link to download a JSON file with class descriptions in the
StarUML program format.


File organization
---------------------------------------------------------------------

The correct location of files in the system allows you to determine their purpose without
resorting to documentation.

The directories like "module_*/frontend" contain everything you need for frontend development.
The directories like "module_*/backend" contain everything you need for backend development.
The directories like "module_*/data" contain NoSQL data.

In fact, the work of files does not depend on their location and if necessary,
they will still be found and processed. Location of files in specific directories — it is
only an organizational measure designed to facilitate the work with the system.

The system has a built-in parser and class loader PSR-0.


NoSQL
---------------------------------------------------------------------

All NoSQL data is stored in "*.data" files that are located in each module.
Each such file can describe one or many instances of any kind of entity
with any level of nesting into each other (tree structure).
Each line of such a file describes a single entity attribute and can take
the form: "name", "name|class_name", "property: value", "- key: value".
An example of such a structure is shown below.

Example of "*.data" file:

    demo
      object_1|class_name
        property_1: value 1
        property_2: value 2 …
        property_N: value N
      array_1
      - key_1: value 1
      - key_2: value 2 …
      - key_N: value N

After clearing the cache, a special mechanism finds each such file in the "modules" and
"system" directories, parses it, converts to PHP code and distributes to files in
the "dynamic/cache" directory. The whole procedure takes a few seconds and is executed
only if changes are made to the global NoSQL tree (usually when saving system settings
via the administrative interface). An example of such a PHP file is shown below.

    namespace effcore {
      cache::$data['demo'] = new \stdClass;
      cache::$data['demo']->object_1 = new class_name;
      cache::$data['demo']->object_1->property_1 = 'value 1';
      cache::$data['demo']->object_1->property_2 = 'value 2';
      cache::$data['demo']->object_1->property_N = 'value N';
      cache::$data['demo']->array_1['key_1'] = 'value 1';
      cache::$data['demo']->array_1['key_2'] = 'value 2';
      cache::$data['demo']->array_1['key_N'] = 'value N';
    }

Data storage in the "dynamic/cache" directory is performed separately for each
type of entity, as shown in the example below.

An example of separate storage of entities:

- dynamic/cache/data--blocks.php
- dynamic/cache/data--breadcrumbs.php
- dynamic/cache/data--file_types.php

Then, when an attempt is made to call a certain part of the NoSQL tree,
the required PHP file is included and the requested data becomes instantly available.
When using OPCache, all data is already in bytecode and probably in RAM in PHP cache,
which makes access to them as fast as possible.

That file structure is very convenient for development because any change to any
attribute will be shown through "git diff" as a 1 line change at a specific location
in a specific file.

You can change the structure of the NoSQL tree through the special "Changes" mechanism.
This mechanism provides the ability to make changes to the global NoSQL tree on the
basis of which the entire system works. An example of making changes is shown in the
file "demo--data--changes.data" in module "Demo". After applying the mechanism and
clearing the cache, the entire tree will be rebuilt.

NoSQL storage supports the following data types:

- integer;
- float;
- boolean;
- string;
- string|_string_true;
- string|_string_false;
- array;
- array|_empty_array
- object|class_name;
- null.

Note: the system menu is located in the NoSQL storage and no one can
disrupt its work using the administrative interface of the system.
The anonymous user menu is stored in the SQL storage, and its editing
is available through the administrative interface of the system.


SQL
---------------------------------------------------------------------

MySQL and SQLite can be used as SQL storage.
The required versions can be found in the "readme/software.md" file.

The following features are supported:

- checks (SQLite, MySQL v.8+);
- transactions ("begin", "rollback", "commit");
- collations ("nocase", "binary");
- constraints ("primary", "unique", "foreign" with cascading action);
- simple and unique indexes ("index", "unique index");
- connection to additional storages through the manual initialization process;
- table prefixes.

Support for cascading foreign key actions:

- for update: "cascade" (have not been tested: "restrict", "no action");
- to remove: "cascade" (have not been tested: "restrict", "no action").

Cross-platform field types are supported:

- autoincrement;
- varchar  (MySQL: varchar  | SQLite: text);
- integer  (MySQL: int      | SQLite: integer);
- real     (MySQL: double   | SQLite: real);
- time     (MySQL: time     | SQLite: text);
- date     (MySQL: date     | SQLite: text);
- datetime (MySQL: datetime | SQLite: text);
- boolean  (MySQL: tinyint  | SQLite: integer);
- blob     (MySQL: blob     | SQLite: blob).

The specified types are sufficient for most tasks.
Other types are allowed but have not been tested.
We recommend using only these types for cross-platform compatibility.

The main focus is on ANSI SQL and cross-platform.
During the development process, PostgreSQL was excluded as an RDBMS that is
the least compliant with ANSI standards and has peculiarities
in working with "autoincrement" counters.

To store dates and times, it was decided to use the types: "time", "date", "datetime".
The "date" type has a larger range of valid values (from "0001-01-01" to "9999-12-31"),
and also does not have automatic time zone conversion.
When adding dates to the storage, they should be converted to UTC±0:00.
It was decided to abandon the "timestamp" type due to automatic time zone
conversion and use the "integer" type instead.

Distributed queries to remote repositories are not supported.

Note 1: SQLite only supports 4 data types: "integer", "real", "text", "blob". Anything
that can be represented as a number (for example, "boolean") will be automatically converted
to the "integer" type and it is should be with other types by the same analogy.
Such an implementation does not control the integrity of the data domain, however,
this system exercises such control on the side of the form fields and it will be impossible
to enter invalid values.

Note 2: MySQL did not support "check" before version 8, but this system implements such
control on the side of form fields and it will be impossible to enter invalid values.


CSSD, JSD
---------------------------------------------------------------------

A single entry point provides the system with the ability to control
the process of issuing the contents of any file, making it possible
to use variables in "*.cssd" and "*.jsd" files.


Event model
---------------------------------------------------------------------

The system has a transparent and predictable event model.
It is enough to register a new event in "events.data" of your module,
specify its weight and handler in PHP code, reset the cache,
and the event will start being processed.
In the administrative interface of the system in the section
"Develop → NoSQL data → Events" you can view all events
registered in the system (the "Development" section will become available
after enabling the "Development" module).

