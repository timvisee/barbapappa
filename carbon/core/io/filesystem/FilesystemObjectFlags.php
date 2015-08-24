<?php

namespace carbon\core\io\filesystem;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

interface FilesystemObjectFlags {

    /**
     * Unknown file type flag. This flag is used for filesystem objects that aren't recognized as a file, directory or
     * symbolic link, maybe because it might not exist.
     */
    const FLAG_TYPE_OBJECT = 1;
    /** File type flag. This flag is used for filesystem objects that are files. */
    const FLAG_TYPE_FILE = 2;
    /** Directory type flag. This flag is used for filesystem objects that are directories. */
    const FLAG_TYPE_DIRECTORY = 4;
    /** Symbolic link type flag. This flag is used for filesystem objects that are symbolic links. */
    const FLAG_TYPE_SYMBOLIC_LINK = 8;
    /**
     * All object types flag. This flag is used for:
     * - FLAG_TYPE_OBJECT
     * - FLAG_TYPE_FILE
     * - FLAG_TYPE_DIRECTORY
     * - FLAG_TYPE_SYMBOLIC_LINK
     */
    const FLAG_TYPE_ALL = 15; // 1 + 2 + 4 + 8

    // TODO: Improve quality of this interface!
}
