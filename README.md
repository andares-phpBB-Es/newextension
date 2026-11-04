# Quick Title Edition

Allows administrators, moderators and/or topic authors to apply attributes to topic titles.

## Installation

1. Download the extension
2. Copy the whole archive content to /ext/kaileymsnay/qte
3. Go to your phpBB board > Administration Control Panel > Customise > Manage extensions > Quick Title Edition: enable

## Update instructions

For **ernadoo/qte 2.0.0-RC1**, follow the
[Spanish migration guide](docs/migracion-ernadoo.md) instead of the same-extension
update procedure below. Disable the old extension without deleting its data;
both extensions share the same database tables.

1. Go to your phpBB board > Administration Control Panel > Customise > Manage extensions > Quick Title Edition: disable
2. Delete all files of the extension from /ext/kaileymsnay/qte
3. Upload all the new files to the same locations
4. Go to your phpBB board > Administration Control Panel > Customise > Manage extensions > Quick Title Edition: enable
5. Purge the board cache

## Recent Topics NG integration

This version adds optional support for IMC-GER Recent Topics NG 1.2.1. Keep
Recent Topics NG installed without modifications. QTE supplies its attributes
through the Recent Topics NG events and renders them before the topic titles
in the top/bottom, side and separate views (including the simple view).
Topics without attributes remain unchanged. No database migration is needed.
After uploading the changed QTE files, purge the phpBB board cache.

The supplied templates target prosilver and styles inheriting its QTE templates.
Custom style overrides may need the equivalent template changes.

Run the standalone listener regression checks with:

```sh
php tests/recenttopicsng.php
```

On a test board, check text and image attributes, colours, `%mod%` and `%date%`,
unmarked topics, all Recent Topics NG locations, and first/last/unread post
title settings. Also check normal forum/search lists and QTE with Recent Topics
NG disabled. The standalone checks do not replace these phpBB integration checks.

## Automated testing

We use automated unit tests to prevent regressions. Check out our build below:

master: [![Build Status](https://github.com/kaileymsnay/qte/workflows/Tests/badge.svg)](https://github.com/kaileymsnay/qte/actions)

## License

[GNU General Public License v2](license.txt)
