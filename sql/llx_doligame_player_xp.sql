CREATE TABLE IF NOT EXISTS llx_doligame_player_xp (
rowid integer NOT NULL auto_increment PRIMARY KEY,
fk_player integer NOT NULL,
code_action integer NOT NULL,
xp integer NOT NULL
) ENGINE=InnoDB;