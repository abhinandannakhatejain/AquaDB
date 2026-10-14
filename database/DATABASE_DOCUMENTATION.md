# Database Documentation — AquaGuard

**Project Title:** AquaGuard — Water Quality Monitoring & Complaint Management System  
**Team Name:** AquaDB  
**Database Name:** `aquaguard` (MariaDB / MySQL)  

---

## 1. Database Overview

The `aquaguard` database serves as the centralized backend storage for the AquaGuard web application. It handles core relational entities required to connect water-quality monitoring, citizen complaint submissions, administrative oversight, and field officer responses. 

The schema consists of core operational tables, pre-configured dashboard views, automated triggers for critical threshold alerts, and a transaction-safe stored procedure implemented for managing officer complaint assignments.

---

## 2. Tables and Their Purposes

| Table Name | Purpose |
| :--- | :--- |
| **`users`** | Stores system user accounts, including citizens, officers, and administrators (managing names, emails, passwords, and roles). |
| **`locations`** | Maintains geographic monitoring points and area metadata (area names, cities, latitude, and longitude coordinates). |
| **`complaint_categories`** | Categorizes water-related issues (e.g., Dirty Water, Bad Smell, Unusual Color, Low Pressure). |
| **`complaints`** | Records citizen-submitted grievances, linking users, complaint categories, and target locations with status indicators. |
| **`officers`** | Holds specialized profile information for water-quality field officers mapped to system users. |
| **`complaint_assignments`** | Tracks mapping assignments connecting complaints to designated field officers and records assignment timestamps. |
| **`water_quality_readings`** | Stores periodic sensor or manual water-quality metrics (pH, turbidity, Total Dissolved Solids, temperature, and evaluated risk levels). |
| **`water_alerts`** | Automatically records critical alerts generated when water quality parameters breach safe thresholds. |
| **`feedback`** | Stores post-resolution feedback and satisfaction ratings provided by citizens. |
| **`area_statistics`** | Aggregates regional water monitoring and complaint metrics for rapid statistical reporting. |

---

## ## 3. Important Columns and Primary/Foreign-Key Relationships

PK = Primary Key; FK = Foreign Key.

- **`users`** — PK: `user_id`. Referenced by `complaints.user_id`, `officers.user_id`, and `feedback.user_id`.
- **`locations`** — PK: `location_id`. Referenced by `complaints.location_id`, `water_quality_readings.location_id`, `water_alerts.location_id`, and `area_statistics.location_id`.
- **`complaint_categories`** — PK: `category_id`. Referenced by `complaints.category_id`.
- **`complaints`** — PK: `complaint_id`; FKs: `user_id`, `category_id`, `location_id`. Referenced by `complaint_assignments.complaint_id` and `feedback.complaint_id`.
- **`officers`** — PK: `officer_id`; FK: `user_id` references `users.user_id`. The `user_id` column is UNIQUE.
- **`complaint_assignments`** — PK: `assignment_id`; FKs: `complaint_id` references `complaints.complaint_id`, and `officer_id` references `officers.officer_id`.
- **`feedback`** — PK: `feedback_id`; FKs: `complaint_id` references `complaints.complaint_id`, and `user_id` references `users.user_id`.
- **`water_quality_readings`** — PK: `reading_id`; FK: `location_id` references `locations.location_id`.
- **`water_alerts`** — PK: `alert_id`; FKs: `location_id` references `locations.location_id`, and `reading_id` references `water_quality_readings.reading_id`.
- **`area_statistics`** — PK: `statistics_id`; FK: `location_id` references `locations.location_id`. The `location_id` column is UNIQUE, allowing at most one statistics row per location.

## 4. Database Views 

The database includes pre-defined views to streamline dashboard data retrieval:

* **`vw_water_dashboard`**
  * **Purpose:** Aggregates location and water-quality reading metrics to display real-time parameter status and risk levels for administrative monitoring.
* **`vw_complaint_dashboard`**
  * **Purpose:** Combines complaint details, citizen identities, categories, and geographic areas into a unified stream for quick tracking and status filtering.

---

## 5. Triggers and What They Do

* **Water-Quality Processing Triggers**
  * **Purpose:** Automatically evaluate incoming records in `water_quality_readings` against established safe thresholds (such as pH levels and TDS limits). When a critical anomaly is detected, the trigger dynamically assigns a `CRITICAL` risk level to the reading and automatically inserts a corresponding warning record into the `water_alerts` table.

---

## 6. Stored Procedure (Added for SQL Assignment)

To fulfill advanced database requirements, a transaction-safe stored procedure was added separately during the lab exercises:

* **`sp_ashad_24BCE0431_assign_complaint(IN p_complaint_id INT, IN p_officer_id INT)`**
  * **What it does:** Encapsulates the logic for assigning an officer to a specific complaint. It opens an explicit transaction, inserts a mapping record into `complaint_assignments`, updates the corresponding complaint status to `ASSIGNED` in the `complaints` table, and commits the transaction safely. It includes an embedded `SQLEXCEPTION` error handler that triggers an automatic rollback if any execution failure occurs.

---

## 7. How the Tables Support the Application

The schema is directly aligned with AquaGuard's application features:
* **Citizen Management & Complaints:** The `users`, `complaint_categories`, and `complaints` tables allow citizens to securely authenticate, submit structured issues, and monitor progress.
* **Administrative Oversight:** Views (`vw_complaint_dashboard`, `vw_water_dashboard`) and aggregate statistics tables provide administrators with clear operational overviews.
* **Field Operations:** The `officers` and `complaint_assignments` tables ensure structured workflows for dispatching field personnel.
* **Water Safety Monitoring:** The `water_quality_readings` and `water_alerts` tables maintain historical and real-time safety metrics, ensuring automated alerts are raised when environmental thresholds are breached.
