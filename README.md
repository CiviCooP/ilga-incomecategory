# ILGA Income Category

Categorizes an organisation in a category according to the active address. The application is very ILGA specific.
Organisations are classified according to the Worldbank income table. Use it for inspiration not for production

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## What it does
- select all the active addresses of organizations.
- lookup the country of the address in the Worldbank income table.
- add the organisation in a income group according to this table.

## Requirements

* PHP v7.2+
* CiviCRM 5.20

On these version this extension is developed. It is however quite possible that it works on lower numbers.

## Installation
The installation creates a number of objects:

The following groups (that can be used for selections and mailings)

| name        | title           | Usage  |
| ------------- |-------------| -----|
|`high_income`    |High Income Invoice | All the organisations that are in the worldbank High Income category |
|`low_income`     |Low Income Invoice  | All the organisations that are in another worldbank category|
|`unknown_income` |Unknown Income Invoice  | Organizations that should be categorized, but are not because for example their country is not in the world bank tables|

Two tables are created:
* `ilga_income_worldbank`: contains the world bank income categories, with country code. Unfortunately, the country is the iso 3 letter abbreviation instead of the 2 letter one.
* `ilage_iso_translation` : table with the iso3 and iso2 country codes together.

An extra job `ILGA Income Category` is added that does the actual categorizing.

### Disabling and uninstalling
The job is removed when the extension is disabled. The extra tables are dropped after uninstalling. The groups are not removed.

## Usage

Run the job `ILGA Income Category`. The job settings console can be found at
`https://<server>/civicrm/admin/job`

## Known Issues (or accepted behaviour)

- If an organisation has no address its not categorized. However, if the address of a organisation is removed its not removed from the group. The rationale is that, although the address is gone now, this is the best way to categorize the organisation.
- The world bank table is not complete. So if a country cannot be found it is added to the `Unknown Invoice Group`


