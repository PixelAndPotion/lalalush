<?php
// This file contains the reusable admin sidebar navigation
// It is included on all admin pages to keep the layout consistent
// The sidebar gives admins quick access to dashboard features

?>

<!-- Main admin layout wrapper -->
<div class="admin-layout">

    <!-- Sidebar section -->
    <!-- Contains logo and navigation links -->
    <div class="admin-sidebar">

        <!-- Admin panel logo/title -->
        <div class="admin-logo">

            <!-- Website name -->
            La La Lush 🌸

            <br>

            <!-- Small subtitle under the logo -->
            <small style="font-size:0.7rem; font-weight:400; color:#f8bbd0;">

                Admin Panel

            </small>

        </div>

        <!-- Navigation menu -->
        <nav>

            <!-- Link to admin dashboard homepage -->
            <a href="index.php" class="admin-nav-link">

                 Dashboard

            </a>

            <!-- Link to manage customer orders -->
            <a href="orders.php" class="admin-nav-link">

                 Orders

            </a>

            <!-- Link to manage products -->
            <a href="products.php" class="admin-nav-link">

                 Products

            </a>

            <!-- Link to manage users -->
            <a href="users.php" class="admin-nav-link">

                 Users

            </a>

            <!-- Link back to the main customer website -->
            <a href="../index.php" class="admin-nav-link">

                 Main Site

            </a>

            <!-- Logout link -->
            <!-- margin-top:auto pushes logout to bottom of sidebar -->
            <a href="../logout.php"
               class="admin-nav-link"
               style="margin-top:auto; color:#f48fb1;">

                 Logout

            </a>

        </nav>

    </div>