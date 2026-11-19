<?php
require 'config.php';
logAkt('Logout');
session_destroy();
header('Location: login.php');
