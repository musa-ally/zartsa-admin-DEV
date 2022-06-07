-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2022 at 01:08 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zartsa_system`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_check_bol` (IN `_bolNumber` VARCHAR(45))  BEGIN
SELECT
	bol.id AS bid,
	bol.number AS bol,
	bol.created_at AS bol_date,
	bol.consignee,
	c.number AS cargo_no,
	c.cargo_type,
	c.tbl_bill_of_ladings_voyages_id AS voyage_id,
	voyages.vessels_name as ves_name,
	c.weight_kg,
	c.id AS id,
	c.remarks,
	c.container_type,
	c.is_selected,
	appStatus.NAME AS application_status,
  IF(NOW() > ADDDATE(bol.created_at, 7), IFNULL( NULLIF( billStat.NAME, '' ), "NOT_INITIATED" ), IFNULL( NULLIF( billStat.NAME, '' ), "NO_PAYMENT" )) AS bill_status,
	er.spot_buying AS xchange_rate,
	voyages.number AS voy_no,
	voyages.estimated_arrival_date AS eat,
	voyages.arrival_date AS ad,
	voyages.departure_date AS depd
FROM
	zpc_system.tbl_bill_of_ladings AS bol
	INNER JOIN zpc_system.tbl_cargo AS c ON c.tbl_bill_of_ladings_id = bol.id
	LEFT JOIN zpc_system.customer_cargo_selections AS ccs ON c.id = ccs.cargo_id
	LEFT JOIN zpc_system.customer_application_bills AS cab ON cab.customer_service_applications_id = ccs.customer_service_applications_id
	LEFT JOIN zpc_system.customer_service_applications AS csa ON csa.id = cab.customer_service_applications_id
	LEFT JOIN zpc_system.application_status AS appStatus ON appStatus.code = csa.application_status_code
	LEFT JOIN zpc_system.bill_status AS billStat ON billStat.code = cab.bill_status_code
  LEFT JOIN zpc_system.exchange_rate AS er ON er.id
	LEFT JOIN zpc_system.tbl_voyages as voyages ON bol.tbl_voyages_id = voyages.id
WHERE bol.number = _bolNumber;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_check_bol_destuffing` (IN `_bolNumber` VARCHAR(45))  BEGIN
SELECT
	bol.id AS bid,
	bol.number AS bol,
	bol.created_at AS bol_date,
	bol.consignee,
	bol.shipper,
	c.number AS cargo_no,
	c.cargo_types_id AS cargo_type_id,
	c.bill_of_ladings_voyages_id AS voyage_id,
	c.bill_of_ladings_voyages_vessels_id AS vessel_id,
	c.seal_number,
	c.weight_kg,
	c.id AS id,
	c.remarks,
	ct.NAME,
	ct.CODE AS cargo_code,
	conT.type,
	c.is_selected,
	appStatus.NAME AS application_status,
	billStat.NAME AS bill_status, 
  IF(conT.type = '20DV', 50000,100000) AS de_bill,
	s.`name` AS service_name,
	ccs.id AS ccsid,
	ccs.stage AS service_stage,
	er.spot_buying AS xchange_rate,
	voyages.number AS voy_no,
	voyages.estimated_arrival_date AS eat,
	voyages.arrival_date AS ad,
	voyages.departure_date AS depd,
	vessels.`name` AS ves_name,
	shipping_lines.`name` AS sl_name
FROM
	zpc_system.bill_of_ladings AS bol
	INNER JOIN zpc_system.cargo AS c ON c.bill_of_ladings_id = bol.id
	INNER JOIN zpc_system.cargo_types AS ct ON c.cargo_types_id = ct.id
	LEFT OUTER JOIN zpc_system.container_types AS conT ON conT.id = c.container_types_id
	LEFT JOIN zpc_system.customer_cargo_selections AS ccs ON c.id = ccs.cargo_id
	LEFT JOIN zpc_system.customer_application_bills AS cab ON cab.customer_service_applications_id = ccs.customer_service_applications_id
	LEFT JOIN zpc_system.customer_service_applications AS csa ON csa.id = cab.customer_service_applications_id
	LEFT JOIN zpc_system.application_status AS appStatus ON appStatus.code = csa.application_status_code
	LEFT JOIN zpc_system.bill_status AS billStat ON billStat.code = cab.bill_status_code
	LEFT JOIN zpc_system.services AS s ON s.code = ccs.customer_service_applications_services_code
	LEFT JOIN zpc_system.exchange_rate AS er ON er.id
	LEFT JOIN zpc_system.voyages ON bol.voyages_id = voyages.id
	LEFT JOIN zpc_system.vessels ON bol.voyages_vessels_id = vessels.id
	LEFT JOIN zpc_system.shipping_lines ON vessels.shipping_lines_id = shipping_lines.id
WHERE
	bol.number = _bolNumber
AND
 ccs.is_destuffed = NULLIF(is_destuffed, 'NO') IS NULL
AND
appStatus.code = 'DS001';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_check_user_has_permission` (IN `_user_id` INT, IN `_permission_name` VARCHAR(45))  BEGIN
declare permissionId int;
select id into permissionId from zpc_system.role_permissions where name=_permission_name limit 1;

if permissionId is not null then
	if exists(select internal_users_id from zpc_system.internal_users_has_role_permissions where internal_users_id=_user_id and role_permissions_id=permissionId limit 1) then
		select 'User has a permission' as message, '300' as status_code;
	else
		select 'User does not have that permission' as message, '304' as status_code;
	end if;
else
	select 'Permission does not exist' as message, '315' as status_code;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_check_user_has_role` (IN `_user_id` INT, IN `_role_name` VARCHAR(45))  BEGIN
declare roleId int;
select id into roleId from zpc_system.internal_user_roles where name=_role_name limit 1;

if roleId is not null then
	if exists(select internal_users_id from zpc_system.internal_users_has_internal_user_roles where internal_users_id=_user_id and internal_user_roles_id=roleId limit 1) then
		select 'User has a role' as message, '300' as status_code;
	else
		select 'User does not have that role' as message, '304' as status_code;
	end if;
else
	select 'Role does not exist' as message, '315' as status_code;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_customer_application_data` (IN `appID` VARCHAR(10), IN `usertype` VARCHAR(3))  BEGIN

IF usertype = 'I' THEN
SELECT
	application_status.`name` AS application_status,
	customer_service_applications.created_at AS app_date,
	internal_users.first_name,
	internal_users.last_name,
	internal_users.phone_number, 
	customer_service_applications.payer_full_name,
	customer_service_applications.payer_phone_number,
	customer_service_applications.payer_email_address
FROM
	customer_service_applications
	LEFT JOIN application_status ON customer_service_applications.application_status_code = application_status.code
	INNER JOIN internal_users ON customer_service_applications.external_users_id = internal_users.id
WHERE
	customer_service_applications.id = appID AND customer_service_applications.user_type = 'I';
ELSEIF usertype = 'E' THEN
SELECT
	application_status.`name` AS application_status,
	customer_service_applications.created_at AS app_date,
	external_users.first_name,
	external_users.last_name,
	external_users.phone_number,
	customer_service_applications.payer_full_name,
	customer_service_applications.payer_phone_number,
	customer_service_applications.payer_email_address	
FROM
	customer_service_applications
	LEFT JOIN application_status ON customer_service_applications.application_status_code = application_status.code
	INNER JOIN external_users ON customer_service_applications.external_users_id = external_users.id
WHERE
	customer_service_applications.id = appID AND customer_service_applications.user_type = 'E';
END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_edit_service` (IN `service_id` INT, IN `name` VARCHAR(45), IN `description` TEXT)  BEGIN
UPDATE zpc_system.services SET
			name = name, description = description, updated_at = NOW() WHERE id = service_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_active_services` ()  BEGIN
SELECT `id`, `name`, `description`, `service_status_code`, `code`, `icon`
		FROM zpc_system.services where service_status_code='AC001';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_bill_of_ladings` (IN `_voyageId` INT)  BEGIN
SELECT id, number, consignee, shipper,notify,port_of_lading
		FROM zpc_system.bill_of_ladings where voyages_id=_voyageId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_cargo_by_bol` (IN `_bolNumber` VARCHAR(45))  BEGIN
SELECT bol.number as bol_number, bol.consignee, bol.notify, bol.port_of_lading, bol.created_at,
cargo.id as cargo_id, cargo.number as cargo_number, cargo.weight_kg, cargo.remarks, cargo.cargo_type,
cargo.container_size, cargo.is_electric, cargo.content
		FROM zpc_system.tbl_bill_of_ladings as bol
        inner join zpc_system.tbl_cargo as cargo on cargo.tbl_bill_of_ladings_id = bol.id
        where bol.number=_bolNumber;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_cargo_to_be_destuffed_by_bol` (IN `_bolNumber` VARCHAR(45))  BEGIN
SELECT bol.number as bol_number, bol.consignee, bol.notify, bol.port_of_lading, bol.created_at,
cargo.id as cargo_id, cargo.number as cargo_number, cargo.weight_kg, cargo.remarks, cargo.cargo_type,
cargo.container_size
		FROM zpc_system.tbl_bill_of_ladings as bol
        inner join zpc_system.tbl_cargo as cargo on cargo.tbl_bill_of_ladings_id = bol.id
        inner join zpc_system.customer_cargo_selections as cSelection on cSelection.cargo_tbl_bill_of_ladings_id=bol.id
        where bol.number=_bolNumber 
        and cSelection.customer_service_applications_services_code='CL001' 
        and cSelection.customer_service_applications_application_status_code='DS001'
        and cargo.id = cSelection.cargo_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_count_applications` ()  BEGIN
SELECT  (
        SELECT COUNT( customer_service_applications.id )
        FROM   customer_service_applications
        ) AS totalapplications,
        (
        SELECT COUNT( customer_service_applications.application_status_code )
        FROM   customer_service_applications
				LEFT JOIN application_status AS aps ON aps.code = customer_service_applications.application_status_code WHERE aps.`code` = 'CL001'
        ) AS cleared,
        (
        SELECT COUNT( customer_service_applications.application_status_code )
        FROM   customer_service_applications
				LEFT JOIN application_status AS aps ON aps.code = customer_service_applications.application_status_code WHERE aps.`code` = 'NC001'
        ) AS notcleared,
				(
        SELECT COUNT( customer_service_applications.application_status_code )
        FROM   customer_service_applications
				LEFT JOIN application_status AS aps ON aps.code = customer_service_applications.application_status_code WHERE aps.`code` = 'DS001'
        ) AS discharged
FROM dual;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_customer_applications` (IN `app_code` VARCHAR(10), IN `app_start` VARCHAR(20), IN `app_end` VARCHAR(20), IN `usertype` VARCHAR(3))  BEGIN

IF usertype = 'I' THEN
SELECT
  customer_service_applications.id AS internal_app_id,
	customer_service_applications.services_code,
	customer_service_applications.external_users_id,
	services.`name` AS service_name,
	services.code,
	service_status.`name` AS service_status,
	customer_service_applications.id AS application_id,
	customer_service_applications.created_at,
	application_status.`name` AS application_status,
	internal_users.first_name,
	internal_users.last_name,
  internal_users.id 
FROM
	customer_service_applications
	INNER JOIN services ON customer_service_applications.services_code = services.code
	LEFT JOIN service_status ON services.service_status_code = service_status.code
	INNER JOIN application_status ON customer_service_applications.application_status_code = application_status.code
	LEFT JOIN internal_users ON customer_service_applications.external_users_id = internal_users.id
WHERE
application_status.`code` = app_code
AND
DATE(customer_service_applications.created_at) BETWEEN app_start AND app_end AND customer_service_applications.user_type = 'I';
ELSEIF usertype = 'E' THEN
SELECT
  customer_service_applications.id AS external_app_id,
	customer_service_applications.services_code,
	customer_service_applications.external_users_id,
	services.`name` AS service_name,
	services.code,
	service_status.`name` AS service_status,
	customer_service_applications.id AS application_id,
	customer_service_applications.created_at,
	application_status.`name` AS application_status,
	external_users.first_name,
	external_users.last_name,
  external_users.id
FROM
	customer_service_applications
	INNER JOIN services ON customer_service_applications.services_code = services.code
	LEFT JOIN service_status ON services.service_status_code = service_status.code
	INNER JOIN application_status ON customer_service_applications.application_status_code = application_status.code
	LEFT JOIN external_users ON customer_service_applications.external_users_id = external_users.id
WHERE
application_status.`code` = app_code
AND
DATE(customer_service_applications.created_at) BETWEEN app_start AND app_end AND customer_service_applications.user_type = 'E';
END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_customer_bol` (IN `_bolNumber` VARCHAR(45))  BEGIN
SELECT bol.number as bol_number, bol.consignee, bol.notify, bol.port_of_lading, bol.created_at,
cargo.id as cargo_id, cargo.number as cargo_number, cargo.weight_kg, cargo.remarks, cargo.cargo_type,
cargo.container_size, cargo.is_electric
		FROM zpc_system.tbl_bill_of_ladings as bol
        inner join zpc_system.tbl_cargo as cargo on cargo.tbl_bill_of_ladings_id = bol.id
        where bol.number=_bolNumber;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_customer_cargo_bills` (IN `appid` BIGINT, IN `service_name` VARCHAR(20))  BEGIN
	SELECT
	customer_service_applications.id,
	customer_cargo_selections.customer_service_applications_id,
	customer_cargo_selections.cargo_id,
	cargo.weight_kg,
	cargo.number,
	cargo_types.`name`,
	customer_cargo_selections.created_at,
	customer_application_bills.control_number,
	customer_application_bills.id AS bill_id,
	customer_application_bills.amount_usd,
	customer_application_bills.amount_tzs,
	customer_application_bills.exchange_rate,
	customer_application_bills.bill_status_code,
	bill_status.`name` AS bill_status,
  bol.number AS bl	
FROM
	customer_service_applications
	INNER JOIN customer_cargo_selections ON customer_service_applications.id = customer_cargo_selections.customer_service_applications_id
	INNER JOIN cargo ON customer_cargo_selections.cargo_id = cargo.id
	INNER JOIN cargo_types ON cargo.cargo_types_id = cargo_types.id
	LEFT JOIN customer_application_bills ON customer_service_applications.id = customer_application_bills.customer_service_applications_id
	LEFT JOIN bill_status ON customer_application_bills.bill_status_code = bill_status.code
	LEFT JOIN services ON services.id = services.id
	LEFT JOIN bill_of_ladings AS bol ON bol.id = customer_cargo_selections.cargo_bill_of_ladings_id
WHERE
	customer_service_applications.id = appid
AND
  services.`name` = service_name;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_customer_cargo_selection` (IN `_service_code` VARCHAR(10), IN `_bol` VARCHAR(45))  BEGIN
