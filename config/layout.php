<?php

/*
|--------------------------------------------------------------------------
| Layout
|--------------------------------------------------------------------------
|
| Controls the public site layout: active header/footer templates,
| body tag classes, and global PHP code that runs on every public page.
| Edit these settings via the dashboard at /dashboard/templates.
|
| Keys:
|   active_header  - template name e.g. 'header-simple', or null for built-in
|   active_footer  - template name e.g. 'footer-simple', or null for built-in
|   body_classes   - additional classes appended to the <body> tag
|   php_top        - PHP code eval()'d before the DOCTYPE on every public page
|
*/

return array (
  'active_header' => 'header-simple',
  'active_footer' => 'footer-light',
  'body_classes' => '',
  'php_top' => '',
);