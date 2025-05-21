<?php
require_once 'config/databases.php';
session_destroy();
redirect('login.php');
