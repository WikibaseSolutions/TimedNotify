# TimedNotify

Provides a time-based notification system for Echo.

## Configuration

The following configuration options are available:

* `$wgTimedNotifyRunRate` (default: `0.05`) - determines the run rate of the notifier
* `$wgTimedNotifyRunDeferred` (default: `true`) - whether to run the
  notifications in a deferred update
* `$wgTimedNotifyPushedNotificationRetentionDays` (default: `60`) - the number
  of days to retain pushed notifications
* `$wgTimedNotifyEnabledNotifiers` - which notifiers to enable

### `$wgTimedNotifyRunRate`

This configuration parameter determines how often to run the notifications.
Calculating which notifications to send out and to whom is expensive. The
notifications are therefore only calculated approximately once every
`1/$wgTimedNotifyRunRate` requests. Thus, if you set your run rate to `0.01`,
the *probability* of running the notifications is 1 in 100 for every request.
Setting this to a lower number will increase performance, as the notifications
have to be calculated less often, but it will decrease the timeliness of the
notifications. That is, notifications may arrive later than expected. It is
recommended to keep this value between `0.01` (`1/100`) and `0.5` (`1/2`),
depending on the size of your wiki. For smaller wiki's, this value should be
greater than for large wiki's.

It should be a number between `0` and `1`. The default value is `0.05` (`1/20`).

### `$wgTimedNotifyRunDeferred`

This configuration parameter determines whether to run the notifications in a
deferred update. If this parameter is set to `true`, notifications are
calculated after a response has already been sent to the browser. This
calculation will therefore not impact the load time of the wiki. Unless you
have a specific reason to set this to `false`, you generally should not have to
change this.

It should be a boolean. The default value is `true`.

### `$wgTimedNotifyPushedNotificationRetentionDays`

This configuration parameter specifies the number of days to remember pushed
events. In order to prevent duplicate notifications for the same event,
TimedNotify keeps track for which events it has already sent out notifications
in a database table. To prevent this table from growing extremely large, old
events are occasionally purged from the table. This configuration options
specifies the minimum age in days before an event is purged from this table. To
prevent duplicate notifications, the value should never subceed `14`.

It should be an integer. The default value is `60`.

### `$wgTimedNotifyDisabledNotifiers`

This configuration parameter specifies which notifiers to disable. It should
be an array of booleans, where the key is the name of the notifier, and the
value is `true` to **disable** the notifier. By default, the array is empty
and all notifiers are enabled.

It should be an array. The default value is `[]`.

## Installation

To be able to install TimedNotify, you must be running at least PHP 7.4 and
MediaWiki 1.35. Echo must also be installed.

* Download and place the file(s) in a directory called `TimedNotify` in your
  `extensions/` folder.
* Add the following code at the bottom of your `LocalSettings.php`:

```php
wfLoadExtension( 'TimedNotify' );
```

* Run the **update script** which will automatically create the necessary
  database tables that this extension needs.  
* **Done** - Navigate to Special:Version on your wiki to verify that the
  extension is successfully installed.
