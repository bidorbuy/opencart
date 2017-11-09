# OpenCart bidorbuy Store Integrator

### Changelog

#### 2.0.12
* Enhancements and bugs fixes.

_[Updated on November 07, 2017]_

#### 2.0.11
* Corrected header processing in Store Integrators core.

_[Updated on September 30, 2017]_

#### 2.0.10
* EOL (End-of-life due to the end of life of this version) for PHP 5.3 support.
* Improved the logging strategy.
* Added extra save button which was removed from debug section (settings page).

_[Updated on August 21, 2017]_

#### 2.0.9
* Fixed error in query (1292): Incorrect datetime value: '0000-00-00 00:00:00' for column 'row_modified_on' at row 1.
* Fixed error in query (1055): Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column.
* Fixed issue when "$this->dbLink->execute" hides the real error messages.
* Fixed issue when bobsi tables are created always with random charset instead of utf8_unicode_ci.
* Fixed issue when export process is interrupted by zlib extension.

_[Updated on June 06, 2017]_

#### 2.0.8
* Added an appropriate warning on the Store Integrator setting page about EOL(End-of-life) of export non HTTP URL to the tradefeed file.

_[Updated on March 07, 2017]_

#### 2.0.7
* Added support for 2.3.0.2 OpenCart version.
* Added a flag to display BAA fields (to display BAA fields on the setting page add '&baa=1' to URL in address bar).

_[Updated on March 03, 2017]_

#### 2.0.6
* Improved the upgrade process.
* Fixed an issue when variation uses its parent's Available Qty.

_[Updated on December 29, 2016]_

#### 2.0.5
* Added support of multiple images.
* Added support of images from product description.
* Added the possibility to open PHP info from store Integrator settings page.

 _[Updated on December 09, 2016]_

#### 2.0.4
* Added additional improvements for Store Integrator Settings page.
* Added new feature: if product has weight attribute, the product name should contain this attribute value.
* Fixed an issue when tradefeed is invalid to being parsed with Invalid byte 1 of 1-byte UTF-8 sequence.
* Fixed an issue when Store Integrator cuts the long name of categories in Export Criteria section.

 _[Updated on November 18, 2016]_

#### 2.0.3
* Fixed issue when Store Integrator cuts the category name.
* Fixed an issue of empty XML after changing the settings.
* Fixed an issue when it is impossible to download log after its removal.
* Fixed an issue when extra character & added to the export URL.
* Corrected the export link length: it was too long.
* Added an error message if "mysqli" extension is not loaded.

_[Updated on October 26, 2016]_

#### 2.0.2
* Removed extra notices from the export page.
* Fixed the appearance of description tag in case the description is not assigned.
* Fixed 'Undefined index: parent_id in' error during the export.
* Added warning in case if 'readfile' function is disabled.
* The PHP version has changed to 5.3.0.

_[Updated on August 04, 2016]_

#### 2.0.1
* Added the ability to display the plugin version.

_[Updated on December 10, 2015]_

#### 2.0.0
* Added support for 2.1.x and 2.2.x OpenCart versions.
* Enhancements and bugs fixes.

_[Updated on October 29, 2015]_

#### 1.0
* First release.

_[Released on April 29, 2014]_