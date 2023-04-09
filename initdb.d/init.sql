DROP TABLE IF EXISTS `attendance`;
DROP TABLE IF EXISTS `token`;

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

CREATE TABLE `token` (
  `access_token` varchar(255) NOT NULL,
  `token_type` varchar(255) NOT NULL,
  `expires_in` varchar(20) NOT NULL,
  `refresh_token` varchar(255) NOT NULL,
  `scope` varchar(255) NOT NULL,
  `issued_unix_datetime` int(10) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;