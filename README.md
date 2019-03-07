# Backup and restore CLI

Moodle oddly omits the ability to perform backup and restore via the CLI. Consider that fixed.

---

## Installation

1. Install this directory at `/admin/tool/backupcli`.
2. Execute the usual Moodle upgrade process.
3. ???
4. Profit.

## Usage

Backup a course:

```
$ php admin/tool/backupcli/cli/backup.php --type=course --id=123 --file=$HOME/backup-123.mbz
```

Or section:

```
$ php admin/tool/backupcli/cli/backup.php --type=section --id=123 --file=$HOME/backup-123.mbz
```

Or activity:

```
$ php admin/tool/backupcli/cli/backup.php --type=activity --id=123 --file=$HOME/backup-123.mbz
```

Restore a backup to a new course in th specified category:

```
$ php admin/tool/backupcli/cli/restore.php --file=$HOME/backup-123.mbz --categoryid=1
```

Or overwrite the specified course, deleting existing content:

```
$ php admin/tool/backupcli/cli/restore.php --file=$HOME/backup-123.mbz --courseid=123
```
