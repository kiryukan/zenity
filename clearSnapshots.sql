SET foreign_key_checks = 0;
DELETE FROM snapshot;
DELETE FROM loadProfile;
DELETE FROM efficiencyIndicator;
DELETE FROM sqlInfo;
DELETE FROM request;
DELETE FROM event;
DELETE FROM instanceState;
DELETE FROM stat;
DELETE FROM advisory;
DELETE FROM advisory_snapshot;
DELETE FROM tablespace;
SET foreign_key_checks = 1;

