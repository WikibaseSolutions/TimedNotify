# PegaNotify

Provides a time-based notification system for Pega using Echo.

## Configuration

The following configuration options are available:

* `$wgPegaNotifyRunRate` (default: `0.05`) - determines the run rate of the notifier
* `$wgPegaNotifyRunDeferred` (default: `true`) - whether to run the
  notifications in a deferred update
* `$wgPegaNotifyPushedNotificationRetentionDays` (default: `60`) - the number
  of days to retain pushed notifications

### `$wgPegaNotifyRunRate`

This configuration parameter determines how often to run the notifications.
Calculating which notifications to send out and to whom is expensive. The
notifications are therefore only calculated approximately once every
`1/$wgPegaNotifyRunRate` requests. Thus, if you set your run rate to `0.01`,
the *probability* of running the notifications is 1 in 100 for every request.

It should be a number between `0` and `1`. The default value is `0.05` (`1/20`).

### `$wgPegaNotifyRunDeferred`

This configuration parameter determines whether to run the notifications in a
deferred update. If this parameter is set to `true`, notifications are
calculated after a response has already been sent to the browser. This
calculation will therefore not impact the load time of the wiki. Unless you
have a specific reason to set this to `false`, you generally should not have to
change this.

It should be a boolean. The default value is `true`.

### `$wgPegaNotifyPushedNotificationRetentionDays`

This configuration parameter specifies the number of days to remember pushed
events. In order to prevent duplicate notifications for the same event,
PegaNotify keeps track for which events it has already sent out notifications
in a database table. To prevent this table from growing extremely large, old
events are occasionally purged from the table. This configuration options
specifies the minimum age in days before an event is purged from this table. To
prevent duplicate notifications, the value should never subceed `14`.

It should be an integer. The default value is `60`.

## Installation

To be able to install PegaNotify, you must be running at least PHP 7.4 and
MediaWiki 1.35. Echo must also be installed.

* Download and place the file(s) in a directory called `PegaNotify` in your
  `extensions/` folder.
* Add the following code at the bottom of your `LocalSettings.php`:

```php
wfLoadExtension( 'PegaNotify' );
```

* Run the **update script** which will automatically create the necessary
  database tables that this extension needs.  
* **Done** - Navigate to Special:Version on your wiki to verify that the
  extension is successfully installed.
