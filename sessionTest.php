<?php

session_start();

$_SESSION["foobar"] = "fooby";

var_dump($_SESSION);exit;