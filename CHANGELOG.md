# Changelog

### 1.2.7

* Added: PHP 8 support
### 1.2.6

* Added: Setting to enable storage of HTML instead of Text

### 1.2.5

* Fixed: Added no search HTML for performance reasons.

### 1.2.4

* Fixed CommunicationItem `getSendable()` to include the recipient on the `Sendable`.
* Removed duplicate `FailureReason` field from CommunicationItem
* Changed `FailureReason` to LongStringColumn in CommunicationItemSendAttempt to ensure full message logged.

### 1.2.3

* Fixed: Mysql 5.6 support by optionally not using native json columns

### 1.2.2

* Fixed:    DateSent in attempt log not being set correctly.

### 1.2.1

* Fixed:    Throttle not correctly adhered to.

### 1.2.0

* Added:    New throttle feature to combat SES throttling etc.
* Added:    New attempt log table
* Fixed:    Double send block repaired

### 1.1.9

* Fixed:	Fixed the issue with no message Id causing null issues

### 1.1.8

* Fixed:	Items being marked sent when delivery failed.

### 1.1.5

* Added:        Supports Pikaday leaf in place of common controls Date input. If present in the project it will be used automatically.

### 1.1.4

* Fixed:        Made main list only show unsent items
* Fixed:        Sending enabled toggle works again
* Fixed:        Recipient text search

### 1.1.3

* Added:	Added --limit= to the custard option to only send a fixed number of queued emails
* Changed:	The custard command now uses the RequiresConnectionCommand base class

### 1.1.2

* Added:	Queue control leaf added

### 1.1.1

* Change:	CommunicationProcessor::sendPackage() now gets the correct Communication model if it has been
		augmented in a project.

### 1.1.0

* Fixed:        Fixed issue with Dependency Injection Container not being accessed correctly.

### 1.0.0

* Added:        Added Changelog
