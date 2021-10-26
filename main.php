<?php

require_once __DIR__ . '/api_functions.php';

$employees = getEmployees();

$employees = getValidEmployees($employees);

$employees = getCurrentWorkingEmployees($employees);

$employees = getEmployeesWithConfiguredBirthday($employees);

processBirthday($employees);

?>




             




