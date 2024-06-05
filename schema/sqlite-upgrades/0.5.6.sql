CREATE TABLE tbl_schema
(
    id      INTEGER PRIMARY KEY,
    version      TEXT  NOT NULL UNIQUE,
    timestamp   REAL NOT NULL,
    success TEXT,
    reason    TEXT

);

ALTER TABLE tbl_provider ADD COLUMN syncgroups TEXT DEFAULT NULL;
ALTER TABLE tbl_provider ADD COLUMN defaultgroup TEXT DEFAULT NULL;
ALTER TABLE tbl_provider ADD COLUMN usernameblacklist TEXT DEFAULT NULL;

INSERT INTO tbl_schema (version, timestamp, success, reason)
VALUES ('0.5.6', strftime('%s', 'now') * 1000, 'y', NULL);
