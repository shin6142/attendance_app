DROP TABLE IF EXISTS `attendance`;

CREATE TABLE `attendance`
(
 `id`               int AUTO_INCREMENT PRIMARY KEY,
 `employee_id`     int(10) NOT NULL,
 `company_id`      int(10) NOT NULL,
 `type`       varchar(20) NOT NULL,
 `base_date`       varchar(20) NOT NULL,
 `datetime`   DATETIME NOT NULL,
 `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;