<?php

Route::get('/index', function () {
    return view('index');
});

Route::match(array('GET', 'POST'),'/callback.php', function () {
    return view('callback');
});
