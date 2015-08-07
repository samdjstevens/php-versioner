<?php
namespace Spanky\Versioner;
use InvalidArgumentException;

/**
 * Class SemVerVersion
 *
 * @package Spanky\Versioner
 */
class SemVerVersion
{
    /**
     * The major number of the version.
     *
     * @var int
     */
    protected $major;

    /**
     * The minor number of the version.
     *
     * @var int
     */
    protected $minor;

    /**
     * The patch number of the version.
     *
     * @var int
     */
    protected $patch;

    /**
     * Create a new SemVer version number.
     *
     * @param  int $major
     * @param  int $minor
     * @param  int $patch
     * @throws \InvalidArgumentException
     */
    public function __construct($major = 0, $minor = 1, $patch = 0)
    {
        $this->major = $this->validateNumber($major);
        $this->minor = $this->validateNumber($minor);
        $this->patch = $this->validateNumber($patch);
    }

    /**
     * Create a SemVerVersion object from a version string.
     *
     * @param  string $versionString
     * @return \Spanky\Versioner\SemVerVersion
     * @throws \InvalidArgumentException
     */
    public static function fromString($versionString) {

        // Match for a valid SemVer string
        preg_match('/v?([0-9]+).([0-9]+).([0-9]+)([-_.].+)?/', $versionString, $matches);

        // Invalid SemVer string
        if (count($matches) < 4) {
            throw new InvalidArgumentException(sprintf(
                '%s does not appear to be a valid SemVer version',
                $versionString
            ));
        }

        // Return a new instance with the matched numbers
        return new static($matches[1], $matches[2], $matches[3]);
    }

    /**
     * Get the major number of the version.
     *
     * @return int
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * Set the major version of the number.
     *
     * @param  int $major
     * @throws \InvalidArgumentException
     */
    public function setMajor($major)
    {
        $this->major = $this->validateNumber($major);
    }

    /**
     * Get the minor number of the version.
     *
     * @return int
     */
    public function getMinor()
    {
        return $this->minor;
    }

    /**
     * Set the minor version of the number.
     *
     * @param  int $minor
     * @throws \InvalidArgumentException
     */
    public function setMinor($minor)
    {
        $this->minor = $this->validateNumber($minor);
    }

    /**
     * Get the patch number of the version.
     *
     * @return int
     */
    public function getPatch()
    {
        return $this->patch;
    }

    /**
     * Set the patch version of the number.
     *
     * @param  int $patch
     * @throws \InvalidArgumentException
     */
    public function setPatch($patch)
    {
        $this->patch = $this->validateNumber($patch);
    }

    /**
     * Create and return a new instance representing the next
     * patch version.
     *
     * @return static
     */
    public function getNextPatchVersion()
    {
        return new static($this->major, $this->minor, $this->patch + 1);
    }

    /**
     * Create and return a new instance representing the next
     * minor version.
     *
     * @return static
     */
    public function getNextMinorVersion()
    {
        return new static($this->major, $this->minor + 1, 0);
    }

    /**
     * Create and return a new instance representing the next
     * major version.
     *
     * @return static
     */
    public function getNextMajorVersion()
    {
        return new static($this->major + 1, 0, 0);
    }

    /**
     * Return the full string representation of the version.
     *
     * @return string
     */
    public function __toString()
    {
        return join('.', [$this->major, $this->minor, $this->patch]);
    }

    /**
     * Validate an input is a valid number for a SemVer part.
     *
     * @param  mixed $number
     * @return int
     * @throws \InvalidArgumentException
     */
    protected function validateNumber($number)
    {
        // Check if the number is numeric if a string is passed in
        if (is_string($number) && ! is_numeric($number)) {
            throw new InvalidArgumentException(sprintf(
                '%s is not a valid number',
                $number
            ));
        }

        // Cast to an integer
        $number = intval($number);

        // Check the number is non-negative
        if ($number < 0) {
            throw new InvalidArgumentException('Negative numbers are not permitted.');
        }

        return intval($number);
    }
}
