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

## File Names
TopDown relies on a simple naming convention to generate the hierarchy.  Use underscores to indicate nesting.  For example:

```
contributing.md
contributing__getting-started.md
contributing__configuration.md
contributing__configuration__advanced.md
contributing__giving-back.md
another-file.md
```

Would generate a table of contents like this:

- Contributing
	- Getting Started
	- Configuration
		- Advanced
	- Giving Back
- Another File

TopDown reads the files out of the given directory top down, which is generally alphabetically, so to rearrange items simply prefix them with a number.

## With GitHub
GitHub wikis are their own repos.  Check yours out to your local machine instead of editing the files in the UI.  When you are done creating or editing your files, run TopDown and your sidebar is ready to go!
