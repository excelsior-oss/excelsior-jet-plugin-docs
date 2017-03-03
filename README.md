# Building README Files for Excelsior JET Build Tool Plugins

## Motivation

The Excelsior JET 
[Maven](https://github.com/excelsior-oss/excelsior-jet-maven-plugin) and
[Gradle](https://github.com/excelsior-oss/excelsior-jet-gradle-plugin) plugins
are nearly identical in terms of feature sets and usage, because they
share a common [API](https://github.com/excelsior-oss/excelsior-jet-api).
Maintaining their documentation separately was clearly a waste of time.
So we've merged their README files into one, using PHP as a preprocessor,
and moved it out into this project.

## Requirements (Windows)

Versions known to work are given in parenthesis.

  * PHP (5.3.3)
  * sed (4.2.1 from [GnuWin32](http://gnuwin32.sourceforge.net/packages/sed.htm))
  * pandoc (1.17.2)

## Building (Windows)

`build.bat` builds the Markdown files from README.md.php and filters them through `sed`
to remove trailing spaces (see top comment in README.md.php as to why those spaces 
are there).

`test.bat` builds and then converts the Markdown files to HTML using pandoc 
with GitHub-flavored Markdown, so that you could preview the README files
in your browser.

`install.bat` builds, tests, and copies the Markdown files to the respective 
plugin projects, which must be co-located with this one in your local file system
hierarchy.
                                                                    
`clean.bat` removes all intermediate files.
