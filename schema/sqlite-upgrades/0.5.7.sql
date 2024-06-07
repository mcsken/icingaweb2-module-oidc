ALTER TABLE tbl_provider ADD COLUMN required_groups TEXT DEFAULT NULL;

INSERT INTO tbl_schema (version, timestamp, success, reason)
VALUES ('0.5.7', strftime('%s', 'now') * 1000, 'y', NULL);
