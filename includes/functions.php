<?php

namespace Lsr\Doc;

/**
 * Add a trailing slash to a string (file/directory path)
 *
 * @param string $string
 *
 * @return string
 */
function trailingSlashIt(string $string) : string {
	if (substr($string, -1) !== DIRECTORY_SEPARATOR) {
		$string .= DIRECTORY_SEPARATOR;
	}
	return $string;
}

/**
 * Checks if a given class implements the given interface
 *
 * @param string|object $class     An object (class instance) or a string (class or interface name)
 * @param string        $interface Interface name
 *
 * @return bool
 */
function class_implements(string|object $class, string $interface) : bool {
	return in_array($interface, \class_implements($class), true);
}