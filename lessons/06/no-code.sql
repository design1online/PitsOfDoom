/* Map table, this stores all of our map information */
CREATE TABLE map (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR( 100 ) NOT NULL ,
  `depth` INT NOT NULL ,
  UNIQUE (`id`)
) TYPE = MYISAM;

/* Now we create our map data table */
 CREATE TABLE  mapdata (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `mapid` INT NOT NULL ,
  `x` INT NOT NULL ,
  `y` INT NOT NULL ,
  `z` INT NOT NULL ,
  `value` CHAR( 1 ) NOT NULL ,
  UNIQUE (`id`)
) TYPE = MYISAM;