# docs/DATABASE_TESTING.md

```markdown
# Database Testing Documentation — AquaGuard

**Project Title:** AquaGuard — Water Quality Monitoring & Complaint Management System  
**Team Name:** AquaDB  
**Tested By:** Ashad Rizwan (24BCE0431)  
**Database:** `aquaguard` (MariaDB / phpMyAdmin)  

---

## Overview

This document outlines the systematic database testing performed for the **AquaGuard** project. It covers 10 primary database exercises, detailing the testing objectives, executed SQL statements, and verification based on phpMyAdmin execution results.

---

## Test Cases & Execution Results

### 1. CRUD Operations
* **Testing Objective:** Verify that basic Create, Read, Update, and Delete operations execute successfully on core database tables.
* **SQL Executed:**
  ```sql
  INSERT INTO locations (area_name, city, latitude, longitude)
  VALUES ('Sample Area', 'Vellore', 12.9500000, 79.1500000);

  SELECT * FROM locations;

  UPDATE complaints
  SET status = 'IN_PROGRESS'
  WHERE complaint_id = 2;

  DELETE FROM locations
  WHERE area_name = 'Sample Area';

```

* **Demonstrated Results:**
* The `INSERT` statement successfully added a new location record with an auto-incremented ID (`location_id` 6).
* The `SELECT` query verified the presence of the inserted location alongside existing areas.
* The `UPDATE` query correctly modified the status of complaint ID 2 to `IN_PROGRESS`.
* The `DELETE` query successfully removed the temporary sample record, restoring the table state.



---

### 2. Complaint Details Using JOIN

* **Testing Objective:** Combine data across multiple relational tables to retrieve comprehensive complaint reports containing citizen details, categories, and geographic locations.
* **SQL Executed:**
```sql
SELECT c.complaint_id, c.description, u.name AS citizen,
       cc.category_name, l.area_name, c.status,
       'Ashad_24BCE0431' AS submitted_by
FROM complaints c
JOIN users u ON c.user_id = u.user_id
JOIN complaint_categories cc ON c.category_id = cc.category_id
JOIN locations l ON c.location_id = l.location_id;

```


* **Demonstrated Results:** The inner join successfully bridged `complaints`, `users`, `complaint_categories`, and `locations`, outputting structured rows containing citizen names, issue categories, area descriptions, status values, and the student identifier column (`Ashad_24BCE0431`).

---

### 3. Officer Assignment JOIN

* **Testing Objective:** Validate the mapping between complaints, assigned water officers, and user profile names through multi-table joins.
* **SQL Executed:**
```sql
SELECT c.complaint_id, c.description, ou.name AS officer,
       ca.assigned_at, 'Ashad_24BCE0431' AS checked_by
FROM complaint_assignments ca
JOIN complaints c ON ca.complaint_id = c.complaint_id
JOIN officers o ON ca.officer_id = o.officer_id
JOIN users ou ON o.user_id = ou.user_id;

```


* **Demonstrated Results:** Successfully linked `complaint_assignments` with `officers`, `users`, and `complaints` to display exact officer names, complaint descriptions, and timestamps.

---

### 4. Area-Wise Aggregate Query

* **Testing Objective:** Calculate area-wise complaint metrics including total complaint volume, resolved counts, and average resolution duration using aggregate functions.
* **SQL Executed:**
```sql
SELECT l.area_name,
       COUNT(*) AS ashad_24BCE0431_total_complaints,
       SUM(c.status = 'RESOLVED') AS resolved,
       AVG(TIMESTAMPDIFF(MINUTE, c.created_at, c.resolved_at))
           AS avg_minutes_to_resolve
FROM complaints c
JOIN locations l ON c.location_id = l.location_id
GROUP BY l.area_name
HAVING COUNT(*) >= 1;

