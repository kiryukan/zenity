DELIMITER $$

CREATE PROCEDURE get_stat()
BEGIN
DECLARE num_rows INTEGER DEFAULT 0;
DECLARE i INTEGER DEFAULT 0;
DECLARE columnName VARCHAR(100);
DECLARE tableName VARCHAR(100);
DECLARE col CURSOR FOR 
  SELECT table_name,column_name 
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA="zenityService";
DECLARE CURSOR element FOR 
  select min(columnName),max(columnName),avg(columnName)   from tableName;
select FOUND_ROWS() into num_rows;


SET i = 1;
the_loop: LOOP

   IF i > num_rows THEN
        CLOSE col;
        LEAVE the_loop;
    END IF;
    FETCH col.column_name INTO columnName;
    FETCH col.table_name INTO tableName;

    select min(columnName),max(columnName),avg(columnName) from tableName;
    SET i = i + 1;
END LOOP the_loop;

END$$
 
DELIMITER ;
CALL get_stat();