# app status code is used to filter cargo
select selection.cargo_id, selection.cargo_cargo_type as cargo_type, selection.cargo_tbl_bill_of_ladings_id as bol_id,
selection.cargo_tbl_bill_of_ladings_tbl_voyages_id as voyages_id, selection.customer_service_applications_id as application_id
from zpc_system.customer_cargo_selections as selection
inner join zpc_system.tbl_bill_of_ladings as bol on bol.id=selection.cargo_tbl_bill_of_ladings_id
where customer_service_applications_services_code=_service_code and bol.number=_bol;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_customer_service_applications` (IN `_user_id` INT)  BEGIN
select sa.id as application_id, s.name as service_name, app_status.name as application_status, bs.name as bill_status,
ab.amount_tzs, ab.control_number, ab.expiring_datetime, ab.id as application_bill_id, sa.application_status_code as status_code
from zpc_system.customer_service_applications as sa
inner join zpc_system.services as s on s.code=sa.services_code
inner join zpc_system.customer_application_bills as ab on ab.customer_service_applications_id=sa.id
inner join zpc_system.application_status as app_status on app_status.code=sa.application_status_code
inner join zpc_system.bill_status as bs on bs.code=ab.bill_status_code
where sa.external_users_id=_user_id and sa.user_type='E';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_permissions` ()  BEGIN
select id, display_name from zpc_system.role_permissions;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_ports` ()  BEGIN
SELECT id, name, address, service_status_id
		FROM zpc_system.zpc_ports;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_roles` ()  BEGIN
select id, display_name from zpc_system.internal_user_roles;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_services` ()  BEGIN
SELECT id, name, description, service_status_code, code, icon
		FROM zpc_system.services;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_ship_lines` ()  BEGIN
SELECT name, code, service_status_id
		FROM zpc_system.shipping_lines;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_tbl_bill_of_ladings` (IN `_voyageId` INT)  BEGIN
SELECT id, number, consignee,notify,port_of_lading
		FROM zpc_system.tbl_bill_of_ladings where tbl_voyages_id=_voyageId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_tbl_cargo` (IN `_bolId` INT)  BEGIN
SELECT id, number, weight_kg, remarks, cargo_type, container_type, container_size
		FROM zpc_system.tbl_cargo where tbl_bill_of_ladings_id=_bolId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_tbl_voyages` (IN `_last_id` INT)  BEGIN
select id, number, estimated_arrival_date, arrival_date, departure_date, vessels_name from zpc_system.tbl_voyages where id>_last_id limit 20;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_user_permissions` (IN `_user_id` INT)  BEGIN
select role_permissions_id as permission_id from zpc_system.internal_users_has_role_permissions where internal_users_id=_user_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_user_profile` (IN `_user_id` INT)  BEGIN
select id, first_name, last_name, username, phone_number, email, id_number, gender from zpc_system.internal_users where id=_user_id limit 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_user_roles` (IN `_user_id` INT)  BEGIN
select internal_user_roles_id as role_id from zpc_system.internal_users_has_internal_user_roles where internal_users_id=_user_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_vessels` (IN `shipLineCode` VARCHAR(20))  BEGIN
declare shippingLineId int;
select id into shippingLineId from zpc_system.shipping_lines where code=shipLineCode limit 1;
if shippingLineId is not null then
select id, name, service_status_id from zpc_system.vessels where shipping_lines_id=shippingLineId;
else
select 'Shipping line does not exist' as message, '315' as status_code;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_voyages` (IN `_vesselId` INT)  BEGIN
SELECT id, number, estimated_arrival_date, arrival_date,departure_date
		FROM zpc_system.voyages where vessels_id=_vesselId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insert_customer_application_bill` (IN `_user_id` INT, IN `_service_code` VARCHAR(20), IN `_amount_usd` DOUBLE, IN `_amount_tzs` DOUBLE, IN `_exchange_rate` DOUBLE, IN `_fullName` VARCHAR(60), IN `_email` VARCHAR(60), IN `_phone` INT)  BEGIN
# check if service already exists
declare serviceId int;
declare customerServiceAppId int;
declare customerBillId int;

DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        SHOW ERRORS LIMIT 1;
        ROLLBACK;
        select 'SQLException encountered' message, '515' status_code;
    END;
    
select id into serviceId from zpc_system.services where code=_service_code limit 1;
# Check if service exist
if serviceId is not null then
	# Check if user exists
    if exists(select id from zpc_system.external_users where id=_user_id limit 1) then
	############ Start transaction ############
	START TRANSACTION;
	# 1. Insert into customer service app
	INSERT INTO zpc_system.customer_service_applications (services_code, external_users_id, application_status_code, user_type,
    payer_full_name, payer_phone_number, payer_email_address, created_at, updated_at) 
		VALUE (_service_code, _user_id, 'NC001', 'E', _fullName, _email, _phone, NOW(), NOW());
        select LAST_INSERT_ID() into customerServiceAppId;
        if customerServiceAppId is not null then
			# 2. insert into customer app bill
            INSERT INTO zpc_system.customer_application_bills
			(amount_tzs, amount_usd, exchange_rate, customer_service_applications_id, customer_service_applications_services_code
            , customer_service_applications_external_users_id, bill_status_code, user_type, created_at, updated_at) 
			VALUE
			(_amount_tzs, _amount_usd, _exchange_rate, customerServiceAppId, _service_code, _user_id, 'PE001', 'E', NOW(), NOW());
			select LAST_INSERT_ID() into customerBillId;
            if (row_count() > 0) then
			############ commit transaction
            COMMIT;
				SELECT 'Your bill is being processed' as message, '300' as status_code, customerServiceAppId as app_id, customerBillId as bill_id;
			else
				ROLLBACK;
				SELECT 'Could not insert bill' as message, '315' as status_code;
			end if;
		else
            ROLLBACK;
        end if;
    else
		select 'User does not exists' as message, '315' as status_code;
    end if;
else
	select 'Service does not exists' as message, '315' as status_code;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insert_daily_bill` (IN `_serviceId` INT, IN `_amountTzs` DOUBLE, IN `_amountUsd` DOUBLE, IN `_exchangeRate` DOUBLE, IN `_expireTime` TIMESTAMP)  BEGIN
declare amountTzs double;
declare amountUsd double;

select amount_tzs, amount_usd into amountTzs, amountUsd from zpc_system.customer_application_bills_daily where services_id=_serviceId 
and created_at >= CURDATE() AND created_at < CURDATE() + INTERVAL 1 DAY limit 1 for update;
if amountTzs is not null then
	# it exists
	update zpc_system.customer_application_bills_daily set amount_tzs=(amountTzs+_amountTzs), amount_usd=(amountUsd+_amountUsd) where services_id=_serviceId 
	and created_at >= CURDATE() AND created_at < CURDATE() + INTERVAL 1 DAY;
else
	# it does not exist so we create
	insert into zpc_system.customer_application_bills_daily (amount_tzs, amount_usd, exchange_rate, expiring_datetime, services_id, created_at, updated_at)
	values (_amountTzs, _amountUsd, _exchangeRate, _expireTime, _serviceId, NOW(), NOW());
	if (row_count() > 0) then
		select 'Bill inserted successfully' as message, 300 as status_code;
	else
		select 'Could not insert bill' as message, 315 as status_code;
	end if;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insert_manifest` (IN `shipLineCode` VARCHAR(20), IN `vesselName` VARCHAR(45), IN `voyageNumber` VARCHAR(45), IN `estimatedADate` DATE, IN `departureDate` DATE, IN `portId` INT, IN `bolNumber` VARCHAR(45), IN `bolConsignee` VARCHAR(45), IN `bolShipper` VARCHAR(45), IN `bolNotify` VARCHAR(45), IN `portOfLading` VARCHAR(45), IN `cargoNumber` VARCHAR(45), IN `cargoSealNumber` VARCHAR(45), IN `cargoWeight` INT, IN `cargoRemarks` TEXT, IN `cargoTypesId` INT, IN `containerTypesId` INT, IN `generalCargoTypesId` INT)  BEGIN
# manifest includes: voyages(journey), vessel(ship), shipping line
# shipping line has a code which is unique but vessel uses name which is also unique but has bigger probability of mistake

declare ship_line_id int;
declare vessel_id int;
declare voyageId int;
declare bolId int;
declare validationMsg varchar(100);


DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        SHOW ERRORS LIMIT 1;
        ROLLBACK;
        select 'SQLException encountered' message, '515' status_code;
    END;
# 1. Check if shipping line exists
select id into ship_line_id from zpc_system.shipping_lines WHERE code=shipLineCode limit 1;

if ship_line_id is not null then
	# 2. Check if Vessel exists
	select id into vessel_id from zpc_system.vessels where shipping_lines_id=ship_line_id and name=vesselName limit 1;
		if vessel_id is not null then
            # 3. Check if voyage exists
            select id into voyageId from zpc_system.voyages where number=voyageNumber and vessels_id=vessel_id limit 1;
            if voyageId is null then
				# voyage does not exist, so create a new one
                insert into zpc_system.voyages (number, estimated_arrival_date, departure_date, zpc_ports_id, vessels_id,
                created_at, updated_at)
                values(voyageNumber, estimatedADate, departureDate, portId, vessel_id, NOW(), NOW());
                select LAST_INSERT_ID() into voyageId;
            end if;
            
            ############ Do fields validation ############
            select fn_validate_manifest_input(cargoTypesId, containerTypesId, generalCargoTypesId, portId) into validationMsg;
            if validationMsg is not null then
				select validationMsg message, '315' status_code;
            else
				 ############ Start transaction ############
				START TRANSACTION;
				# 5. Insert bill of lading
                select id into bolId from zpc_system.bill_of_ladings where number = bolNumber limit 1;
                if bolId is null then
					insert into zpc_system.bill_of_ladings (number, consignee, shipper, notify, port_of_lading, voyages_id, voyages_zpc_ports_id,
					voyages_vessels_id, created_at, updated_at)
						values(bolNumber, bolConsignee, bolShipper, bolNotify, portOfLading, voyageId, portId, vessel_id, NOW(), NOW());
						
					select LAST_INSERT_ID() into bolId;
                end if;
				if bolId is not null then
					# 6. Insert Cargo
					insert into zpc_system.cargo (number, seal_number, weight_kg, remarks, cargo_types_id, container_types_id,
                    general_cargo_types_id, bill_of_ladings_id, bill_of_ladings_voyages_id, bill_of_ladings_voyages_zpc_ports_id,
                    bill_of_ladings_voyages_vessels_id, created_at, updated_at)
                    values(cargoNumber, cargoSealNumber, cargoWeight, cargoRemarks, cargoTypesId, containerTypesId,
                    generalCargoTypesId, bolId, voyageId, portId, vessel_id, NOW(), NOW());
						if (row_count() > 0) then
							############ commit transaction
                            COMMIT;
                            SELECT 'Manifest added successfully' as message, '300' as status_code;
						else
							SELECT 'Could not insert cargo' as message, '315' as status_code;
						end if;
				else
					SELECT 'Could not get Bill of leading' as message, '305' as status_code;
				end if;
            end if;
		else
			SELECT 'Vessel does not exist' as message, '315' as status_code;
		end if;
	else
		SELECT 'Shipping line does not exist' as message, '315' as status_code;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insert_port` (IN `port_name` VARCHAR(45), IN `address` VARCHAR(45))  BEGIN
# check if port already exist
if exists(select name from zpc_system.zpc_ports where name=port_name limit 1) then
        select 'Port Already exist' as message, '315' as status_code;
