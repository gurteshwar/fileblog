Possible causes for Adobe Phonegap Build not pulling code from github
=====================================================================

While using [Adobe Phonegap Build](https://build.phonegap.com) with [github](https://github.com/), we came across an issue where `config.xml` properties were not reflecting in the app and the builds were resulting in empty apps with incomplete resources.

Following two solutions helped us resolve the same:

1. Make sure all your code is lying inside a directory called `www`, which should be placed at the root of your repository.

2. Make sure there is **only one** `index.php` in the entire application, which should be immediately inside `www`. If there's an index.php in any sub-directory, Adobe Phonegap Build tool will assume the parent directory of that inner `index.html` to be the application root and hence result in a faulty build.
