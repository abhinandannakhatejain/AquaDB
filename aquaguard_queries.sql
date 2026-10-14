-- AquaGuard DBMS lab query documentation
-- Team: AquaDB
-- Tested by: Ashad_24BCE0431
-- Database: aquaguard (MariaDB/phpMyAdmin)


USE aquaguard;

-- 1. CRUD OPERATIONS
INSERT INTO locations (area_name, city, latitude, longitude)
VALUES ('Sample Area', 'Vellore', 12.9500000, 79.1500000);

SELECT * FROM locations;

UPDATE complaints
SET status = 'IN_PROGRESS'
WHERE complaint_id = 2;

DELETE FROM locations
WHERE area_name = 'Sample Area';


-- 2. COMPLAINT DETAILS JOIN
SELECT c.complaint_id, c.description, u.name AS citizen,
       cc.category_name, l.area_name, c.status,
       'Ashad_24BCE0431' AS submitted_by
FROM complaints c
JOIN users u ON c.user_id = u.user_id
JOIN complaint_categories cc ON c.category_id = cc.category_id
JOIN locations l ON c.location_id = l.location_id;


-- 3. OFFICER ASSIGNMENT JOIN
SELECT c.complaint_id, c.description, ou.name AS officer,
       ca.assigned_at, 'Ashad_24BCE0431' AS checked_by
FROM complaint_assignments ca
JOIN complaints c ON ca.complaint_id = c.complaint_id
JOIN officers o ON ca.officer_id = o.officer_id
JOIN users ou ON o.user_id = ou.user_id;


-- 4. AREA-WISE AGGREGATE QUERY
SELECT l.area_name,
       COUNT(*) AS ashad_24BCE0431_total_complaints,
       SUM(c.status = 'RESOLVED') AS resolved,
       AVG(TIMESTAMPDIFF(MINUTE, c.created_at, c.resolved_at))
           AS avg_minutes_to_resolve
FROM complaints c
JOIN locations l ON c.location_id = l.location_id
GROUP BY l.area_name
HAVING COUNT(*) >= 1;


-- 5. VIEW QUERIES
SELECT vw.*, 'Ashad_24BCE0431' AS checked_by
FROM vw_water_dashboard vw
WHERE risk_level = 'CRITICAL';

SELECT vw.*, 'Ashad_24BCE0431' AS checked_by
FROM vw_complaint_dashboard vw
WHERE status = 'PENDING';


-- 6. TRIGGER DEMONSTRATION
SELECT location_id, area_name, city
FROM locations
WHERE location_id = 3;


INSERT INTO water_quality_readings
    (location_id, ph, turbidity, tds, temperature)
VALUES (3, 5.5, 12, 1200, 29);

SELECT reading_id, location_id, ph, turbidity, tds, temperature, risk_level,
       'Ashad_24BCE0431' AS tested_by
FROM water_quality_readings
ORDER BY reading_id DESC
LIMIT 1;

SELECT alert_id, location_id, reading_id, severity, reason, is_resolved,
       'Ashad_24BCE0431' AS tested_by
FROM water_alerts
ORDER BY reading_id DESC
LIMIT 1;


-- 7. TRANSACTIONS
-- COMMIT demo: only run if this assignment is not already present.
START TRANSACTION;
INSERT INTO complaint_assignments (complaint_id, officer_id)
VALUES (2, 1);
UPDATE complaints SET status = 'ASSIGNED' WHERE complaint_id = 2;
COMMIT;

-- ROLLBACK demo
START TRANSACTION;
UPDATE complaints SET status = 'REJECTED' WHERE complaint_id = 3;
ROLLBACK;

SELECT complaint_id, status, 'Ashad_24BCE0431' AS tested_by
FROM complaints
WHERE complaint_id = 3;


-- 8. EXPLAIN
EXPLAIN
SELECT complaint_id, status, 'Ashad_24BCE0431' AS checked_by
FROM complaints
WHERE status = 'PENDING';


-- 9. STORED PROCEDURE
-- Created in phpMyAdmin with delimiter $$.

DELIMITER $$
CREATE PROCEDURE sp_ashad_24BCE0431_assign_complaint(
    IN p_complaint_id INT,
    IN p_officer_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    START TRANSACTION;
    INSERT INTO complaint_assignments (complaint_id, officer_id)
    VALUES (p_complaint_id, p_officer_id);
    UPDATE complaints SET status = 'ASSIGNED'
    WHERE complaint_id = p_complaint_id;
    COMMIT;
END$$
DELIMITER ;

-- Verification queries (safe to run)
SELECT complaint_id, status
FROM complaints
WHERE complaint_id = 3;

SELECT *
FROM complaint_assignments
WHERE complaint_id = 3 AND officer_id = 1;
