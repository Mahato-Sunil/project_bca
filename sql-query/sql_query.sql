NameCREATE TABLE bio_data (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    FirstName VARCHAR(25),
    MiddleName VARCHAR(25),
    LastName VARCHAR(25),
    DOB DATE,
    Citizenship VARCHAR(30) UNIQUE
);
CREATE TABLE contact_info (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    PrimaryPhone VARCHAR(10),
    SecondaryPhone VARCHAR(10),
    Email VARCHAR(50),
    Ctzn_no VARCHAR(30),
    FOREIGN KEY (Ctzn_no) REFERENCES bio_data(Citizenship)
);
CREATE TABLE permanent_address (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    Province_p VARCHAR(10),
    District_p VARCHAR(10),
    Ctzn_no VARCHAR(30) UNIQUE,
    FOREIGN KEY (Ctzn_no) REFERENCES bio_data(Citizenship)
);
CREATE TABLE current_address (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    Province VARCHAR(10),
    District VARCHAR(10),
    Ctzn_no VARCHAR(30) UNIQUE,
    FOREIGN KEY (Ctzn_no) REFERENCES bio_data(Citizenship)
);
CREATE TABLE credentials (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(50),
    Hash VARCHAR(255),
    reg_no VARCHAR(50) UNIQUE,
    FOREIGN KEY (reg_no) REFERENCES bio_data(Citizenship)
);
CREATE TABLE user_setting (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    Alternate_Email VARCHAR(30),
    Bio VARCHAR(255),
    user_key VARCHAR(30) UNIQUE,
    FOREIGN KEY (user_key) REFERENCES bio_data(Citizenship)
);
CREATE TABLE family_data (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(50),
    Email VARCHAR(50),
    Contact VARCHAR(10) UNIQUE,
    Photo VARCHAR(255),
    Ctzn_id VARCHAR(50),
    FOREIGN KEY (Ctzn_id) REFERENCES bio_data(Citizenship)
);
CREATE TABLE location (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(50),
    Phone VARCHAR(10) UNIQUE,
    Citizenship VARCHAR(50) UNIQUE,
    Latitude VARCHAR(20),
    Longitude VARCHAR(20),
    Time VARCHAR(100),
    FOREIGN KEY (Citizenship) REFERENCES bio_data(Citizenship)
);

CREATE TABLE user_status (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    Status VARCHAR(20)
    userKey  VARCHAR(50) UNIQUE,
    FOREIGN KEY (userKey) REFERENCES bio_data(Citizenship)
);


-- sql query to alter the foreign key type and constraints name 
alter table contact_info Drop FOREIGN key contact_info_ibfk_1;
ALTER TABLE contact_info
ADD CONSTRAINT contact_info_ibfk_1
FOREIGN KEY (Ctzn_no)
REFERENCES bio_data (Citizenship)
ON DELETE CASCADE;

alter table credentials Drop FOREIGN key credentials_ibfk_1;
ALTER TABLE credentials
ADD CONSTRAINT credentials_ibfk_1
FOREIGN KEY (reg_no)
REFERENCES bio_data (Citizenship)
ON DELETE CASCADE;

alter table current_address Drop FOREIGN key current_address_ibfk_1;
ALTER TABLE current_address
ADD CONSTRAINT current_address_ibfk_1
FOREIGN KEY (Ctzn_no)
REFERENCES bio_data (Citizenship)
ON DELETE CASCADE;

alter table family_data Drop FOREIGN key family_data_ibfk_1;
ALTER TABLE family_data
ADD CONSTRAINT family_data_ibfk_1
FOREIGN KEY (Ctzn_id)
REFERENCES bio_data (Citizenship)
ON DELETE CASCADE;

alter table location Drop FOREIGN key location_ibfk_1;
ALTER TABLE location
ADD CONSTRAINT location_ibfk_1
FOREIGN KEY (Citizenship)
REFERENCES bio_data (Citizenship)
ON DELETE CASCADE;

alter table permanent_address Drop FOREIGN key permanent_address_ibfk_1;
ALTER TABLE permanent_address
ADD CONSTRAINT permanent_address_ibfk_1
FOREIGN KEY (Ctzn_no)
REFERENCES bio_data (Citizenship)
ON DELETE CASCADE;

alter table user_setting Drop FOREIGN key user_setting_ibfk_1;
ALTER TABLE user_setting
ADD CONSTRAINT user_setting_ibfk_1
FOREIGN KEY (user_key)
REFERENCES bio_data (Citizenship)
ON DELETE CASCADE;

alter table user_status Drop FOREIGN key user_status_ibfk_1;
ALTER TABLE user_status
ADD CONSTRAINT user_status_ibfk_1
FOREIGN KEY (userKey)
REFERENCES bio_data (Citizenship)
ON DELETE CASCADE;


-- code to truncate tables 
TRUNCATE bio_data;
TRUNCATE contact_info;
TRUNCATE credentials;
TRUNCATE current_address;
TRUNCATE family_data;
TRUNCATE location;
TRUNCATE permanent_address;
TRUNCATE user_status;
TRUNCATE user_setting;
TRUNCATE location_updates;
truncate notification;
TRUNCATE user_notification;
