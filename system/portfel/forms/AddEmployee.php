<?php

    require "../dataClasses/Employee.php";
    
    $employees = Employee::getEmployees();
    $employeeId = $employees[count($transactions)-1]->getId()+1;
    $employee = new Employee((int) $employeeId, $_POST["name"], $_POST["surname"],
        $_POST["description"], (int) $_POST["reputation"], (float) $_POST["hours"], (float) $_POST["salary"]);

    $employee->save();

    header("location: ../");