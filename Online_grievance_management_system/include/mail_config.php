<?php
define('SMTP_HOST', getenv('GMS_SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_USERNAME', getenv('GMS_SMTP_USERNAME') ?: 'khushiangadi712004@gmail.com');
define('SMTP_PASSWORD', getenv('GMS_SMTP_PASSWORD') ?: 'ayve bdsf tseb gipz');
define('SMTP_PORT', (int)(getenv('GMS_SMTP_PORT') ?: 587));
define('SMTP_SECURE', getenv('GMS_SMTP_SECURE') ?: 'tls');
define('SMTP_FROM_EMAIL', getenv('GMS_SMTP_FROM_EMAIL') ?: 'khushiangadi712004@gmail.com');
define('SMTP_FROM_NAME', getenv('GMS_SMTP_FROM_NAME') ?: 'Grievance Management System');
?>
