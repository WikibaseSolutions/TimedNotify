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

## Adding a new notification type

Adding a new time-based notification type is relatively easy, and is quite
similar to [adding a new notification type to Echo
directly](https://www.mediawiki.org/wiki/Extension:Echo/Creating_a_new_notification_type).

### Notification definition

Each notifier must be defined in a class. This class must extend the base
`TimedNotify\Notifier` class, and may implement the following methods:

| **Method**                               | **Description**                                                                                                                                                                                                                                                                                                                                                                                                                                                                            |
|------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `getName()`                              | The name of the notifier (should be unique).                                                                                                                                                                                                                                                                                                                                                                                                                                               |
| `getPresentationModel()`                 | The class name of the presentation model. This corresponds directly to [presentation models in Echo](https://www.mediawiki.org/wiki/Extension:Echo/Creating_a_new_notification_type#EchoPresentationModel).                                                                                                                                                                                                                                                                                |
| `getNotifications()`                     | An array of notifications that should be sent. A notification should have the following form: <br/>```['id' => (string) a unique identifier for this notification (will automatically be scoped to the notifier). The notification will only be emitted if a notification with this key has not already been emitted. If this value is omitted, the notification will be emitted unconditionally. (optional), 'data' => (array) additional data to add to the notification. (optional)]``` |
| `getIcons()`                             | Additional icons to define.                                                                                                                                                                                                                                                                                                                                                                                                                                                                |
| `static getNotificationUsers(EchoEvent)` | The users that should be notified by the given event.                                                                                                                                                                                                                                                                                                                                                                                                                                      |
| `static getFilteredUsers(EchoEvent)`     | The list of users that should not be notified by this event.                                                                                                                                                                                                                                                                                                                                                                                                                               |                                       

### Notification registration

Notification registration happens in the method that corresponds to the
`TimedNotifyGetNotifierClasses` hook. This hook includes a single variable,
`&$notifierClasses` and contains a list of classes that extend `Notifier`. You
must add your new notifiers through this hook. For example:

```php
public function onTimedNotifyGetNotifierClasses( array &$notifierClasses ): void {
    $notifierClasses[] = MyCoolNotifier::class;
}
```

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
