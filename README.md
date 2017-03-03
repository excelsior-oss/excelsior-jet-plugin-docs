# How to build the README files for Excelsior JET build tool plugins

## Requirements (Windows)

Versions known to work are given in parenthesis.

  * PHP (5.3.3)
  * sed (4.2.1 from [GnuWin32](http://gnuwin32.sourceforge.net/packages/sed.htm))
  * pandoc (1.17.2)

## Building

`build.bat` builds the Markdown files from README.md.php and filters them through `sed`
to remove trailing spaces (see top comment in README.md.php as to why those spaces 
are there).

`test.bat` builds and then converts the Markdown files to HTML using pandoc 
with GitHub-flavored Markdown, so that you could preview the README files
in your browser.

`install.bat` copies the Markdown files to the respective plugin projects, 
which must be co-located with this one in your local file hierarchy.
                                                                    
`clean.bat` removes all intermediary files.
