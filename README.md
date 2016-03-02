# TopDown
**Generate a Table of Contents for your markdown files.**

This was inspired by the GitHub wiki.  Its usefulness is trumped by the lack of organization and the ease with which you can break a link by changing a file name.  With TopDown you can generate a simple Table of Contents, for example the `_Sidebar.md` file, just by naming your files well.

## How it Works
1. Install this library.
2. Create a PHP file and include TopDown.php.
3. Tell TopDown where the files live and the name of the TOC file to create.
```
$sidebar = new TopDown(‘/path/to/files’);
$sidebar->create('_Sidebar.md');
```

This will create the file `/path/to/files/_Sidebar.md`

## With GitHub
GitHub wikis are their own repos.  Check yours out to your local machine instead of editing the files in the UI.  When you are done creating or editing your files, run TopDown and your sidebar is ready to go!

There is a subclass to make generating GitHub Wiki sidebars super simple.

1. Check out your wiki repo and `cd` into it.
2. Run `composer require keyboardcowboy/topdown`
3. Add a php file, for example `buildSidebar.php` to the repo then copy and paste this snippet:
		<?php
		require_once 'vendor/keyboardcowboy/topdown/TopDown.php';
		
		$sidebar = new GitHubWikiSidebar();
		$sidebar->create();
		
Now, simply run `php buildSidebar.php`!  That’s it!

## File Names
TopDown relies on a simple naming convention to generate the hierarchy.  By default a double-hyphen is used as the hierarchical separator, but you can tell TopDown to use any string as the separator.  For example:

```
contributing.md
contributing--configuration.md
contributing--configuration--advanced.md
contributing--configuration--beginner.md
contributing--getting-started.md
contributing--giving-back.md
more.md
```

Would generate a table of contents like this:

- Contributing
	- Configuration
		- Advanced
		- Beginner
	- Getting Started
	- Giving Back
- More

TopDown reads the files out of the given directory top down, which is generally alphabetically, so to rearrange items simply prefix them with a number.

```
contributing.md
contributing--1-getting-started.md
contributing--2-configuration.md
contributing--2-configuration--1-beginner.md
contributing--2-configuration--2-advanced.md
contributing--3-giving-back.md
more.md
```

File names that begin with a number then a period or hyphen will have that part trimmed off to generate the link name.

- Contributing
	- Getting Started
	- Configuration
		- Beginner
		- Advanced
	- Giving Back
- More

## Options

`TopDown::title` *string*
: Set a custom title for the file.  Defaults to ‘Table of Contents’

`TopDown::separator` *string*
: Set a custom separator to determine hierarchy.  Defaults to `--`.

`TopDown::format` *int*
: Set the list format.  Defaults to `TopDown::UNORDERED`.

`TopDown::fileExt` *bool*
: Whether or not to build the links with the file extension. GitHub does not use the `.md` file extensions on its wiki page urls.

`TopDown::ignore` *array*
: An array of filenames to ignore.
