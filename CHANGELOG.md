# Changelog

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
