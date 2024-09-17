CREATE TRIGGER family_notify_trigger 
ON bio_data
AFTER INSERT
AS
BEGIN
  -- No need to declare variables within the trigger
  INSERT INTO notification (NoticeId, NoticeMsg, NoticeTime)
  SELECT Citizenship, 'New User: ' + FirstName + ' is Registered to System', CONVERT(varchar(30), GETDATE(), 120)
  FROM Inserted;  -- Directly reference inserted data
END;


-- for mysql server 
CREATE TRIGGER family_notify_trigger
AFTER INSERT ON bio_data
FOR EACH ROW
BEGIN
  INSERT INTO notification (NoticeId, NoticeMsg, NoticeTime)
  Values (NEW.Citizenship, CONCAT(' User : ', NEW.FirstName, ' is requesting for approval. '), NOW());
END;

-- for location updates 
DELIMITER $$

CREATE TRIGGER location_update_trigger
AFTER INSERT ON location
FOR EACH ROW
BEGIN
  INSERT INTO location_updates (LocationId, Latitude, Longitude, RequestTime)
  VALUES (NEW.Citizenship, NEW.Latitude, NEW.Longitude, NOW());
END $$

DELIMITER ;