```


* **Demonstrated Results:** Grouped records by `area_name` accurately showing total complaints, resolved counts, and computed average resolution times in minutes per locality.

---

### 5. Dashboard View Tests

* **Testing Objective:** Verify data extraction through database views (`vw_water_dashboard` and `vw_complaint_dashboard`) designed for administrative panels.
* **SQL Executed:**
```sql
SELECT vw.*, 'Ashad_24BCE0431' AS checked_by
FROM vw_water_dashboard vw
WHERE risk_level = 'CRITICAL';

SELECT vw.*, 'Ashad_24BCE0431' AS checked_by
FROM vw_complaint_dashboard vw
WHERE status = 'PENDING';

```


* **Demonstrated Results:** Filtered views properly isolated critical water-quality readings and pending citizen complaints.

---

### 6. Water-Quality Trigger and Alert Test

* **Testing Objective:** Demonstrate that inserting a critical water-quality reading automatically fires database triggers to log risk levels and generate corresponding water alerts.
* **SQL Executed:**
```sql
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

```


* **Demonstrated Results:** Inserting abnormal parameters (`pH 5.5`, `TDS 1200`) triggered an automated evaluation, marking the reading `CRITICAL` and instantly populating a corresponding entry in the `water_alerts` table.

---

### 7. COMMIT and ROLLBACK Tests

* **Testing Objective:** Validate transaction control blocks, confirming successful commits for data modifications and proper rollbacks for aborted states.
* **SQL Executed:**
```sql
-- COMMIT Test
START TRANSACTION;
INSERT INTO complaint_assignments (complaint_id, officer_id)
VALUES (2, 1);
UPDATE complaints SET status = 'ASSIGNED' WHERE complaint_id = 2;
COMMIT;

-- ROLLBACK Test
START TRANSACTION;
UPDATE complaints SET status = 'REJECTED' WHERE complaint_id = 3;
ROLLBACK;

SELECT complaint_id, status, 'Ashad_24BCE0431' AS tested_by
FROM complaints
WHERE complaint_id = 3;

```


* **Demonstrated Results:** The commit transaction successfully persisted assignment records and status updates. The rollback test safely discarded the temporary rejection update on complaint ID 3, preserving its original `PENDING` status.

---

### 8. EXPLAIN Query

* **Testing Objective:** Inspect the query execution plan and index utilization for filtering operations on the `complaints` table.
* **SQL Executed:**
```sql
EXPLAIN
SELECT complaint_id, status, 'Ashad_24BCE0431' AS checked_by
FROM complaints
WHERE status = 'PENDING';

```


* **Demonstrated Results:** The execution plan verified that the query utilizes available index structures (`idx_complaints_status`), demonstrating optimized conditional filtering without requiring full table scans.

---

### 9. Stored Procedure Creation

* **Testing Objective:** Create a robust stored procedure (`sp_ashad_24BCE0431_assign_complaint`) equipped with exception handling and explicit transaction boundaries to handle complaint officer assignments safely.
* **SQL Executed:**
```sql
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

```


* **Demonstrated Results:** The procedure compiled successfully in MariaDB/phpMyAdmin, establishing a reusable routine containing atomic transaction controls.

---

### 10. Stored Procedure Execution and Verification

* **Testing Objective:** Execute the stored procedure and verify that database tables reflect the correct state updates.
* **SQL Executed:**
```sql
CALL sp_ashad_24BCE0431_assign_complaint(3, 1);

SELECT complaint_id, status
FROM complaints
WHERE complaint_id = 3;

SELECT *
FROM complaint_assignments
WHERE complaint_id = 3 AND officer_id = 1;

```


* **Demonstrated Results:** Execution successfully assigned officer 1 to complaint 3. Verification queries confirmed that the complaint status updated to `ASSIGNED` and a corresponding mapping record was correctly inserted into `complaint_assignments`.

---

## Conclusion

The database implementation for **AquaGuard** has been thoroughly tested across data manipulation routines, complex multi-table joins, administrative views, automated triggers, transaction safeguards, performance tuning (`EXPLAIN`), and custom stored procedures. All test cases executed successfully, verifying that the underlying schema provides reliable data integrity and performance for web application workflows.