else
INSERT INTO zpc_system.zpc_ports
		(name, address, created_at, updated_at) 
	VALUE
		(port_name, address, NOW(), NOW());
        select 'Port successfully inserted' as message, '300' as status_code;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insert_remove_password_policy` (IN `_action` TINYINT, IN `_name` VARCHAR(45))  BEGIN
# user wants to add password policy
	if exists(select id from zpc_system.password_policies where name=_name limit 1) then
		# password policy exists
        update zpc_system.password_policies set value=_action where name=_name;
		select 'Password policy updated successfully' as message, 300 as status_code;
	else
		# password policy does not exist
		select 'Password policy does not exist' as message, 315 as status_code;
	end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insert_remove_role_permission` (IN `_action` TINYINT, IN `_role_id` INT, IN `_permission_id` INT)  BEGIN
if(_action = 1) then
	# user wants to add role permission
	if exists(select internal_user_roles_id from zpc_system.internal_user_roles_has_role_permissions where internal_user_roles_id=_role_id and role_permissions_id=_permission_id limit 1) then
		# role permission exists
		select 'Role permission already exist' as message, 315 as status_code;
	else
		# role permission does not exist
        # check if role & permission exists
        if exists(select id from zpc_system.internal_user_roles where id=_role_id limit 1) then
			if exists(select id from zpc_system.role_permissions where id=_permission_id limit 1) then
				insert into zpc_system.internal_user_roles_has_role_permissions (internal_user_roles_id, role_permissions_id)
				values(_role_id, _permission_id);
				select 'Role permission successfully added' as message, 300 as status_code;
            else
				select 'Permission does not exist' as message, 315 as status_code;
            end if;
        else
			select 'Role does not exist' as message, 315 as status_code;
        end if;
	end if;
else
		# user wants to remove role permission
		if exists(select internal_user_roles_id from zpc_system.internal_user_roles_has_role_permissions where internal_user_roles_id=_role_id and role_permissions_id=_permission_id limit 1) then
			# role permission exists
			delete from zpc_system.internal_user_roles_has_role_permissions where internal_user_roles_id=_role_id and role_permissions_id=_permission_id;
			select 'Role permission successfully removed' as message, 300 as status_code;
		else
			# role permission does not exist
			select 'Role permission does not exist' as message, 315 as status_code;
	end if;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insert_remove_user_permission` (IN `_action` TINYINT, IN `_permission_id` INT, IN `_user_id` INT)  BEGIN
if(_action = 1) then
	# user wants to add permission
	if exists(select role_permissions_id from zpc_system.internal_users_has_role_permissions where internal_users_id=_user_id and role_permissions_id=_permission_id limit 1) then
		# role permission exists
		select 'Role permission already exist' as message, 315 as status_code;
	else
		# role permission does not exist
        # check if permission exists
        if exists(select id from zpc_system.role_permissions where id=_permission_id limit 1) then
				insert into zpc_system.internal_users_has_role_permissions (internal_users_id, internal_users_account_status_code, role_permissions_id)
				values(_user_id, 'AC001', _permission_id);
				select 'Permission successfully added' as message, 300 as status_code;
            else
				select 'Permission does not exist' as message, 315 as status_code;
            end if;
	end if;
else
		# user wants to remove role permission
		if exists(select role_permissions_id from zpc_system.internal_users_has_role_permissions where internal_users_id=_user_id and role_permissions_id=_permission_id limit 1) then
			# Permission exists
			delete from zpc_system.internal_users_has_role_permissions where internal_users_id=_user_id and role_permissions_id=_permission_id;
			select 'Role permission successfully removed' as message, 300 as status_code;
		else
			# role permission does not exist
			select 'Role permission does not exist' as message, 315 as status_code;
	end if;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insert_service` (IN `name` VARCHAR(45), IN `description` TEXT, IN `serviceCode` VARCHAR(20))  BEGIN

# check if service already exists
if exists(select service_code from zpc_system.services where service_code=serviceCode limit 1) then
select 'Service Already exists' as message, '315' as status_code;
else
INSERT INTO zpc_system.services
		(name, description, service_code, created_at, updated_at) 
	VALUE
		(`name`, `description`, `serviceCode`, NOW(), NOW());
select 'Service inserted successfully' as message, '300' as status_code;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insert_shipping_line` (IN `shippingLineName` VARCHAR(45), IN `shippingLineCode` VARCHAR(20))  BEGIN
if exists(select code from zpc_system.shipping_lines where code=shippingLineCode limit 1) then
select 'Shipping line already exist' as message, '315' as status_code;
else
insert into zpc_system.shipping_lines (name, code, created_at, updated_at) values(shippingLineName, shippingLineCode, NOW(), NOW());
select 'Shipping line inserted successfully' as message, '300' as status_code;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insert_vessel` (IN `vesselName` VARCHAR(45), IN `shipLineCode` VARCHAR(20))  BEGIN
# check if shipping line exist
declare shipId int;
select id into shipId from zpc_system.shipping_lines where code=shipLineCode limit 1;
if shipId is not null then
INSERT INTO zpc_system.vessels
		(name, shipping_lines_id, created_at, updated_at) 
	VALUE
		(vesselName, shipId, NOW(), NOW());
select 'Vessel successfully inserted!' as message, '300' as status_code;
else
select 'Shipping line does not exist' as message, '315' as status_code;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_search_id_types` (IN `_query` VARCHAR(45))  BEGIN
select id, name, description, service_status_code from zpc_system.id_types where name like CONCAT('%',_query,'%');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_search_permissions` (IN `_query` VARCHAR(45))  BEGIN
select id, name, display_name, description, service_status_code from zpc_system.role_permissions where display_name like CONCAT('%',_query,'%');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_search_roles` (IN `_query` VARCHAR(45))  BEGIN
select id, name, display_name, description, service_status_code from zpc_system.internal_user_roles where display_name like CONCAT('%',_query,'%');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_search_services` (IN `_query` VARCHAR(45))  BEGIN
select id, name, description, service_status_code, service_code from zpc_system.services where name like CONCAT('%',_query,'%');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_search_shipping_lines` (IN `_query` VARCHAR(45))  BEGIN
select id, name, code, service_status_code from zpc_system.shipping_lines where name like CONCAT('%',_query,'%');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_search_user` (IN `name` VARCHAR(45))  BEGIN
select id, first_name, last_name, username, email, phone_number, gender, id_types_id, id_number, account_status_code from zpc_system.internal_users where username like CONCAT('%',name,'%');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_staff_create_bill` (IN `_user_id` INT, IN `_service_code` VARCHAR(20), IN `_amount_usd` DOUBLE, IN `_exchange_rate` DOUBLE, IN `_cargo_id` INT, IN `_cargo_types_id` INT, IN `_bol_id` INT, IN `_voyages_id` INT, IN `_vessel_id` INT, IN `_app_status` INT, IN `_bill_status` INT)  BEGIN
# check if service already exists
declare serviceId int;
declare customerServiceAppId int;

DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        SHOW ERRORS LIMIT 1;
        ROLLBACK;
        select 'SQLException encountered' message, '515' status_code;
    END;

select id into serviceId from zpc_system.services where service_code=_service_code limit 1;
# Check if service exist
if serviceId is not null then
	# Check if user exists
    if exists(select id from zpc_system.internal_users where id=_user_id limit 1) then
	############ Start transaction ############
	START TRANSACTION;
	# 1. Insert into customer service app
	INSERT INTO zpc_system.customer_service_applications (services_id, external_users_id, user_type, application_status_id, created_at, updated_at) 
		VALUE (serviceId, _user_id, 'I', _app_status, NOW(), NOW());
        select LAST_INSERT_ID() into customerServiceAppId;
        if customerServiceAppId is not null then
			# 2. insert into customer app bill
            INSERT INTO zpc_system.customer_application_bills
			(amount_tzs, amount_usd, exchange_rate, customer_service_applications_id, customer_service_applications_services_id
            , customer_service_applications_external_users_id, user_type, bill_status_id, created_at, updated_at) 
			VALUE
			((_exchange_rate * _amount_usd), _amount_usd, _exchange_rate, customerServiceAppId, serviceId, _user_id, 'I', _bill_status, NOW(), NOW());
            
            insert into zpc_system.customer_cargo_selections 
            (cargo_id, cargo_cargo_types_id, cargo_bill_of_ladings_id, cargo_bill_of_ladings_voyages_id,cargo_bill_of_ladings_voyages_zpc_ports_id,
            cargo_bill_of_ladings_voyages_vessels_id,customer_service_applications_id,customer_service_applications_services_id,
            customer_service_applications_external_users_id,service_bill_formulars_id, user_type,created_at, updated_at)
            values(_cargo_id, _cargo_types_id, _bol_id, _voyages_id, 1, _vessel_id, customerServiceAppId, serviceId, _user_id, 1, 'I', NOW(), NOW());
			
            if (row_count() > 0) then
            ############ commit transaction
            COMMIT;
				SELECT 'Your bill is being processed' as message, '300' as status_code;
			else
				ROLLBACK;
				SELECT 'Could not insert bill' as message, '315' as status_code;
			end if;
		else
            ROLLBACK;
        end if;
    else
		select 'User does not exists' as message, '315' as status_code;
    end if;
else
	select 'Service does not exists' as message, '315' as status_code;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_application_status` (IN `appid` BIGINT, IN `appcode` VARCHAR(10))  BEGIN
	 UPDATE 
        customer_service_applications
    SET 
        customer_service_applications.application_status_code=appcode
    WHERE
        customer_service_applications.id = appid;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_cargo_selection` (IN `_cargo_number` VARCHAR(45), IN `_application_id` INT, IN `_service_code` VARCHAR(10), IN `_user_id` INT)  BEGIN
declare cargoId int;
declare cargoType varchar(45);
declare cargoBolId int;
declare cargoVoyagesId int;

select id, cargo_type, tbl_bill_of_ladings_id, tbl_bill_of_ladings_voyages_id
into cargoId, cargoType, cargoBolId, cargoVoyagesId from zpc_system.tbl_cargo where number=_cargo_number limit 1;

if cargoId is not null then
update zpc_system.tbl_cargo set is_selected=1 where number=_cargo_number;
	INSERT INTO zpc_system.customer_cargo_selections
			(cargo_id, cargo_cargo_type, cargo_tbl_bill_of_ladings_id, cargo_tbl_bill_of_ladings_tbl_voyages_id,
			customer_service_applications_id, customer_service_applications_services_code,
            customer_service_applications_external_users_id, service_bill_formulars_id,
			customer_service_applications_application_status_code, user_type, created_at, updated_at) 
		VALUE
			(cargoId, cargoType, cargoBolId, cargoVoyagesId, _application_id, _service_code, _user_id, 1, 'NC001', 'E', NOW(), NOW());
            if (row_count() > 0) then
				SELECT 'Cargo updated successfully' as message, '300' as status_code;
			else
				SELECT 'Could not update cargo selection' as message, '315' as status_code;
			end if;
else 
	select 'Cargo does not exist' as message, '315' as status_code;
end if;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_validate_manifest_input` (`cargoTypesId` INT, `containerTypesId` INT, `generalCargoTypesId` INT, `portId` INT) RETURNS VARCHAR(100) CHARSET utf8mb4 BEGIN
declare cargoTypeCode varchar(45);
	# Check if port exists
    if exists(select id from zpc_system.zpc_ports where id=portId) then
		# Check cargo type
		select code into cargoTypeCode from zpc_system.cargo_types where id=cargoTypesId limit 1;
		if cargoTypeCode is not null then
			# Check wether cargo is container or loose cargo
			if (cargoTypeCode = 'GC001') then
				# Cargo is general cargo
				# Check general cargo type
				if exists(select id from zpc_system.general_cargo_types where id=generalCargoTypesId limit 1) then
					RETURN null;
				else
					RETURN 'General Cargo type does not exist';
				end if;
		else 
			if (cargoTypeCode = 'CN001') then
				# cargo is container
				# Check if container type exist
				if exists(select id from zpc_system.container_types where id=containerTypesId limit 1) then
					RETURN null;
				else
					RETURN 'Container type does not exist';
				end if;
			else
				RETURN 'Cargo type cannot be processed';
			end if;
		end if;
		else
			RETURN 'Cargo type does not exist';
		end if;
    else
		RETURN 'Port does not exist';
    end if;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `account_status`
--

