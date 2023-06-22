# Configurable report webservice #

A local plugin to supply a web service endpoint to retrieve configurable reports as CSV files using web service tokens.

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/local/configurablereportwebservice

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## Use ##

* Generate a web service token for a user in the normal way.
* Grant the web service user the 'local/configurablereportwebservice:fetchreports' capability.
* Ensure the user has access to the desired report and that CSV export has been enabled for it.
* The report will now be available on the URL:
```
https://MOODLE_DOMAIN_NAME/local/configurablereportwebservice/getcsv.php?id=REPORTID&t=WEBSERVICETOKEN
```
## License ##

2023 Andrew Hancox <andrewdchancox@googlemail.com>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
