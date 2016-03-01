<?php
/**
 * @file
 * Topdown: Organize your markdown files into a table of contents.
 */

class TopDown {
  // The directory to scan for files.
  private $dir;

  // The raw files in the given directory.
  private $files = [];

  // The hierarchical content.
  private $content = [];

  /**
   * TopDown constructor.
   *
   * @param $directory
   *   The location to look for markdown files.
   *
   * @throws Exception
   */
  public function __construct($directory = '') {
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
   * Load files from the directory.
   */
  private function _loadFiles() {
    $this->files = glob("*.md");

    // Ignore certain files.
    $this->files = array_diff($this->files, ['_Sidebar.md', '_Footer.md']);
  }

  /**
   * Create a hierarchy of files.
   */
  private function _processHierarchy() {
    foreach ($this->files as $file) {
      // Convert title characters.
      $path = str_replace('.md', '', $file);

      // Split into hierarchy.
      $parts = explode('__', $path);
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
    $child_path = implode('__', $path);

    if (!isset($parent[$child_path])) {
      $parent[$child_path]['#title'] = self::convertToTitle($child);
    }

    if (!empty($children)) {
      $this->_buildHierarchy($parent[$child_path], $children, $path);
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
    // Run basic substitutions.
    return strtr($string, ['.md' => '', '-' => ' ']);
  }

  /**
   * Create the file.
   *
   * @param $filename
   */
  public function create($filename) {
    // Set the header.
    $content = ['# Table of Contents'];

    // Add the contents of the markdown files.
    foreach ($this->content as $path => $item) {
      $this->_addItem($content, $path, $item);
    }

    // Add a footnote.
    $content[] = '';
    $content[] = '---';
    $content[] = '';
    $content[] = "_Sidebar auto-generated with [TopDown](TopDown)_";

    // Write the file.
    file_put_contents($filename, implode(PHP_EOL, $content));
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

    $content[] = str_repeat(' ', 2 * $depth) . "- [{$title}]({$path})";

    if (!empty($item)) {
      $depth++;
      foreach ($item as $child_path => $child) {
        $this->_addItem($content, $child_path, $child, $depth);
      }
    }
  }
}
