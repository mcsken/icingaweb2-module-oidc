CREATE TABLE tbl_provider
(
    id      INTEGER PRIMARY KEY,
    name    TEXT,
    url    TEXT,
    secret    TEXT,
    appname    TEXT,
    logo    TEXT,
    buttoncolor    TEXT,
    textcolor    TEXT,
    caption    TEXT,
    enabled TEXT,
    ctime   REAL,
    mtime   REAL
);

CREATE TABLE tbl_user
(
    id      INTEGER PRIMARY KEY,
    name    TEXT,
    email    TEXT,
    provider_id INTEGER,
    mapped_local_user TEXT,
    mapped_backend TEXT,
    active INTEGER,
    lastlogin   REAL,
    ctime   REAL,
    mtime   REAL
);

CREATE TABLE tbl_group
(
    id      INTEGER PRIMARY KEY,
    name    TEXT,
    parent    INTEGER,
    provider_id INTEGER,
    ctime   REAL,
    mtime   REAL
);

CREATE TABLE tbl_group_membership
(
    id      INTEGER PRIMARY KEY,
    group_id      INTEGER,
    provider_id INTEGER,
    username    TEXT,
    ctime   REAL,
    mtime   REAL
);
CREATE TABLE tbl_schema
(
    id      INTEGER PRIMARY KEY,
    version      TEXT UNIQUE NOT NULL,
    timestamp   REAL NOT NULL,
    success TEXT,
    reason    TEXT,

);

INSERT INTO tbl_schema (version, timestamp, success, reason)
VALUES ('0.5.5', UNIX_TIMESTAMP() * 1000, 'y', NULL);
