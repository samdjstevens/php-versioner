# Versioner

A project versioning helper tool built in PHP.

Versioner provides an easy to use command line interface to help track and tag versions of your project by writing to a 
JSON file in the root of your project directory, and (to come), creating and pushing git commits to the project 
repository. This version file is easily readable by both humans and machines, and can be used as part of an 
automated deployment process.

## Installation

Versioner is intended to be installed globally via Composer so that it can be used on multiple projects, and 
projects which are not written in PHP. Install Versioner by running the below in your CLI:

```
composer global require spanky/versioner
```

That's it. You are now ready to use Versioner.

## Using Versioner

### Setting the project version
To start using Versioner and set an initial version on your project, navigate to the project root and type 
`versioner set 0.1.0` (or whatever SemVer compliant version you wish to set) into your CLI. A `version` 
file will be created in your project root, which you should commit to your version control system.

### Bumping the project version.
To bump the version of your project according to SemVer rules, simply type `versioner bump` into your CLI. 
This will bump the patch version by default. To bump the minor or major version, add the `--minor` or `--major` flags 
respectively. 

### Getting the project version
If you want to know the current version of your project without looking at the file, type `versioner current` 
into your CLI.
