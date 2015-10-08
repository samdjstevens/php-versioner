<?php
namespace Spanky\Versioner;

use RuntimeException;
use Spanky\Versioner\Exceptions\InvalidVersionFileException;

/**
 * Class Versioner
 *
 * @package Spanky\Versioner
 */
class Versioner
{
    /**
     * The full path of the version file.
     *
     * @var string
     */
    protected $versionFilePath;

    /**
     * Constructor.
     *
     * @param string $versionFilePath
     * @throws \RuntimeException
     */
    public function __construct($versionFilePath)
    {
        $this->setVersionFilePath($versionFilePath);
    }

    /**
     * Set the full file path of the version file to use.
     *
     * @param string $versionFilePath
     * @throws \RuntimeException
     */
    public function setVersionFilePath($versionFilePath)
    {
        // Get the real path of the one passed in
        $realPath = realpath($versionFilePath);

        // Check to see if the file exists, creating it if it doesn't
        if ($realPath === false) {
            file_put_contents($versionFilePath, '');
        }

        // Check to see if the file is readable
        if (! is_readable($realPath)) {
            throw new RuntimeException("Version file at {$versionFilePath} does not appear to be readable.");
        }

        // Check to see if the file is writable
        if (! is_writable($realPath)) {
            throw new RuntimeException("Version file at {$versionFilePath} does not appear to be writable.");
        }

        $this->versionFilePath = $realPath;
    }

    /**
     * Returns true if the version file is empty, and false
     * otherwise.
     *
     * @return bool
     */
    public function versionFileIsEmpty()
    {
        return file_get_contents($this->versionFilePath) === '';
    }

    /**
     * Returns the current version found in the version file
     * as a SemVerVersion object.
     *
     * @return \Spanky\Versioner\SemVerVersion
     * @throws \InvalidVersionFileException
     */
    public function getCurrentVersion()
    {
        // Read the file contents in and attempt to decode the JSON
        $versionJson = json_decode(file_get_contents($this->versionFilePath));

        // If the JSON decode failed, or the version property was not found,
        // then throw a RuntimeException, as the file is not formatted correctly
        if ($versionJson === false || ! isset($versionJson->version)) {
            throw new InvalidVersionFileException("The version file appears to corrupt.");
        }

        // Try to return a SemVerVersion from the version string found in the JSON.
        // If the version is an invalid, throw a RuntimeException.
        try {
            return SemVerVersion::fromString($versionJson->version);
        } catch (InvalidArgumentException $e) {
            throw new InvalidVersionFileException("The version file appears to contain an invalid version number.");
        }
    }

    /**
     * Write a version number to the version file.
     *
     * @param string $versionString
     * @return \Spanky\Versioner\SemVerVersion
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function setCurrentVersionFromString($versionString)
    {
        // Try to create a new SemVerVersion object from the string version passed in
        $version = SemVerVersion::fromString($versionString);

        // Write the version to the file
        if (! file_put_contents($this->versionFilePath, json_encode(['version' => (string) $version]))) {
            throw new RuntimeException("Failed to write to the version file.");
        }

        // Return the created SemVerVersion object
        return $version;
    }
}
