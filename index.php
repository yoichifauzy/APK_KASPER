<?php
// Redirect root to landing page
// Use a PHP redirect so both browser and bots are forwarded immediately.
// This file should be placed at the workspace root so visiting
// http://localhost/APK_KAS/ goes to landingpage.php
header('Location: landingpage.php', true, 302);
exit;