CREATE TABLE `account_status` (
  `id` int(11) NOT NULL,
  `name` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_status`
--

INSERT INTO `account_status` (`id`, `name`, `description`, `code`, `created_at`, `updated_at`) VALUES
(1, 'Active', 'This means that the account is active and working', 'AC001', NULL, NULL),
(2, 'Blocked', 'This means that the account hs been blocked and is not allowed to perform any action permanently', 'BL001', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `application_status`
--

CREATE TABLE `application_status` (
  `id` int(11) NOT NULL,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `application_status`
--

INSERT INTO `application_status` (`id`, `name`, `description`, `code`, `created_at`, `updated_at`) VALUES
(1, 'Cleared', NULL, 'CL001', NULL, NULL),
(2, 'Cancelled', NULL, 'CA001', NULL, NULL),
(4, 'Discharged', NULL, 'DS001', NULL, NULL),
(5, 'Not cleared', NULL, 'NC001', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `event_status` enum('SUCCESS','FAILED') COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_type` enum('Create','Delete','Edit','Block','Unblock','ViewItem','ViewList') COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `row_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `event_status`, `event_type`, `user_id`, `description`, `table_name`, `row_id`, `created_at`, `updated_at`) VALUES
(1, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-02-26 21:31:21', '2022-02-26 21:31:21'),
(2, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-02-26 21:39:53', '2022-02-26 21:39:53'),
(3, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-02-26 21:42:06', '2022-02-26 21:42:06'),
(4, 'SUCCESS', 'ViewList', 1, 'admin has viewed users list', NULL, NULL, '2022-02-26 22:23:16', '2022-02-26 22:23:16'),
(5, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-02-27 08:28:03', '2022-02-27 08:28:03'),
(6, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-02-28 08:46:57', '2022-02-28 08:46:57'),
(7, 'SUCCESS', 'ViewList', 1, 'admin has viewed users list', NULL, NULL, '2022-02-28 08:47:05', '2022-02-28 08:47:05'),
(8, 'SUCCESS', 'ViewList', 1, 'admin has viewed users list', NULL, NULL, '2022-02-28 08:47:21', '2022-02-28 08:47:21'),
(9, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-03-02 09:09:23', '2022-03-02 09:09:23'),
(10, 'SUCCESS', 'ViewList', 1, 'admin has viewed users list', NULL, NULL, '2022-03-02 09:09:32', '2022-03-02 09:09:32'),
(11, 'SUCCESS', 'ViewList', 1, 'admin has viewed users list', NULL, NULL, '2022-03-02 09:09:45', '2022-03-02 09:09:45'),
(12, 'SUCCESS', 'ViewList', 1, 'admin has viewed users list', NULL, NULL, '2022-03-02 09:10:23', '2022-03-02 09:10:23'),
(13, 'SUCCESS', 'ViewList', 1, 'admin has viewed users list', NULL, NULL, '2022-03-02 09:12:46', '2022-03-02 09:12:46'),
(14, 'SUCCESS', 'Edit', 1, 'admin has edited password policy', NULL, NULL, '2022-03-02 09:18:25', '2022-03-02 09:18:25'),
(15, 'SUCCESS', 'Edit', 1, 'admin has edited password policy', NULL, NULL, '2022-03-02 09:18:44', '2022-03-02 09:18:44'),
(16, 'SUCCESS', 'Edit', 1, 'admin has edited password policy', NULL, NULL, '2022-03-02 09:18:45', '2022-03-02 09:18:45'),
(17, 'SUCCESS', 'Edit', 1, 'admin has edited password policy', NULL, NULL, '2022-03-02 09:18:56', '2022-03-02 09:18:56'),
(18, 'SUCCESS', 'Edit', 1, 'admin has edited password policy', NULL, NULL, '2022-03-02 09:19:05', '2022-03-02 09:19:05'),
(19, 'SUCCESS', 'Edit', 1, 'admin has edited password policy', NULL, NULL, '2022-03-02 09:19:06', '2022-03-02 09:19:06'),
(20, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-03-02 09:19:30', '2022-03-02 09:19:30'),
(21, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-03-02 12:40:49', '2022-03-02 12:40:49'),
(22, 'SUCCESS', 'ViewList', 1, 'admin has viewed users list', NULL, NULL, '2022-03-02 12:40:52', '2022-03-02 12:40:52'),
(23, 'SUCCESS', 'ViewList', 1, 'admin has viewed users list', NULL, NULL, '2022-03-02 16:35:11', '2022-03-02 16:35:11'),
(24, 'SUCCESS', 'ViewList', 1, 'admin has viewed users list', NULL, NULL, '2022-03-02 16:35:54', '2022-03-02 16:35:54'),
(25, 'SUCCESS', 'ViewItem', 1, 'admin has viewed victorm profile', NULL, NULL, '2022-03-02 16:35:56', '2022-03-02 16:35:56'),
(26, 'SUCCESS', 'ViewItem', 1, 'admin has viewed victorm profile', NULL, NULL, '2022-03-02 16:36:59', '2022-03-02 16:36:59'),
(27, 'SUCCESS', 'ViewItem', 1, 'admin has viewed victorm profile', NULL, NULL, '2022-03-02 16:37:55', '2022-03-02 16:37:55'),
(28, 'SUCCESS', 'Edit', 1, 'admin has added or removed permission to user', NULL, NULL, '2022-03-02 16:37:58', '2022-03-02 16:37:58'),
(29, 'SUCCESS', 'ViewItem', 1, 'admin has viewed victorm profile', NULL, NULL, '2022-03-02 16:38:06', '2022-03-02 16:38:06'),
(30, 'SUCCESS', 'ViewItem', 1, 'admin has viewed victorm profile', NULL, NULL, '2022-03-02 16:38:24', '2022-03-02 16:38:24'),
(31, 'SUCCESS', 'Edit', 1, 'admin has added or removed permission to user', NULL, NULL, '2022-03-02 16:38:27', '2022-03-02 16:38:27'),
(32, 'SUCCESS', 'ViewItem', 1, 'admin has viewed victorm profile', NULL, NULL, '2022-03-02 16:39:36', '2022-03-02 16:39:36'),
(33, 'SUCCESS', 'Edit', 1, 'admin has added or removed permission to user', NULL, NULL, '2022-03-02 16:39:39', '2022-03-02 16:39:39'),
(34, 'SUCCESS', 'ViewItem', 1, 'admin has viewed victorm profile', NULL, NULL, '2022-03-02 16:40:04', '2022-03-02 16:40:04'),
(35, 'SUCCESS', 'Edit', 1, 'admin has added or removed permission to user', NULL, NULL, '2022-03-02 16:40:08', '2022-03-02 16:40:08'),
(36, 'SUCCESS', 'ViewItem', 1, 'admin has viewed victorm profile', NULL, NULL, '2022-03-02 16:40:28', '2022-03-02 16:40:28'),
(37, 'SUCCESS', 'ViewItem', 1, 'admin has viewed victorm profile', NULL, NULL, '2022-03-02 16:40:56', '2022-03-02 16:40:56'),
(38, 'SUCCESS', 'ViewItem', 1, 'admin has viewed victorm profile', NULL, NULL, '2022-03-02 16:40:58', '2022-03-02 16:40:58'),
(39, 'SUCCESS', 'Edit', 1, 'admin has added or removed permission to user', NULL, NULL, '2022-03-02 16:41:04', '2022-03-02 16:41:04'),
(40, 'SUCCESS', 'ViewList', 1, 'admin has viewed users list', NULL, NULL, '2022-03-02 16:41:48', '2022-03-02 16:41:48'),
(41, 'SUCCESS', 'ViewList', 1, 'admin has viewed users list', NULL, NULL, '2022-03-02 16:41:50', '2022-03-02 16:41:50'),
(42, 'SUCCESS', 'ViewItem', 1, 'admin has viewed victorm profile', NULL, NULL, '2022-03-02 16:41:52', '2022-03-02 16:41:52'),
(43, 'SUCCESS', 'Edit', 1, 'admin has added or removed permission to user', NULL, NULL, '2022-03-02 16:41:57', '2022-03-02 16:41:57'),
(44, 'SUCCESS', 'Edit', 1, 'admin has added or removed permission to user', NULL, NULL, '2022-03-02 16:42:04', '2022-03-02 16:42:04'),
(45, 'SUCCESS', 'ViewItem', 1, 'admin has viewed victorm profile', NULL, NULL, '2022-03-02 16:43:31', '2022-03-02 16:43:31'),
(46, 'SUCCESS', 'Edit', 1, 'admin has added or removed permission to user', NULL, NULL, '2022-03-02 16:43:35', '2022-03-02 16:43:35'),
(47, 'SUCCESS', 'Edit', 1, 'admin has added or removed permission to user', NULL, NULL, '2022-03-02 16:43:40', '2022-03-02 16:43:40'),
(48, 'SUCCESS', 'Edit', 1, 'admin has added or removed permission to user', NULL, NULL, '2022-03-02 16:43:44', '2022-03-02 16:43:44'),
(49, 'SUCCESS', 'Edit', 1, 'admin has added or removed permission to user', NULL, NULL, '2022-03-02 16:43:47', '2022-03-02 16:43:47'),
(50, 'SUCCESS', 'Edit', 1, 'admin has added or removed permission to user', NULL, NULL, '2022-03-02 16:43:48', '2022-03-02 16:43:48'),
(51, 'SUCCESS', 'ViewList', 1, 'admin has viewed users list', NULL, NULL, '2022-03-02 16:58:38', '2022-03-02 16:58:38'),
(52, 'SUCCESS', 'ViewList', 1, 'admin has viewed users list', NULL, NULL, '2022-03-02 17:26:38', '2022-03-02 17:26:38'),
(53, 'SUCCESS', 'ViewList', 1, 'admin has viewed users list', NULL, NULL, '2022-03-02 17:26:57', '2022-03-02 17:26:57'),
(54, 'SUCCESS', 'ViewItem', 1, 'admin has viewed victorm profile', NULL, NULL, '2022-03-02 17:28:17', '2022-03-02 17:28:17'),
(55, 'SUCCESS', 'ViewList', 1, 'admin has successful viewed Roles & Permission list', NULL, NULL, '2022-03-02 18:01:03', '2022-03-02 18:01:03'),
(56, 'SUCCESS', 'ViewItem', 1, 'admin has viewed role named: passenger terminal', NULL, NULL, '2022-03-02 18:01:41', '2022-03-02 18:01:41'),
(57, 'SUCCESS', 'ViewList', 1, 'admin has successful viewed Roles & Permission list', NULL, NULL, '2022-03-02 18:01:46', '2022-03-02 18:01:46'),
(58, 'SUCCESS', 'ViewList', 1, 'admin has successful viewed Roles & Permission list', NULL, NULL, '2022-03-02 18:06:08', '2022-03-02 18:06:08'),
(59, 'SUCCESS', 'ViewList', 1, 'admin has successful viewed Roles & Permission list', NULL, NULL, '2022-03-02 18:06:33', '2022-03-02 18:06:33'),
(60, 'SUCCESS', 'ViewList', 1, 'admin has successful viewed Roles & Permission list', NULL, NULL, '2022-03-02 18:07:04', '2022-03-02 18:07:04'),
(61, 'SUCCESS', 'ViewList', 1, 'admin has viewed users list', NULL, NULL, '2022-03-02 19:47:04', '2022-03-02 19:47:04'),
(62, 'SUCCESS', 'ViewItem', 1, 'Has viewed the dashboard', NULL, NULL, '2022-03-03 07:52:56', '2022-03-03 07:52:56'),
(63, 'SUCCESS', 'ViewList', 1, 'Has viewed users list', NULL, NULL, '2022-03-03 07:55:46', '2022-03-03 07:55:46'),
(64, 'SUCCESS', 'ViewList', 1, 'Has viewed users list', NULL, NULL, '2022-03-03 07:56:43', '2022-03-03 07:56:43'),
(65, 'SUCCESS', 'ViewList', 1, 'Has viewed users list', NULL, NULL, '2022-03-03 07:56:48', '2022-03-03 07:56:48'),
(66, 'SUCCESS', 'ViewList', 1, 'Has viewed users list', NULL, NULL, '2022-03-03 07:57:44', '2022-03-03 07:57:44'),
(67, 'SUCCESS', 'ViewList', 1, 'Has viewed users list', NULL, NULL, '2022-03-03 08:02:37', '2022-03-03 08:02:37'),
(68, 'SUCCESS', 'ViewList', 1, 'Has viewed users list', NULL, NULL, '2022-03-03 08:03:09', '2022-03-03 08:03:09'),
(69, 'SUCCESS', 'ViewList', 1, 'Has viewed users list', NULL, NULL, '2022-03-03 08:03:37', '2022-03-03 08:03:37'),
(70, 'SUCCESS', 'ViewList', 1, 'Has viewed users list', NULL, NULL, '2022-03-03 08:04:37', '2022-03-03 08:04:37'),
(71, 'SUCCESS', 'ViewList', 1, 'Has viewed users list', NULL, NULL, '2022-03-03 08:07:02', '2022-03-03 08:07:02'),
(72, 'SUCCESS', 'ViewList', 1, 'Has viewed users list', NULL, NULL, '2022-03-03 08:07:04', '2022-03-03 08:07:04'),
(73, 'SUCCESS', 'ViewList', 1, 'Has viewed users list', NULL, NULL, '2022-03-03 08:07:52', '2022-03-03 08:07:52'),
(74, 'SUCCESS', 'ViewList', 1, 'Has viewed users list', NULL, NULL, '2022-03-03 08:07:53', '2022-03-03 08:07:53'),
(75, 'SUCCESS', 'ViewItem', 1, 'Has viewed victorm profile', NULL, NULL, '2022-03-03 08:07:54', '2022-03-03 08:07:54'),
(76, 'SUCCESS', 'Edit', 1, 'Has added or removed permission to user', NULL, NULL, '2022-03-03 08:08:02', '2022-03-03 08:08:02'),
(77, 'SUCCESS', 'Edit', 1, 'Has added or removed permission to user', NULL, NULL, '2022-03-03 08:08:03', '2022-03-03 08:08:03'),
(78, 'SUCCESS', 'Edit', 1, 'Has added or removed permission to user', NULL, NULL, '2022-03-03 08:08:06', '2022-03-03 08:08:06'),
(79, 'SUCCESS', 'ViewList', 1, 'Has viewed users list', NULL, NULL, '2022-03-03 08:08:16', '2022-03-03 08:08:16'),
(80, 'SUCCESS', 'ViewList', 1, 'Has successful viewed Roles & Permission list', NULL, NULL, '2022-03-03 08:08:22', '2022-03-03 08:08:22'),
(81, 'SUCCESS', 'ViewItem', 1, 'Has viewed role named: Super Admin', NULL, NULL, '2022-03-03 08:08:31', '2022-03-03 08:08:31'),
(82, 'SUCCESS', 'ViewList', 1, 'Has successful viewed Roles & Permission list', NULL, NULL, '2022-03-03 08:08:36', '2022-03-03 08:08:36'),
(83, 'SUCCESS', 'Block', 1, 'Has successfully blocked a role named: passenger terminal', NULL, NULL, '2022-03-03 08:08:52', '2022-03-03 08:08:52'),
(84, 'SUCCESS', 'ViewList', 1, 'Has successful viewed Roles & Permission list', NULL, NULL, '2022-03-03 08:08:52', '2022-03-03 08:08:52'),
(85, 'SUCCESS', 'Block', 1, 'Has successfully blocked a role named: passenger terminal', NULL, NULL, '2022-03-03 08:09:01', '2022-03-03 08:09:01'),
(86, 'SUCCESS', 'ViewList', 1, 'Has successful viewed Roles & Permission list', NULL, NULL, '2022-03-03 08:09:01', '2022-03-03 08:09:01'),
(87, 'SUCCESS', 'ViewList', 1, 'Has viewed users list', NULL, NULL, '2022-03-03 08:10:34', '2022-03-03 08:10:34'),
(88, 'SUCCESS', 'ViewList', 1, 'Has successful viewed Roles & Permission list', NULL, NULL, '2022-03-03 08:14:02', '2022-03-03 08:14:02'),
(89, 'SUCCESS', 'ViewList', 1, 'Has viewed identification list', NULL, NULL, '2022-03-03 08:14:36', '2022-03-03 08:14:36'),
(90, 'SUCCESS', 'ViewList', 1, 'Has successful viewed Roles & Permission list', NULL, NULL, '2022-03-03 08:15:09', '2022-03-03 08:15:09'),
(91, 'SUCCESS', 'ViewList', 1, 'Has viewed users list', NULL, NULL, '2022-03-03 08:28:41', '2022-03-03 08:28:41'),
(92, 'SUCCESS', 'ViewList', 1, 'Has successful viewed Roles & Permission list', NULL, NULL, '2022-03-03 08:34:39', '2022-03-03 08:34:39'),
(93, 'SUCCESS', 'ViewList', 1, 'Has successful viewed Roles & Permission list', NULL, NULL, '2022-03-03 08:39:26', '2022-03-03 08:39:26'),
(94, 'SUCCESS', 'ViewList', 1, 'Has viewed identification list', NULL, NULL, '2022-03-03 08:39:28', '2022-03-03 08:39:28'),
(95, 'SUCCESS', 'Edit', 1, 'Has edited password policy named: has_special_characters', NULL, NULL, '2022-03-03 08:42:05', '2022-03-03 08:42:05'),
(96, 'SUCCESS', 'Edit', 1, 'Has edited password policy named: has_special_characters', NULL, NULL, '2022-03-03 08:42:21', '2022-03-03 08:42:21'),
(97, 'SUCCESS', 'ViewItem', 1, 'Has viewed the dashboard', NULL, NULL, '2022-03-03 08:43:02', '2022-03-03 08:43:02'),
(98, 'SUCCESS', 'ViewList', 1, 'Has viewed users list', NULL, NULL, '2022-03-03 08:45:11', '2022-03-03 08:45:11'),
(99, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-03-04 15:15:45', '2022-03-04 15:15:45'),
(100, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-03-04 16:11:08', '2022-03-04 16:11:08'),
(101, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-03-04 16:13:56', '2022-03-04 16:13:56'),
(102, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-03-04 16:19:35', '2022-03-04 16:19:35'),
(103, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-03-04 16:20:19', '2022-03-04 16:20:19'),
(104, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-03-04 16:21:23', '2022-03-04 16:21:23'),
(105, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-03-04 16:21:36', '2022-03-04 16:21:36'),
(106, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-03-04 16:59:42', '2022-03-04 16:59:42'),
(107, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-03-05 10:11:02', '2022-03-05 10:11:02'),
(108, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-03-05 10:39:15', '2022-03-05 10:39:15'),
(109, 'SUCCESS', 'ViewItem', 1, 'admin has viewed the dashboard', NULL, NULL, '2022-03-05 10:39:53', '2022-03-05 10:39:53');

-- --------------------------------------------------------

--
-- Table structure for table `bill_of_ladings`
--

CREATE TABLE `bill_of_ladings` (
  `id` bigint(20) NOT NULL,
  `number` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `consignee` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipper` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notify` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `port_of_lading` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voyages_id` bigint(20) NOT NULL,
  `voyages_zpc_ports_id` int(11) NOT NULL,
  `voyages_vessels_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bill_status`
--

CREATE TABLE `bill_status` (
  `id` int(11) NOT NULL,
  `name` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bill_status`
--

INSERT INTO `bill_status` (`id`, `name`, `description`, `code`, `created_at`, `updated_at`) VALUES
(1, 'Pending', NULL, 'PE001', NULL, NULL),
(2, 'Paid', NULL, 'PA001', NULL, NULL),
(3, 'Expired', NULL, 'EX001', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cargo`
--

CREATE TABLE `cargo` (
  `id` bigint(20) NOT NULL,
  `number` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'This can be container number or general cargo number depends on cargo type',
  `seal_number` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_kg` decimal(10,2) NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo_types_id` int(11) NOT NULL,
  `container_types_id` int(11) DEFAULT NULL,
  `container_sizes_id` int(11) DEFAULT NULL,
  `general_cargo_types_id` int(11) DEFAULT NULL,
  `bill_of_ladings_id` bigint(20) NOT NULL,
  `bill_of_ladings_voyages_id` bigint(20) NOT NULL,
  `bill_of_ladings_voyages_zpc_ports_id` int(11) NOT NULL,
  `bill_of_ladings_voyages_vessels_id` int(11) NOT NULL,
  `is_selected` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cargo_types`
--

CREATE TABLE `cargo_types` (
  `id` int(11) NOT NULL COMMENT 'This table identifies wether cargo is container or loose cargo',
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cargo_types`
--

INSERT INTO `cargo_types` (`id`, `name`, `description`, `code`, `created_at`, `updated_at`) VALUES
(1, 'General cargo', NULL, 'GC001', NULL, NULL),
(2, 'Container', NULL, 'CN001', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `container_sizes`
--

CREATE TABLE `container_sizes` (
  `id` int(11) NOT NULL COMMENT 'This tables carries wether container is 40, 20',
  `size` tinyint(4) NOT NULL,
  `description` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `container_sizes`
--

INSERT INTO `container_sizes` (`id`, `size`, `description`, `created_at`, `updated_at`) VALUES
(1, 20, NULL, NULL, NULL),
(2, 40, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `container_types`
--

CREATE TABLE `container_types` (
  `id` int(11) NOT NULL,
  `type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `container_types`
--

INSERT INTO `container_types` (`id`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, '40HC', NULL, NULL, NULL),
(2, '20DV', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_application_bills`
--

CREATE TABLE `customer_application_bills` (
  `id` int(11) NOT NULL,
  `amount_tzs` double NOT NULL,
  `amount_usd` double NOT NULL,
  `exchange_rate` double NOT NULL,
  `control_number` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiring_datetime` timestamp NULL DEFAULT NULL,
  `customer_service_applications_id` bigint(20) NOT NULL,
  `customer_service_applications_services_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_service_applications_external_users_id` int(11) NOT NULL,
  `bill_status_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `user_type` enum('I','E') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_application_bills_archive`
--

CREATE TABLE `customer_application_bills_archive` (
  `id` int(11) NOT NULL,
  `amount_tzs` double NOT NULL,
  `amount_usd` double NOT NULL,
  `exchange_rate` double NOT NULL,
  `control_number` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiring_datetime` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_service_applications_id` bigint(20) NOT NULL,
  `customer_service_applications_services_id` int(11) NOT NULL,
  `customer_service_applications_external_users_id` int(11) NOT NULL,
  `bill_status_id` int(11) NOT NULL DEFAULT 1,
  `user_type` enum('I','E') COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_application_bills_daily`
--

CREATE TABLE `customer_application_bills_daily` (
  `id` int(11) NOT NULL,
  `amount_tzs` double NOT NULL,
  `amount_usd` double NOT NULL,
  `exchange_rate` double NOT NULL,
  `expiring_datetime` timestamp NULL DEFAULT NULL,
  `services_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_application_bills_monthly`
--

CREATE TABLE `customer_application_bills_monthly` (
  `id` int(11) NOT NULL,
  `amount_tzs` double NOT NULL,
  `amount_usd` double NOT NULL,
  `exchange_rate` double NOT NULL,
  `expiring_datetime` timestamp NULL DEFAULT NULL,
  `services_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_application_bills_monthly`
--

INSERT INTO `customer_application_bills_monthly` (`id`, `amount_tzs`, `amount_usd`, `exchange_rate`, `expiring_datetime`, `services_id`, `created_at`, `updated_at`) VALUES
(3, 23000, 1000, 2300, NULL, 1, '2022-02-09 07:09:39', '2022-02-09 07:09:39');

-- --------------------------------------------------------

--
-- Table structure for table `customer_application_bills_weekly`
--

CREATE TABLE `customer_application_bills_weekly` (
  `id` int(11) NOT NULL,
  `amount_tzs` double NOT NULL,
  `amount_usd` double NOT NULL,
  `exchange_rate` double NOT NULL,
  `expiring_datetime` timestamp NULL DEFAULT NULL,
  `services_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_application_bills_yearly`
--

CREATE TABLE `customer_application_bills_yearly` (
  `id` int(11) NOT NULL,
  `amount_tzs` double NOT NULL,
  `amount_usd` double NOT NULL,
  `exchange_rate` double NOT NULL,
  `expiring_datetime` timestamp NULL DEFAULT NULL,
  `services_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_cargo_selections`
--

CREATE TABLE `customer_cargo_selections` (
  `id` int(11) NOT NULL,
  `cargo_id` bigint(20) NOT NULL,
  `cargo_cargo_type` enum('container','loose') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cargo_tbl_bill_of_ladings_id` bigint(20) NOT NULL,
  `cargo_tbl_bill_of_ladings_tbl_voyages_id` bigint(20) NOT NULL,
  `customer_service_applications_id` bigint(20) NOT NULL,
  `customer_service_applications_services_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_service_applications_external_users_id` int(11) NOT NULL,
  `service_bill_formulars_id` int(11) NOT NULL,
  `customer_service_applications_application_status_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` enum('I','E') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_service_applications`
--

CREATE TABLE `customer_service_applications` (
  `id` bigint(20) NOT NULL,
  `services_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `external_users_id` int(11) NOT NULL,
  `application_status_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` enum('I','E') COLLATE utf8mb4_unicode_ci NOT NULL,
  `payer_full_name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payer_phone_number` int(11) NOT NULL,
  `payer_email_address` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exchange_rate`
--

CREATE TABLE `exchange_rate` (
  `id` int(11) NOT NULL,
  `spot_buying` decimal(10,2) DEFAULT NULL,
  `mean` decimal(10,2) DEFAULT NULL,
  `spot_selling` decimal(10,2) DEFAULT NULL,
  `exchange_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exchange_rate`
--

INSERT INTO `exchange_rate` (`id`, `spot_buying`, `mean`, `spot_selling`, `exchange_date`, `created_at`, `updated_at`) VALUES
(1, '1230.00', '1230.00', '1500.00', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `external_users`
--

CREATE TABLE `external_users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('M','F') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `external_users`
--

INSERT INTO `external_users` (`id`, `first_name`, `last_name`, `username`, `password`, `phone_number`, `email`, `gender`, `created_at`, `updated_at`) VALUES
(1, 'customer', 'customer', 'customer', '$2y$10$UuJadY2f5pda0UYJpxSh4.FzAy1P7YwBZzDnJciNuf5uLVmsdajli', '0788362656', NULL, 'M', '2022-02-20 08:10:13', '2022-02-23 06:09:59');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `general_cargo_types`
--

CREATE TABLE `general_cargo_types` (
  `id` int(11) NOT NULL,
  `type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `general_cargo_types`
--

INSERT INTO `general_cargo_types` (`id`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Loose cargo', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `id_types`
--

CREATE TABLE `id_types` (
  `id` int(11) NOT NULL,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_status_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AC001',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `id_types`
--

INSERT INTO `id_types` (`id`, `name`, `description`, `service_status_code`, `created_at`, `updated_at`) VALUES
(1, 'NIDA', 'National Identification', 'AC001', NULL, '2022-02-18 11:34:00'),
(2, 'Driver License', 'Leseni ya udereva', 'AC001', '2021-11-16 07:26:50', '2022-02-18 11:33:56'),
(3, 'Birth Certificate', 'KItambulisho cha kuzaliwa', 'AC001', '2022-03-04 16:38:26', '2022-03-04 16:38:26');

-- --------------------------------------------------------

--
-- Table structure for table `internal_users`
--

CREATE TABLE `internal_users` (
  `id` bigint(20) NOT NULL,
  `first_name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` int(11) NOT NULL,
  `email` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_number` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_attempts` tinyint(1) NOT NULL DEFAULT 0,
  `account_status_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AC001',
  `id_types_id` int(11) NOT NULL,
  `gender` enum('M','F') COLLATE utf8mb4_unicode_ci NOT NULL,
  `theme` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT '#008b8b',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `internal_users`
--

INSERT INTO `internal_users` (`id`, `first_name`, `last_name`, `username`, `phone_number`, `email`, `password`, `id_number`, `login_attempts`, `account_status_code`, `id_types_id`, `gender`, `theme`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Admin', 'admin', 629409043, 'godsonmandla@gmail.com', '$2y$10$AQox5.YURAdmoObA4fTShe.u65ZPIoN3XLGluEwB6QikmL0O.q.NW', '3456', 0, 'AC001', 1, 'M', '#008b8b', NULL, NULL, '2022-03-05 10:10:47'),
(2, 'Victor', 'Massawe', 'victorm', 788463722, 'victor@gmail.com', '$2y$10$47KB8jCiGAQSqfI2Ftam7OPsplFdycgzYc/jYgcmuRFep/ueSVvea', '6453421', 0, 'AC001', 1, 'M', '#008b8b', NULL, '2022-03-02 16:35:50', '2022-03-02 16:35:50');

-- --------------------------------------------------------

--
-- Table structure for table `internal_users_has_internal_user_roles`
--

CREATE TABLE `internal_users_has_internal_user_roles` (
  `internal_users_id` bigint(20) NOT NULL,
  `internal_users_account_status_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `internal_user_roles_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `internal_users_has_internal_user_roles`
--

INSERT INTO `internal_users_has_internal_user_roles` (`internal_users_id`, `internal_users_account_status_code`, `internal_user_roles_id`, `created_at`, `updated_at`) VALUES
(1, 'AC001', 1, NULL, NULL),
(2, 'AC001', 1, '2022-03-02 16:35:50', '2022-03-02 16:35:50');

-- --------------------------------------------------------

--
-- Table structure for table `internal_users_has_role_permissions`
--

CREATE TABLE `internal_users_has_role_permissions` (
  `internal_users_id` bigint(20) NOT NULL,
  `internal_users_account_status_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_permissions_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `internal_users_has_role_permissions`
--

INSERT INTO `internal_users_has_role_permissions` (`internal_users_id`, `internal_users_account_status_code`, `role_permissions_id`) VALUES
(1, 'AC001', 1),
(1, 'AC001', 2),
(1, 'AC001', 3),
(1, 'AC001', 4),
(1, 'AC001', 5),
(1, 'AC001', 6),
(1, 'AC001', 7),
(1, 'AC001', 8),
(1, 'AC001', 9),
(1, 'AC001', 10),
(1, 'AC001', 11),
(1, 'AC001', 12),
(1, 'AC001', 13),
(1, 'AC001', 14),
(1, 'AC001', 15),
(1, 'AC001', 16),
(1, 'AC001', 17),
(1, 'AC001', 18),
(1, 'AC001', 19),
(1, 'AC001', 20),
(1, 'AC001', 21),
(1, 'AC001', 22),
(1, 'AC001', 23),
(2, 'AC001', 1),
(2, 'AC001', 2),
(2, 'AC001', 3),
(2, 'AC001', 4),
(2, 'AC001', 5),
(2, 'AC001', 6);

-- --------------------------------------------------------

--
-- Table structure for table `internal_user_roles`
--

CREATE TABLE `internal_user_roles` (
  `id` int(11) NOT NULL,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_status_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AC001',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `internal_user_roles`
--

INSERT INTO `internal_user_roles` (`id`, `name`, `display_name`, `description`, `service_status_code`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'Super Admin', 'This is the highest role', 'AC001', NULL, '2022-02-18 10:59:56'),
(2, 'admin', 'Admin', 'This is department role', 'AC001', NULL, '2022-02-18 11:08:25'),
(4, 'staff', 'Staff', 'This role if for normal staffs', 'AC001', '2021-11-16 06:53:16', '2022-02-18 10:39:56'),
(5, 'passenger_terminal', 'passenger terminal', 'passenger terminal', 'AC001', '2022-03-02 09:11:31', '2022-03-03 08:09:01');

-- --------------------------------------------------------

--
-- Table structure for table `internal_user_roles_has_role_permissions`
--

CREATE TABLE `internal_user_roles_has_role_permissions` (
  `internal_user_roles_id` int(11) NOT NULL,
  `role_permissions_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `internal_user_roles_has_role_permissions`
--

INSERT INTO `internal_user_roles_has_role_permissions` (`internal_user_roles_id`, `role_permissions_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(2, 1),
(2, 2),
(2, 3),
(5, 3),
(5, 5),
(5, 7),
(5, 8),
(5, 9),
(5, 14);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(23, 'default', '{\"uuid\":\"799ce17a-6a2b-48eb-94f5-df8550c40929\",\"displayName\":\"App\\\\Jobs\\\\SendOTPSMS\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendOTPSMS\",\"command\":\"O:19:\\\"App\\\\Jobs\\\\SendOTPSMS\\\":13:{s:25:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000code\\\";i:9735;s:31:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000first_name\\\";s:5:\\\"Admin\\\";s:33:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000phone_number\\\";i:788999999;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1645911031, 1645911031),
(24, 'default', '{\"uuid\":\"233f8fcd-3a54-4e85-8778-892e65b3c489\",\"displayName\":\"App\\\\Jobs\\\\SendOTPEmail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendOTPEmail\",\"command\":\"O:21:\\\"App\\\\Jobs\\\\SendOTPEmail\\\":13:{s:27:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000code\\\";i:9735;s:28:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000email\\\";s:22:\\\"eddytimtimer@gmail.com\\\";s:31:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000username\\\";s:5:\\\"admin\\\";s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1645911031, 1645911031),
(25, 'default', '{\"uuid\":\"38f15a29-a746-45a0-90e8-692563289459\",\"displayName\":\"App\\\\Jobs\\\\SendOTPSMS\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendOTPSMS\",\"command\":\"O:19:\\\"App\\\\Jobs\\\\SendOTPSMS\\\":13:{s:25:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000code\\\";i:6460;s:31:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000first_name\\\";s:5:\\\"Admin\\\";s:33:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000phone_number\\\";i:788999999;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1645950467, 1645950467),
(26, 'default', '{\"uuid\":\"348e4618-f6b0-44ae-953a-ac9cd6c28743\",\"displayName\":\"App\\\\Jobs\\\\SendOTPEmail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendOTPEmail\",\"command\":\"O:21:\\\"App\\\\Jobs\\\\SendOTPEmail\\\":13:{s:27:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000code\\\";i:6460;s:28:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000email\\\";s:22:\\\"eddytimtimer@gmail.com\\\";s:31:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000username\\\";s:5:\\\"admin\\\";s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1645950467, 1645950467),
(27, 'default', '{\"uuid\":\"8c8d1033-69e7-449f-8e39-8633a1b8e226\",\"displayName\":\"App\\\\Jobs\\\\SendOTPSMS\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendOTPSMS\",\"command\":\"O:19:\\\"App\\\\Jobs\\\\SendOTPSMS\\\":13:{s:25:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000code\\\";i:6225;s:31:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000first_name\\\";s:5:\\\"Admin\\\";s:33:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000phone_number\\\";i:788999999;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1646038003, 1646038003),
(28, 'default', '{\"uuid\":\"3b7277a3-c290-403d-9c0b-2b0497e75626\",\"displayName\":\"App\\\\Jobs\\\\SendOTPEmail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendOTPEmail\",\"command\":\"O:21:\\\"App\\\\Jobs\\\\SendOTPEmail\\\":13:{s:27:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000code\\\";i:6225;s:28:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000email\\\";s:22:\\\"eddytimtimer@gmail.com\\\";s:31:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000username\\\";s:5:\\\"admin\\\";s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1646038003, 1646038003),
(29, 'default', '{\"uuid\":\"e70e9feb-0808-4c79-8148-4c3e6139509f\",\"displayName\":\"App\\\\Jobs\\\\SendOTPSMS\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendOTPSMS\",\"command\":\"O:19:\\\"App\\\\Jobs\\\\SendOTPSMS\\\":13:{s:25:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000code\\\";i:3386;s:31:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000first_name\\\";s:5:\\\"Admin\\\";s:33:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000phone_number\\\";i:788999999;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1646212139, 1646212139),
(30, 'default', '{\"uuid\":\"aba2541b-195d-465e-a10b-da6af64ca693\",\"displayName\":\"App\\\\Jobs\\\\SendOTPEmail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendOTPEmail\",\"command\":\"O:21:\\\"App\\\\Jobs\\\\SendOTPEmail\\\":13:{s:27:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000code\\\";i:3386;s:28:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000email\\\";s:22:\\\"eddytimtimer@gmail.com\\\";s:31:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000username\\\";s:5:\\\"admin\\\";s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1646212139, 1646212139),
(31, 'default', '{\"uuid\":\"00f90697-3e39-4993-8272-c43cdb82f406\",\"displayName\":\"App\\\\Jobs\\\\SendOTPSMS\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendOTPSMS\",\"command\":\"O:19:\\\"App\\\\Jobs\\\\SendOTPSMS\\\":13:{s:25:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000code\\\";i:1123;s:31:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000first_name\\\";s:5:\\\"Admin\\\";s:33:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000phone_number\\\";i:788999999;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1646224836, 1646224836),
(32, 'default', '{\"uuid\":\"4a642b32-4be4-493f-95ef-5eee3553d575\",\"displayName\":\"App\\\\Jobs\\\\SendOTPEmail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendOTPEmail\",\"command\":\"O:21:\\\"App\\\\Jobs\\\\SendOTPEmail\\\":13:{s:27:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000code\\\";i:1123;s:28:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000email\\\";s:22:\\\"eddytimtimer@gmail.com\\\";s:31:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000username\\\";s:5:\\\"admin\\\";s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1646224836, 1646224836),
(33, 'registration_mails', '{\"uuid\":\"3ef62a6e-a31a-4ea2-9f75-10a3a6cf5d9f\",\"displayName\":\"App\\\\Jobs\\\\SendRegistrationEmail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendRegistrationEmail\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\SendRegistrationEmail\\\":14:{s:8:\\\"password\\\";s:8:\\\"fG4nq994\\\";s:9:\\\"full_name\\\";s:14:\\\"Victor Massawe\\\";s:8:\\\"username\\\";s:7:\\\"victorm\\\";s:5:\\\"email\\\";s:16:\\\"victor@gmail.com\\\";s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";s:18:\\\"registration_mails\\\";s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1646238950, 1646238950),
(34, 'default', '{\"uuid\":\"84dc8fa2-b418-4b26-a014-28d1066b86c8\",\"displayName\":\"App\\\\Jobs\\\\SendOTPSMS\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendOTPSMS\",\"command\":\"O:19:\\\"App\\\\Jobs\\\\SendOTPSMS\\\":13:{s:25:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000code\\\";i:3441;s:31:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000first_name\\\";s:5:\\\"Admin\\\";s:33:\\\"\\u0000App\\\\Jobs\\\\SendOTPSMS\\u0000phone_number\\\";i:788999999;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1646293961, 1646293961),
(35, 'default', '{\"uuid\":\"db74f8b0-5050-4814-ba79-27f80231d6da\",\"displayName\":\"App\\\\Jobs\\\\SendOTPEmail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendOTPEmail\",\"command\":\"O:21:\\\"App\\\\Jobs\\\\SendOTPEmail\\\":13:{s:27:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000code\\\";i:3441;s:28:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000email\\\";s:22:\\\"eddytimtimer@gmail.com\\\";s:31:\\\"\\u0000App\\\\Jobs\\\\SendOTPEmail\\u0000username\\\";s:5:\\\"admin\\\";s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1646293961, 1646293961);

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id` bigint(20) UNSIGNED NOT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `geoip_city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `geoip_country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `geoip_lon` double(8,2) NOT NULL,
  `geoip_lat` double(8,2) NOT NULL,
  `geoip_state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`created_at`, `updated_at`, `id`, `event`, `user_id`, `email`, `ip`, `geoip_city`, `geoip_country`, `geoip_lon`, `geoip_lat`, `geoip_state`) VALUES
('2022-02-19 14:14:22', '2022-02-19 14:14:22', 1, 'succeeded', 1, 'eddytimtimer@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-02-23 07:22:39', '2022-02-23 07:22:39', 2, 'succeeded', 1, 'eddytimtimer@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-02-23 09:20:49', '2022-02-23 09:20:49', 3, 'succeeded', 1, 'eddytimtimer@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-02-23 09:23:29', '2022-02-23 09:23:29', 4, 'succeeded', 1, 'eddytimtimer@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-02-24 07:53:58', '2022-02-24 07:53:58', 5, 'succeeded', 1, 'eddytimtimer@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-02-25 08:02:34', '2022-02-25 08:02:34', 6, 'succeeded', 1, 'eddytimtimer@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-02-25 08:49:54', '2022-02-25 08:49:54', 7, 'succeeded', 1, 'eddytimtimer@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-02-25 08:52:55', '2022-02-25 08:52:55', 8, 'succeeded', 1, 'eddytimtimer@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-02-26 21:30:31', '2022-02-26 21:30:31', 9, 'succeeded', 1, 'eddytimtimer@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-02-27 08:27:46', '2022-02-27 08:27:46', 10, 'succeeded', 1, 'eddytimtimer@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-02-28 08:46:43', '2022-02-28 08:46:43', 11, 'succeeded', 1, 'eddytimtimer@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-03-02 09:08:59', '2022-03-02 09:08:59', 12, 'succeeded', 1, 'eddytimtimer@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-03-02 12:40:36', '2022-03-02 12:40:36', 13, 'succeeded', 1, 'eddytimtimer@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-03-03 07:52:40', '2022-03-03 07:52:40', 14, 'succeeded', 1, 'eddytimtimer@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-03-04 14:46:43', '2022-03-04 14:46:43', 15, 'succeeded', 1, 'godsonmandla@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-03-04 14:47:27', '2022-03-04 14:47:27', 16, 'succeeded', 1, 'godsonmandla@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-03-04 15:14:35', '2022-03-04 15:14:35', 17, 'succeeded', 1, 'godsonmandla@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut'),
('2022-03-05 10:10:47', '2022-03-05 10:10:47', 18, 'succeeded', 1, 'godsonmandla@gmail.com', '127.0.0.1', 'New Haven', 'United States', -72.92, 41.31, 'Connecticut');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2022_01_20_135806_create_jobs_table', 1),
(6, '2022_01_31_132401_create_login_attempts_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `password_policies`
--

CREATE TABLE `password_policies` (
  `id` int(11) NOT NULL,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` tinyint(4) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_policies`
--

INSERT INTO `password_policies` (`id`, `name`, `display_name`, `value`, `created_at`, `updated_at`) VALUES
(1, 'has_alphabtes', 'Alphabets', 1, NULL, NULL),
(2, 'has_numeric', 'Numeric', 1, NULL, NULL),
(3, 'has_special_characters', 'Special characters', 0, NULL, NULL),
(4, 'length', 'Password length', 8, NULL, NULL),
(5, 'capital_letters', 'Capital letter', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `service_status_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AC001'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `name`, `display_name`, `description`, `created_at`, `updated_at`, `service_status_code`) VALUES
(1, 'view_dashboard', 'View Dashboard', 'Permission to view administrator dashboard', NULL, '2022-02-19 14:18:53', 'AC001'),
(2, 'view_user_list', 'View Users List', 'Permission to view list of users', NULL, '2022-02-18 12:46:56', 'IA001'),
(3, 'creating_users', 'Creating Users', 'Permission to create users', '2021-11-16 06:53:45', '2022-02-18 12:46:26', 'AC001'),
(4, 'view_user_profile', 'View User Profile', 'Permission to view user profile', '2022-02-18 10:13:12', '2022-02-18 12:48:00', 'IA001'),
(5, 'view_roles_list', 'View Roles List', 'Permission to view lis of roles', '2022-02-18 10:34:08', '2022-02-18 12:48:56', 'AC001'),
(6, 'view_permission_list', 'View Permission List', 'Permission to view list of permissions', '2022-02-18 10:35:41', '2022-02-18 12:49:37', 'AC001'),
(7, 'view_role_profile', 'View Role Profile', 'Permission to view role profile', '2022-02-18 12:50:38', '2022-02-18 12:50:38', 'AC001'),
(8, 'create_role', 'Create role', 'Permission to create a new role', '2022-02-18 12:51:32', '2022-02-18 12:51:32', 'AC001'),
(9, 'edit_user', 'Edit user', 'Permission to edit user profile', '2022-02-18 12:52:29', '2022-02-18 12:52:29', 'AC001'),
(10, 'edit_role', 'Edit Role', 'Permission to edit role', '2022-02-18 12:53:18', '2022-02-18 12:53:18', 'AC001'),
(11, 'edit_permission', 'Edit Permission', 'Permission to edit permission', '2022-02-18 12:53:37', '2022-02-18 12:53:37', 'AC001'),
(12, 'block_user', 'Block User', 'Permission to block user', '2022-02-18 12:54:02', '2022-02-18 12:54:02', 'AC001'),
(13, 'block_role', 'Block Role', 'Permission to block role', '2022-02-18 12:54:26', '2022-02-18 12:54:26', 'AC001'),
(14, 'block_permission', 'Block Permission', 'Permission to block permission', '2022-02-18 12:55:07', '2022-02-18 12:55:07', 'AC001'),
(15, 'view_identification_list', 'View Identification List', 'Permission to view list of identification', '2022-02-18 12:57:46', '2022-02-18 12:57:46', 'AC001'),
(16, 'edit_identification', 'Edit Identification', 'Permission to edit identification', '2022-02-18 12:58:39', '2022-02-18 12:58:39', 'AC001'),
(17, 'block_identification', 'Block Identification', 'Permission to block identification', '2022-02-18 12:59:16', '2022-02-18 12:59:16', 'AC001'),
(18, 'view_list_of_services', 'View List of services', 'Permission to view list of services', '2022-02-18 13:01:14', '2022-02-18 13:01:14', 'AC001'),
(19, 'edit_service', 'Edit service', 'Permission to edit service', '2022-02-18 13:01:50', '2022-02-18 13:01:50', 'AC001'),
(20, 'block_services', 'Block Services', 'Permission to block services', '2022-02-18 13:03:01', '2022-02-18 13:03:01', 'AC001'),
(21, 'create_identification', 'Create Identification', 'Permission to create new identification', '2022-02-23 09:24:11', '2022-02-23 09:24:11', 'AC001'),
(22, 'view_discharge_list', 'View Discharge list', 'Permission to view discharge list', '2022-02-23 09:24:11', '2022-02-23 09:24:11', 'AC001'),
(23, 'set_password_policy', 'Set Password Policy', 'Permission to set password policy', '2022-02-23 09:24:11', '2022-02-23 09:24:11', 'AC001');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_status_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2' COMMENT 'Service status is 2 by default indicating that service is initially inactive',
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `service_status_code`, `code`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'CLEARANCE', 'This service allows customer to clear cargo from storage', 'AC001', 'CL001', 'warehouse.svg', NULL, '2022-02-14 10:44:09'),
(3, 'DESTUFFING', 'This service allows customer to open the container', 'AC001', 'DS001', NULL, NULL, '2022-03-03 08:40:50'),
(4, 'FORK LIFT', 'This service allows customer lift their cargo from conatiner or container itself', 'AC001', 'FL001', 'forklift.svg', '2021-10-28 08:20:08', '2022-02-17 08:07:37'),
(13, 'DOCK', 'This service is for docking a ship', 'IA001', 'DO001', 'cargo.svg', '2021-11-17 06:00:26', '2022-02-14 10:50:31');

-- --------------------------------------------------------

--
-- Table structure for table `service_bill_formulars`
--

CREATE TABLE `service_bill_formulars` (
  `id` int(11) NOT NULL,
  `formular` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_bill_formulars`
--

INSERT INTO `service_bill_formulars` (`id`, `formular`, `description`, `service_code`, `created_at`, `updated_at`) VALUES
(1, 'size', 'Container size  only', 'FL001', NULL, NULL),
(2, 'size_x_day', 'Container size times number of days', 'CL001', NULL, NULL),
(3, 'size', 'Container size only', 'DS001', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_status`
--

CREATE TABLE `service_status` (
  `id` int(11) NOT NULL,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_status`
--

INSERT INTO `service_status` (`id`, `name`, `description`, `code`, `created_at`, `updated_at`) VALUES
(1, 'Active', 'This means service is App and running', 'AC001', NULL, NULL),
(2, 'InActive', 'This means service is temporarily unavailable', 'IA001', NULL, NULL),
(3, 'Bloacked', 'This means service is not available until further notice', 'BL001', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_lines`
--

CREATE TABLE `shipping_lines` (
  `id` int(11) NOT NULL COMMENT 'This is a table that carries all ships (shipping line companies) which can have many vessels',
  `name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'This is a unique code that identifies a shipping line and is generated after receiving a shipping line name from agent (SHIPCO)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `service_status_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `storage_pardon_applications`
--

CREATE TABLE `storage_pardon_applications` (
  `id` int(11) NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_application_bills_id` int(11) NOT NULL,
  `customer_service_applications_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_service_applications_services_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` enum('I','E') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bill_of_ladings`
--

CREATE TABLE `tbl_bill_of_ladings` (
  `id` bigint(20) NOT NULL,
  `number` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `consignee` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notify` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `port_of_lading` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tbl_voyages_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_bill_of_ladings`
--

INSERT INTO `tbl_bill_of_ladings` (`id`, `number`, `consignee`, `notify`, `port_of_lading`, `tbl_voyages_id`, `created_at`, `updated_at`) VALUES
(1, 'MEDUBA643148', 'SAYD FUAD', 'FUAD SHIPPING', 'DEHAM', 1, '2022-02-28 17:42:26', '2022-02-28 17:42:26'),
(2, 'MEDUIK211494', 'SALIM MOHAMMED', 'SALIM MOHAMMED', 'DEHAM', 1, '2022-02-28 17:42:26', '2022-02-28 17:42:26');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cargo`
--

CREATE TABLE `tbl_cargo` (
  `id` bigint(20) NOT NULL,
  `number` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'This can be container number or general cargo number depends on cargo type',
  `weight_kg` decimal(10,2) NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo_type` enum('CONTAINER','LOOSE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `container_type` enum('20DV','40HC','0') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `container_size` enum('20','40','0') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tbl_bill_of_ladings_id` bigint(20) NOT NULL,
  `tbl_bill_of_ladings_voyages_id` bigint(20) NOT NULL,
  `is_selected` tinyint(1) NOT NULL DEFAULT 0,
  `is_electric` tinyint(1) DEFAULT 0,
  `content` enum('FULL','EMPTY') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_cargo`
--

INSERT INTO `tbl_cargo` (`id`, `number`, `weight_kg`, `remarks`, `cargo_type`, `container_type`, `container_size`, `tbl_bill_of_ladings_id`, `tbl_bill_of_ladings_voyages_id`, `is_selected`, `is_electric`, `content`, `created_at`, `updated_at`) VALUES
(1, 'SEGU2786311', '27830.00', 'MAIZE (CORN)- OTHER', 'CONTAINER', '40HC', '40', 1, 1, 0, 0, 'FULL', '2022-02-28 17:42:26', '2022-02-28 17:42:26'),
(2, 'SEGU2786312', '27830.00', NULL, 'LOOSE', NULL, '0', 1, 1, 0, 0, NULL, '2022-02-28 17:42:26', '2022-02-28 17:42:26'),
(3, 'MEDU4990220', '27830.00', 'MEAT', 'CONTAINER', '20DV', '20', 2, 1, 0, 1, 'FULL', '2022-02-28 17:42:26', '2022-02-28 17:42:26'),
(4, 'SEGU2786322', '27830.00', NULL, 'LOOSE', NULL, '0', 2, 1, 0, 0, NULL, '2022-02-28 17:42:26', '2022-02-28 17:42:26');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_voyages`
--

CREATE TABLE `tbl_voyages` (
  `id` bigint(20) NOT NULL COMMENT 'This voyage tables indicates a journey that belongs to a vessel',
  `number` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimated_arrival_date` date NOT NULL,
  `arrival_date` date DEFAULT NULL,
  `departure_date` date DEFAULT NULL,
  `vessels_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vessel_code` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_line` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_voyages`
--

INSERT INTO `tbl_voyages` (`id`, `number`, `estimated_arrival_date`, `arrival_date`, `departure_date`, `vessels_name`, `vessel_code`, `shipping_line`, `created_at`, `updated_at`) VALUES
(1, 'VP-230', '2022-03-01', '2022-03-01', '2022-03-21', 'name of vessel', '00123', 'MAERSK', '2022-02-28 17:42:26', '2022-02-28 17:42:26');

-- --------------------------------------------------------

--
-- Table structure for table `user_otp_codes`
--

CREATE TABLE `user_otp_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` enum('E','I') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_otp_codes`
--

INSERT INTO `user_otp_codes` (`id`, `user_id`, `code`, `user_type`, `created_at`, `updated_at`) VALUES
(1, 1, '6816', 'I', '2022-02-19 14:14:22', '2022-03-05 10:10:47'),
(2, 1, '6072', 'E', '2022-02-22 07:41:39', '2022-02-23 06:09:59');

-- --------------------------------------------------------

--
-- Table structure for table `vessels`
--

CREATE TABLE `vessels` (
  `id` int(11) NOT NULL COMMENT 'This is a vessel table which belongs to the shipping line company and have many voyages',
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `shipping_lines_id` int(11) NOT NULL,
  `service_status_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voyages`
--

CREATE TABLE `voyages` (
  `id` bigint(20) NOT NULL COMMENT 'This voyage tables indicates a journey that belongs to a vessel where by a vessel also belongs to a shipping line',
  `number` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimated_arrival_date` date NOT NULL,
  `arrival_date` date DEFAULT NULL,
  `departure_date` date DEFAULT NULL,
  `zpc_ports_id` int(11) NOT NULL,
  `vessels_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_status`
--
ALTER TABLE `account_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`);

--
-- Indexes for table `application_status`
--
ALTER TABLE `application_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bill_of_ladings`
--
ALTER TABLE `bill_of_ladings`
  ADD PRIMARY KEY (`id`,`voyages_id`,`voyages_zpc_ports_id`,`voyages_vessels_id`),
  ADD KEY `fk_bill_of_ladings_voyages1_idx` (`voyages_id`,`voyages_zpc_ports_id`,`voyages_vessels_id`);

--
-- Indexes for table `bill_status`
--
ALTER TABLE `bill_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`);

--
-- Indexes for table `cargo`
--
ALTER TABLE `cargo`
  ADD PRIMARY KEY (`id`,`cargo_types_id`,`bill_of_ladings_id`,`bill_of_ladings_voyages_id`,`bill_of_ladings_voyages_zpc_ports_id`,`bill_of_ladings_voyages_vessels_id`),
  ADD KEY `fk_cargo_cargo_types1_idx` (`cargo_types_id`),
  ADD KEY `fk_cargo_container_types1_idx` (`container_types_id`),
  ADD KEY `fk_cargo_container_sizes1_idx` (`container_sizes_id`),
  ADD KEY `fk_cargo_general_cargo_types1_idx` (`general_cargo_types_id`),
  ADD KEY `fk_cargo_bill_of_ladings1_idx` (`bill_of_ladings_id`,`bill_of_ladings_voyages_id`,`bill_of_ladings_voyages_zpc_ports_id`,`bill_of_ladings_voyages_vessels_id`);

--
-- Indexes for table `cargo_types`
--
ALTER TABLE `cargo_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_UNIQUE` (`code`);

--
-- Indexes for table `container_sizes`
--
ALTER TABLE `container_sizes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `container_types`
--
ALTER TABLE `container_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_application_bills`
--
ALTER TABLE `customer_application_bills`
  ADD PRIMARY KEY (`id`,`customer_service_applications_id`,`customer_service_applications_services_code`,`customer_service_applications_external_users_id`,`bill_status_code`),
  ADD KEY `fk_customer_application_bills_customer_service_applications_idx` (`customer_service_applications_id`,`customer_service_applications_services_code`,`customer_service_applications_external_users_id`),
  ADD KEY `fk_customer_application_bills_bill_status1_idx` (`bill_status_code`);

--
-- Indexes for table `customer_application_bills_archive`
--
ALTER TABLE `customer_application_bills_archive`
  ADD PRIMARY KEY (`id`,`customer_service_applications_id`,`customer_service_applications_services_id`,`customer_service_applications_external_users_id`,`bill_status_id`),
  ADD KEY `fk_customer_app_bills_archive_customer_service_applications_idx` (`customer_service_applications_id`,`customer_service_applications_services_id`,`customer_service_applications_external_users_id`),
  ADD KEY `fk_customer_app_bills_archive_bill_status1_idx` (`bill_status_id`);

--
-- Indexes for table `customer_application_bills_daily`
--
ALTER TABLE `customer_application_bills_daily`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_application_bills_monthly`
--
ALTER TABLE `customer_application_bills_monthly`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_application_bills_weekly`
--
ALTER TABLE `customer_application_bills_weekly`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_application_bills_yearly`
--
ALTER TABLE `customer_application_bills_yearly`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_cargo_selections`
--
ALTER TABLE `customer_cargo_selections`
  ADD PRIMARY KEY (`id`,`customer_service_applications_id`,`customer_service_applications_services_code`,`customer_service_applications_external_users_id`,`service_bill_formulars_id`),
  ADD KEY `fk_cargo_selection_customer_service_applications1_idx` (`customer_service_applications_id`,`customer_service_applications_services_code`,`customer_service_applications_external_users_id`),
  ADD KEY `fk_cargo_selection_service_bill_formulars1_idx` (`service_bill_formulars_id`),
  ADD KEY `fk_cargo_selection_cargo1_idx` (`cargo_id`,`cargo_cargo_type`,`cargo_tbl_bill_of_ladings_id`,`cargo_tbl_bill_of_ladings_tbl_voyages_id`);

--
-- Indexes for table `customer_service_applications`
--
ALTER TABLE `customer_service_applications`
  ADD PRIMARY KEY (`id`,`services_code`,`external_users_id`),
  ADD KEY `fk_customer_service_applications_services1_idx` (`services_code`),
  ADD KEY `fk_customer_service_applications_external_users1_idx` (`external_users_id`);

--
-- Indexes for table `exchange_rate`
--
ALTER TABLE `exchange_rate`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `name_UNIQUE` (`spot_buying`) USING BTREE;

--
-- Indexes for table `external_users`
--
ALTER TABLE `external_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `general_cargo_types`
--
ALTER TABLE `general_cargo_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `id_types`
--
ALTER TABLE `id_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`);

--
-- Indexes for table `internal_users`
--
ALTER TABLE `internal_users`
  ADD PRIMARY KEY (`id`,`account_status_code`),
  ADD UNIQUE KEY `phone_number_UNIQUE` (`phone_number`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`),
  ADD UNIQUE KEY `id_number_UNIQUE` (`id_number`),
  ADD KEY `fk_internal_users_account_status1_idx` (`account_status_code`),
  ADD KEY `fk_internal_users_id_types1_idx` (`id_types_id`);

--
-- Indexes for table `internal_users_has_internal_user_roles`
--
ALTER TABLE `internal_users_has_internal_user_roles`
  ADD PRIMARY KEY (`internal_users_id`,`internal_users_account_status_code`,`internal_user_roles_id`),
  ADD KEY `fk_internal_users_has_internal_user_roles_internal_user_rol_idx` (`internal_user_roles_id`),
  ADD KEY `fk_internal_users_has_internal_user_roles_internal_users1_idx` (`internal_users_id`,`internal_users_account_status_code`);

--
-- Indexes for table `internal_users_has_role_permissions`
--
ALTER TABLE `internal_users_has_role_permissions`
  ADD PRIMARY KEY (`internal_users_id`,`internal_users_account_status_code`,`role_permissions_id`),
  ADD KEY `fk_internal_users_has_role_permissions_role_permissions1_idx` (`role_permissions_id`),
  ADD KEY `fk_internal_users_has_role_permissions_internal_users1_idx` (`internal_users_id`,`internal_users_account_status_code`);

--
-- Indexes for table `internal_user_roles`
--
ALTER TABLE `internal_user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`);

--
-- Indexes for table `internal_user_roles_has_role_permissions`
--
ALTER TABLE `internal_user_roles_has_role_permissions`
  ADD PRIMARY KEY (`internal_user_roles_id`,`role_permissions_id`),
  ADD KEY `fk_internal_user_roles_has_role_permissions_role_permission_idx` (`role_permissions_id`),
  ADD KEY `fk_internal_user_roles_has_role_permissions_internal_user_r_idx` (`internal_user_roles_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login_attempts_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_policies`
--
ALTER TABLE `password_policies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`,`service_status_code`),
  ADD UNIQUE KEY `service_code_UNIQUE` (`code`),
  ADD KEY `fk_services_service_status1_idx` (`service_status_code`);

--
-- Indexes for table `service_bill_formulars`
--
ALTER TABLE `service_bill_formulars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_status`
--
ALTER TABLE `service_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_UNIQUE` (`code`);

--
-- Indexes for table `shipping_lines`
--
ALTER TABLE `shipping_lines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_UNIQUE` (`code`);

--
-- Indexes for table `storage_pardon_applications`
--
ALTER TABLE `storage_pardon_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_bill_of_ladings`
--
ALTER TABLE `tbl_bill_of_ladings`
  ADD PRIMARY KEY (`id`,`tbl_voyages_id`),
  ADD KEY `fk_tbl_bill_of_ladings_voyages1_idx` (`tbl_voyages_id`);

--
-- Indexes for table `tbl_cargo`
--
ALTER TABLE `tbl_cargo`
  ADD PRIMARY KEY (`id`,`tbl_bill_of_ladings_id`,`tbl_bill_of_ladings_voyages_id`),
  ADD KEY `fk_cargo_tbl_bill_of_ladings1_idx` (`tbl_bill_of_ladings_id`,`tbl_bill_of_ladings_voyages_id`);

--
-- Indexes for table `tbl_voyages`
--
ALTER TABLE `tbl_voyages`
  ADD PRIMARY KEY (`id`,`number`);

--
-- Indexes for table `user_otp_codes`
--
ALTER TABLE `user_otp_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vessels`
--
ALTER TABLE `vessels`
  ADD PRIMARY KEY (`id`,`shipping_lines_id`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`),
  ADD KEY `fk_vessels_shipping_lines1_idx` (`shipping_lines_id`);

--
-- Indexes for table `voyages`
--
ALTER TABLE `voyages`
  ADD PRIMARY KEY (`id`,`zpc_ports_id`,`vessels_id`),
  ADD KEY `fk_voyages_zpc_ports1_idx` (`zpc_ports_id`),
  ADD KEY `fk_voyages_vessels1_idx` (`vessels_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_status`
--
ALTER TABLE `account_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `application_status`
--
ALTER TABLE `application_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `bill_of_ladings`
--
ALTER TABLE `bill_of_ladings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bill_status`
--
ALTER TABLE `bill_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cargo`
--
ALTER TABLE `cargo`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cargo_types`
--
ALTER TABLE `cargo_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'This table identifies wether cargo is container or loose cargo', AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `container_sizes`
--
ALTER TABLE `container_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'This tables carries wether container is 40, 20', AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `container_types`
--
ALTER TABLE `container_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customer_application_bills`
--
ALTER TABLE `customer_application_bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_application_bills_archive`
--
ALTER TABLE `customer_application_bills_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_application_bills_daily`
--
ALTER TABLE `customer_application_bills_daily`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_application_bills_monthly`
--
ALTER TABLE `customer_application_bills_monthly`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer_application_bills_weekly`
--
ALTER TABLE `customer_application_bills_weekly`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customer_application_bills_yearly`
--
ALTER TABLE `customer_application_bills_yearly`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customer_cargo_selections`
--
ALTER TABLE `customer_cargo_selections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_service_applications`
--
ALTER TABLE `customer_service_applications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exchange_rate`
--
ALTER TABLE `exchange_rate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `external_users`
--
ALTER TABLE `external_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `general_cargo_types`
--
ALTER TABLE `general_cargo_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `id_types`
--
ALTER TABLE `id_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `internal_users`
--
ALTER TABLE `internal_users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `internal_user_roles`
--
ALTER TABLE `internal_user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `password_policies`
--
ALTER TABLE `password_policies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `service_bill_formulars`
--
ALTER TABLE `service_bill_formulars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `service_status`
--
ALTER TABLE `service_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shipping_lines`
--
ALTER TABLE `shipping_lines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'This is a table that carries all ships (shipping line companies) which can have many vessels';

--
-- AUTO_INCREMENT for table `storage_pardon_applications`
--
ALTER TABLE `storage_pardon_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_bill_of_ladings`
--
ALTER TABLE `tbl_bill_of_ladings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_cargo`
--
ALTER TABLE `tbl_cargo`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_voyages`
--
ALTER TABLE `tbl_voyages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'This voyage tables indicates a journey that belongs to a vessel', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_otp_codes`
--
ALTER TABLE `user_otp_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vessels`
--
ALTER TABLE `vessels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'This is a vessel table which belongs to the shipping line company and have many voyages';

--
-- AUTO_INCREMENT for table `voyages`
--
ALTER TABLE `voyages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'This voyage tables indicates a journey that belongs to a vessel where by a vessel also belongs to a shipping line';

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cargo`
--
ALTER TABLE `cargo`
  ADD CONSTRAINT `fk_cargo_cargo_types1` FOREIGN KEY (`cargo_types_id`) REFERENCES `cargo_types` (`id`),
  ADD CONSTRAINT `fk_cargo_container_sizes1` FOREIGN KEY (`container_sizes_id`) REFERENCES `container_sizes` (`id`),
  ADD CONSTRAINT `fk_cargo_container_types1` FOREIGN KEY (`container_types_id`) REFERENCES `container_types` (`id`);

--
-- Constraints for table `customer_application_bills_archive`
--
ALTER TABLE `customer_application_bills_archive`
  ADD CONSTRAINT `fk_customer_app_bills_archive_bill_status1` FOREIGN KEY (`bill_status_id`) REFERENCES `bill_status` (`id`);

--
-- Constraints for table `internal_users`
--
ALTER TABLE `internal_users`
  ADD CONSTRAINT `fk_internal_users_id_types1` FOREIGN KEY (`id_types_id`) REFERENCES `id_types` (`id`);

--
-- Constraints for table `internal_users_has_internal_user_roles`
--
ALTER TABLE `internal_users_has_internal_user_roles`
  ADD CONSTRAINT `fk_internal_users_has_internal_user_roles_internal_user_roles1` FOREIGN KEY (`internal_user_roles_id`) REFERENCES `internal_user_roles` (`id`);

--
-- Constraints for table `internal_users_has_role_permissions`
--
ALTER TABLE `internal_users_has_role_permissions`
  ADD CONSTRAINT `fk_internal_users_has_role_permissions_role_permissions1` FOREIGN KEY (`role_permissions_id`) REFERENCES `role_permissions` (`id`);

--
-- Constraints for table `internal_user_roles_has_role_permissions`
--
ALTER TABLE `internal_user_roles_has_role_permissions`
  ADD CONSTRAINT `fk_internal_user_roles_has_role_permissions_internal_user_rol1` FOREIGN KEY (`internal_user_roles_id`) REFERENCES `internal_user_roles` (`id`),
  ADD CONSTRAINT `fk_internal_user_roles_has_role_permissions_role_permissions1` FOREIGN KEY (`role_permissions_id`) REFERENCES `role_permissions` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
