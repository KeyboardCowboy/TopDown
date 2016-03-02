<?php
/**
 * @file
 * Topdown: Organize your markdown files into a table of contents.
 */

class TopDown {
  // Constants to define the list format.
  const UNORDERED = 0;
  const ORDERED = 1;

  // The directory to scan for files.
  private $dir;

  // The raw files in the given directory.
  private $files = [];

  // The hierarchical content.
  private $content = [];

  // The H1 of the resulting file.
  public $title = 'Table of Contents';

  // The list format.
  public $format = self::UNORDERED;

  // Whether to trim off the file extensions when creating links.  GitHub Wikis
  // don't use file extensions.
  public $fileExt = TRUE;

  // The hierarchical separator.
  public $separator = '--';

  // Files to ignore.
  public $ignore = [];

  /**
   * TopDown constructor.
   *
   * @param $directory
   *   The location to look for markdown files.
   *
   * @throws Exception
   */
  public function __construct($directory = '.') {
    if (is_dir($directory)) {
      $this->dir = $directory;

      $this->_loadFiles();
      $this->_processHierarchy();
    }
    else {
      throw new Exception("Invalid directory.");
    }
  }

  /**
   * Clean up file names to create titles.
   *
   * @param $string
   *
   * @return string
   */
  public static function convertToTitle($string) {
    // If the filename is prefixed with a number for sorting, trim it off.
    $string = preg_replace('/^(\d+\.(\ +)?)|(\d+\-)/', '', $string);

    // Run basic substitutions.
    $string = strtr($string, ['.md' => '', '-' => ' ']);

    return $string;
  }

  /**
   * Create the file.
   *
   * @param $filename
   */
  public function create($filename) {
    // Set the header.
    $content = ["# {$this->title}"];

    // Add the contents of the markdown files.
    foreach ($this->content as $path => $item) {
      $this->_addItem($content, $path, $item);
    }

    // Add a footnote.
    // @todo: Move this into an includable MD file.
    $content[] = '';
    $content[] = '---';
    $content[] = '';
    $content[] = "_Sidebar generated with [TopDown](https://github.com/KeyboardCowboy/TopDown).  Manual changes may be overridden._";

    // Write the file.
    file_put_contents($filename, implode(PHP_EOL, $content));
  }

  /**
   * Load files from the directory.
   */
  private function _loadFiles() {
    $this->files = glob("*.md");

    // Ignore certain files.
    $this->files = array_diff($this->files, $this->ignore);
  }

  /**
   * Create a hierarchy of files.
   */
  private function _processHierarchy() {
    foreach ($this->files as $file) {
      // Remove file extensions.  We will add them in as we create the links to
      // preserve hierarchy.
      $file = str_replace('.md', '', $file);

      // Split into hierarchy.
      $parts = explode($this->separator, $file);
      $this->_buildHierarchy($this->content, $parts);
    }
  }

  /**
   * Recursive helper to build file hierarchy.
   *
   * @param $parent
   * @param $children
   * @param $path
   */
  private function _buildHierarchy(&$parent, $children, $path = []) {
    $child = array_shift($children);

    // Build the path for this child.
    $path[] = $child;
    $child_path = implode($this->separator, $path);

    if (!isset($parent[$child_path])) {
      $parent[$child_path]['#title'] = self::convertToTitle($child);
    }

    if (!empty($children)) {
      $this->_buildHierarchy($parent[$child_path], $children, $path);
    }
  }

  /**
   * Recursive helper to build the markdown table from content.
   *
   * @param $content
   * @param $path
   * @param $item
   * @param int $depth
   */
  private function _addItem(&$content, $path, $item, $depth = 0) {
    $title = $item['#title'];
    unset($item['#title']);

    // Construct the line item.
    $filename = "{$path}.md";
    $path = $this->fileExt ? $filename : $path;
    $bullet = str_repeat(' ', 2 * $depth) . ($this->format == self::ORDERED ? "1. " : "- ");
    $line = file_exists("{$this->dir}/{$filename}") ? "[{$title}]({$path})" : $title;

    $content[] = "{$bullet}{$line}";

    // Process children.
    if (!empty($item)) {
      $depth++;
      foreach ($item as $child_path => $child) {
        $this->_addItem($content, $child_path, $child, $depth);
      }
    }
  }
}

/**
 * Subclass for GitHub Wikis.
 */
class GitHubWikiSidebar extends TopDown {
  public $fileExt = FALSE;
  public $ignore = ['_Sidebar.md', '_Footer.md', 'Home.md'];

  public function create() {
    parent::create('_Sidebar.md');
  }
}
