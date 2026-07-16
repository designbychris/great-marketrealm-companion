<?php
return View::render(
    'dashboard.index',
    [
        'characters' => $characters,
        'user' => wp_get_current_user(),
    ]
);
