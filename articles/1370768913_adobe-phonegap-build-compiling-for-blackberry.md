Adobe Phonegap Build - Compiling for Blackberry
===============================================

Two things to keep in mind if you're compiling for Blackberry using [Adobe Phonegap Build](https://build.phonegap.com):

1. **File names**:
All files must only have alphanumeric characters. That means no dashes (`-`)
or underscores ( `_` ) or periods (`.`) in the basename of the file.

2. **File sizes**:
Any asset or resource (image / icon) **should not exceed** 800KB in size.

Yes, this is all very irritating, but that's how it is.
