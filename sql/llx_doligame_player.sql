CREATE TABLE IF NOT EXISTS llx_doligame_player (
rowid integer NOT NULL auto_increment PRIMARY KEY,
fk_user integer NOT NULL,
level integer NOT NULL,
total_xp integer NOT NULL,
levelup_xp integer NOT NULL
) ENGINE=InnoDB;