<?php

require_once "includes/auth.php";

$user = $_SESSION['user'];

include "includes/header.php";
include "includes/navbar.php";
include "includes/sidebar.php";
?>

<div class="content-wrapper">

<section class="content-header">
    <h1>Dashboard</h1>
    </section>

    <section class="content">

    <div class="row">

    <div class="col-lg-3 col-6">

    <div class="small-box bg-primary">

    <div class="inner">
    <h3>1258</h3>
    <p>Citizens</p>
    </div>

    <div class="icon">
    <i class="fas fa-users"></i>
    </div>

    <a href="modules/citizens/" class="small-box-footer">
    Manage <i class="fas fa-arrow-circle-right"></i>
    </a>

    </div>

    </div>

    <div class="col-lg-3 col-6">

    <div class="small-box bg-success">

    <div class="inner">
    <h3>235</h3>
    <p>Registers</p>
    </div>

    <div class="icon">
    <i class="fas fa-book"></i>
    </div>

    <a href="modules/registers/" class="small-box-footer">
    Open
    </a>

    </div>

    </div>

    <div class="col-lg-3 col-6">

    <div class="small-box bg-warning">

    <div class="inner">
    <h3>68</h3>
    <p>Certificates</p>
    </div>

    <div class="icon">
    <i class="fas fa-file-pdf"></i>
    </div>

    <a href="#" class="small-box-footer">
    Generate
    </a>

    </div>

    </div>

    <div class="col-lg-3 col-6">

    <div class="small-box bg-danger">

    <div class="inner">
    <h3>4</h3>
    <p>Users</p>
    </div>

    <div class="icon">
    <i class="fas fa-user-shield"></i>
    </div>

    <a href="modules/users/" class="small-box-footer">
    Manage
    </a>

    </div>

    </div>

    </div>

    </section>

    </div>

    <?php
    include "includes/footer.php";
    ?>