ALTER TABLE tbl_provider ADD COLUMN nooidcgroups enum ('y', 'n') DEFAULT 'n' NOT NULL;

INSERT INTO tbl_schema (version, timestamp, success, reason)
VALUES ('0.5.8', UNIX_TIMESTAMP() * 1000, 'y', NULL);
